<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['gestor']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $estado = $_POST['estado'] ?? '';
    $observacoes = trim($_POST['observacoes'] ?? '');

    if ($id > 0 && in_array($estado, ['aprovado', 'rejeitado'], true)) {
        $stmt = $pdo->prepare('UPDATE pedidos SET estado = ?, observacoes = ?, decidido_por = ?, data_decisao = NOW() WHERE id = ?');
        $stmt->execute([$estado, $observacoes, $_SESSION['user']['id'], $id]);

        $pedidoStmt = $pdo->prepare('SELECT * FROM pedidos WHERE id = ?');
        $pedidoStmt->execute([$id]);
        $pedido = $pedidoStmt->fetch();

        if ($pedido && $estado === 'aprovado' && $pedido['tipo_pedido'] === 'mudanca_curso' && !empty($pedido['curso_destino_id'])) {
            $updateAluno = $pdo->prepare('UPDATE users SET curso_id = ? WHERE id = ?');
            $updateAluno->execute([$pedido['curso_destino_id'], $pedido['aluno_id']]);
        }

        flashMessage('success', 'Pedido atualizado com sucesso.');
    }

    header('Location: pedidos.php');
    exit;
}

$pedidos = $pdo->query('SELECT p.*, u.nome AS aluno_nome, u.numero_utilizador, c1.nome AS origem, c2.nome AS destino, g.nome AS gestor_nome
FROM pedidos p
INNER JOIN users u ON p.aluno_id = u.id
LEFT JOIN cursos c1 ON p.curso_origem_id = c1.id
LEFT JOIN cursos c2 ON p.curso_destino_id = c2.id
LEFT JOIN users g ON p.decidido_por = g.id
ORDER BY p.id DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Gerir pedidos</h2>
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Aluno</th><th>Tipo</th><th>Descrição</th><th>Estado</th><th>Curso destino</th><th>Decisão</th></tr>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?= e($pedido['numero_utilizador']) ?> - <?= e($pedido['aluno_nome']) ?></td>
                    <td><?= e($pedido['tipo_pedido']) ?></td>
                    <td><?= e($pedido['descricao']) ?></td>
                    <td><?= e($pedido['estado']) ?></td>
                    <td><?= e($pedido['destino'] ?? '') ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                            <textarea name="observacoes" class="form-control mb-2" rows="2" placeholder="Observações"><?= e($pedido['observacoes'] ?? '') ?></textarea>
                            <button name="estado" value="aprovado" class="btn btn-sm btn-success">Aprovar</button>
                            <button name="estado" value="rejeitado" class="btn btn-sm btn-danger">Rejeitar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">Voltar</a>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
