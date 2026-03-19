<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['gestor']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($acao === 'criar' && $nome !== '' && $codigo !== '') {
        $stmt = $pdo->prepare('INSERT INTO ucs (nome, codigo, ativo) VALUES (?, ?, 1)');
        $stmt->execute([$nome, $codigo]);
    }

    if ($acao === 'editar' && $id > 0 && $nome !== '' && $codigo !== '') {
        $stmt = $pdo->prepare('UPDATE ucs SET nome = ?, codigo = ? WHERE id = ?');
        $stmt->execute([$nome, $codigo, $id]);
    }

    if ($acao === 'desativar' && $id > 0) {
        $stmt = $pdo->prepare('UPDATE ucs SET ativo = IF(ativo = 1, 0, 1) WHERE id = ?');
        $stmt->execute([$id]);
    }

    flashMessage('success', 'Operação executada com sucesso.');
    header('Location: ucs.php');
    exit;
}

$ucs = $pdo->query('SELECT * FROM ucs ORDER BY id DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Gerir unidades curriculares</h2>
<div class="row">
    <div class="col-md-4">
        <div class="card mb-3"><div class="card-body">
            <form method="POST">
                <input type="hidden" name="acao" value="criar">
                <label class="form-label">Nova UC</label>
                <input type="text" name="nome" class="form-control mb-2" placeholder="Nome" required>
                <input type="text" name="codigo" class="form-control mb-2" placeholder="Código" required>
                <button class="btn btn-primary">Criar</button>
            </form>
        </div></div>
    </div>
    <div class="col-md-8">
        <div class="card"><div class="card-body">
            <table class="table table-bordered">
                <tr><th>ID</th><th>Nome</th><th>Código</th><th>Ativo</th><th>Ações</th></tr>
                <?php foreach ($ucs as $uc): ?>
                    <tr>
                        <td><?= $uc['id'] ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="acao" value="editar">
                                <input type="hidden" name="id" value="<?= $uc['id'] ?>">
                                <input type="text" name="nome" class="form-control" value="<?= e($uc['nome']) ?>">
                        </td>
                        <td>
                                <input type="text" name="codigo" class="form-control" value="<?= e($uc['codigo']) ?>">
                        </td>
                        <td><?= $uc['ativo'] ? 'Sim' : 'Não' ?></td>
                        <td>
                                <button class="btn btn-sm btn-warning">Editar</button>
                            </form>
                            <form method="POST" class="mt-1">
                                <input type="hidden" name="acao" value="desativar">
                                <input type="hidden" name="id" value="<?= $uc['id'] ?>">
                                <button class="btn btn-sm btn-secondary"><?= $uc['ativo'] ? 'Desativar' : 'Ativar' ?></button>
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
