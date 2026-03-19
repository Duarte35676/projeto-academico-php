<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['aluno']);

$cursos = $pdo->query('SELECT * FROM cursos WHERE ativo = 1 ORDER BY nome')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo_pedido'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');
    $cursoOrigem = $_SESSION['user']['curso_id'] ?: null;
    $cursoDestino = !empty($_POST['curso_destino_id']) ? (int) $_POST['curso_destino_id'] : null;

    if ($tipo === '') {
        flashMessage('danger', 'Selecione um tipo de pedido.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO pedidos (aluno_id, tipo_pedido, descricao, curso_origem_id, curso_destino_id, estado, data_pedido) VALUES (?, ?, ?, ?, ?, "pendente", NOW())');
        $stmt->execute([$_SESSION['user']['id'], $tipo, $descricao, $cursoOrigem, $cursoDestino]);
        flashMessage('success', 'Pedido criado com sucesso.');
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
                <h2>Novo pedido</h2>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tipo de pedido</label>
                        <select name="tipo_pedido" class="form-select" required>
                            <option value="">Selecione</option>
                            <option value="matricula">Matrícula/Inscrição</option>
                            <option value="mudanca_curso">Mudança de curso</option>
                            <option value="certificado">Certificado de habilitações</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Curso de destino (só para mudança de curso)</label>
                        <select name="curso_destino_id" class="form-select">
                            <option value="">Não aplicável</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= $curso['id'] ?>"><?= e($curso['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn-primary">Submeter pedido</button>
                    <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
