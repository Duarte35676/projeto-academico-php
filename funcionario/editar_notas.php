<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['funcionario', 'gestor']);

$pautaId = (int) ($_GET['id'] ?? 0);
if ($pautaId <= 0) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nota'])) {
    $update = $pdo->prepare('UPDATE pauta_notas SET nota_final = ? WHERE id = ?');
    foreach ($_POST['nota'] as $notaId => $valor) {
        $valor = trim($valor);
        $nota = ($valor === '') ? null : (float) $valor;
        $update->execute([$nota, $notaId]);
    }
    flashMessage('success', 'Notas atualizadas com sucesso.');
    header('Location: editar_notas.php?id=' . $pautaId);
    exit;
}

$stmt = $pdo->prepare('SELECT p.*, c.nome AS curso_nome, u.nome AS uc_nome FROM pautas p
INNER JOIN cursos c ON p.curso_id = c.id
INNER JOIN ucs u ON p.uc_id = u.id
WHERE p.id = ?');
$stmt->execute([$pautaId]);
$pauta = $stmt->fetch();

$notas = $pdo->prepare('SELECT pn.id, pn.nota_final, us.nome, us.numero_utilizador
FROM pauta_notas pn
INNER JOIN users us ON pn.aluno_id = us.id
WHERE pn.pauta_id = ? ORDER BY us.nome');
$notas->execute([$pautaId]);
$notas = $notas->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h2>Lançar notas - <?= e($pauta['uc_nome']) ?> (<?= e($pauta['epoca']) ?>)</h2>
<div class="card">
    <div class="card-body">
        <form method="POST">
            <table class="table table-bordered">
                <tr><th>Número</th><th>Aluno</th><th>Nota final</th></tr>
                <?php foreach ($notas as $nota): ?>
                    <tr>
                        <td><?= e($nota['numero_utilizador']) ?></td>
                        <td><?= e($nota['nome']) ?></td>
                        <td><input type="number" step="0.1" min="0" max="20" class="form-control" name="nota[<?= $nota['id'] ?>]" value="<?= e((string) $nota['nota_final']) ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button class="btn btn-success">Guardar notas</button>
            <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
