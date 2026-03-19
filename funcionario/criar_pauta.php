<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['funcionario', 'gestor']);

$cursos = $pdo->query('SELECT * FROM cursos WHERE ativo = 1 ORDER BY nome')->fetchAll();
$ucs = $pdo->query('SELECT * FROM ucs WHERE ativo = 1 ORDER BY nome')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cursoId = (int) ($_POST['curso_id'] ?? 0);
    $ucId = (int) ($_POST['uc_id'] ?? 0);
    $anoLetivo = trim($_POST['ano_letivo'] ?? '');
    $epoca = trim($_POST['epoca'] ?? '');

    if ($cursoId <= 0 || $ucId <= 0 || $anoLetivo === '' || $epoca === '') {
        flashMessage('danger', 'Preencha os campos obrigatórios.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO pautas (curso_id, uc_id, ano_letivo, epoca, criado_por, data_criacao) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$cursoId, $ucId, $anoLetivo, $epoca, $_SESSION['user']['id']]);
        $pautaId = (int) $pdo->lastInsertId();

        $alunos = $pdo->prepare('SELECT id FROM users WHERE role = "aluno" AND curso_id = ? AND ativo = 1');
        $alunos->execute([$cursoId]);
        $alunos = $alunos->fetchAll();

        $insertNota = $pdo->prepare('INSERT INTO pauta_notas (pauta_id, aluno_id, nota_final) VALUES (?, ?, NULL)');
        foreach ($alunos as $aluno) {
            $insertNota->execute([$pautaId, $aluno['id']]);
        }

        flashMessage('success', 'Pauta criada com sucesso.');
        header('Location: dashboard.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <h2>Criar pauta</h2>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Curso</label>
                        <select name="curso_id" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= $curso['id'] ?>"><?= e($curso['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">UC</label>
                        <select name="uc_id" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach ($ucs as $uc): ?>
                                <option value="<?= $uc['id'] ?>"><?= e($uc['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ano letivo</label>
                        <input type="text" name="ano_letivo" class="form-control" placeholder="2025/2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Época</label>
                        <select name="epoca" class="form-select" required>
                            <option value="Normal">Normal</option>
                            <option value="Recurso">Recurso</option>
                            <option value="Especial">Especial</option>
                        </select>
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                    <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
