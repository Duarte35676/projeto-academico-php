<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['gestor']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($acao === 'criar' && $nome !== '') {
        $stmt = $pdo->prepare('INSERT INTO cursos (nome, ativo) VALUES (?, 1)');
        $stmt->execute([$nome]);
    }

    if ($acao === 'editar' && $id > 0 && $nome !== '') {
        $stmt = $pdo->prepare('UPDATE cursos SET nome = ? WHERE id = ?');
        $stmt->execute([$nome, $id]);
    }

    if ($acao === 'desativar' && $id > 0) {
        $stmt = $pdo->prepare('UPDATE cursos SET ativo = IF(ativo = 1, 0, 1) WHERE id = ?');
        $stmt->execute([$id]);
    }

    flashMessage('success', 'Operação executada com sucesso.');
    header('Location: cursos.php');
    exit;
}

$cursos = $pdo->query('SELECT * FROM cursos ORDER BY id DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Gerir cursos</h2>
<div class="row">
    <div class="col-md-4">
        <div class="card mb-3"><div class="card-body">
            <form method="POST">
                <input type="hidden" name="acao" value="criar">
                <label class="form-label">Novo curso</label>
                <input type="text" name="nome" class="form-control mb-2" required>
                <button class="btn btn-primary">Criar</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card"><div class="card-body">
            <table class="table table-bordered">
                <tr><th>ID</th><th>Nome</th><th>Ativo</th><th>Ações</th></tr>
                <?php foreach ($cursos as $curso): ?>
                    <tr>
                        <td><?= $curso['id'] ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="acao" value="editar">
                                <input type="hidden" name="id" value="<?= $curso['id'] ?>">
                                <input type="text" name="nome" class="form-control" value="<?= e($curso['nome']) ?>">
                                <button class="btn btn-sm btn-warning">Editar</button>
                            </form>
                        </td>
                        <td><?= $curso['ativo'] ? 'Sim' : 'Não' ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="acao" value="desativar">
                                <input type="hidden" name="id" value="<?= $curso['id'] ?>">
                                <button class="btn btn-sm btn-secondary"><?= $curso['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
        </div></div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
