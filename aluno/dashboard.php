<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['aluno']);

$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT u.*, c.nome AS curso_nome FROM users u LEFT JOIN cursos c ON u.curso_id = c.id WHERE u.id = ?');
$stmt->execute([$userId]);
$aluno = $stmt->fetch();

$pedidos = $pdo->prepare('SELECT p.*, c1.nome AS origem, c2.nome AS destino FROM pedidos p
LEFT JOIN cursos c1 ON p.curso_origem_id = c1.id
LEFT JOIN cursos c2 ON p.curso_destino_id = c2.id
WHERE p.aluno_id = ? ORDER BY p.id DESC');
$pedidos->execute([$userId]);
$pedidos = $pedidos->fetchAll();

$notas = $pdo->prepare('SELECT pn.nota_final, p.ano_letivo, p.epoca, u.nome AS uc_nome
FROM pauta_notas pn
INNER JOIN pautas p ON pn.pauta_id = p.id
INNER JOIN ucs u ON p.uc_id = u.id
WHERE pn.aluno_id = ? ORDER BY p.id DESC');
$notas->execute([$userId]);
$notas = $notas->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Área do Aluno</h2>
    <a class="btn btn-primary" href="pedido_novo.php">Novo pedido</a>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Perfil</h5>
                <p><strong>Número:</strong> <?= e($aluno['numero_utilizador']) ?></p>
                <p><strong>Nome:</strong> <?= e($aluno['nome']) ?></p>
                <p><strong>Curso:</strong> <?= e($aluno['curso_nome'] ?? 'Sem curso') ?></p>
                <?php if (!empty($aluno['foto'])): ?>
                    <img src="/academico_simples/uploads/<?= e($aluno['foto']) ?>" class="img-fluid rounded" alt="Foto do aluno">
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Os meus pedidos</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr><th>Tipo</th><th>Descrição</th><th>Estado</th><th>Obs.</th></tr>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?= e($pedido['tipo_pedido']) ?></td>
                                <td><?= e($pedido['descricao']) ?></td>
                                <td><?= e($pedido['estado']) ?></td>
                                <td><?= e($pedido['observacoes'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$pedidos): ?>
                            <tr><td colspan="4">Sem pedidos.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5>Notas</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr><th>UC</th><th>Ano letivo</th><th>Época</th><th>Nota</th></tr>
                        <?php foreach ($notas as $nota): ?>
                            <tr>
                                <td><?= e($nota['uc_nome']) ?></td>
                                <td><?= e($nota['ano_letivo']) ?></td>
                                <td><?= e($nota['epoca']) ?></td>
                                <td><?= e((string) $nota['nota_final']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$notas): ?>
                            <tr><td colspan="4">Sem notas lançadas.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
