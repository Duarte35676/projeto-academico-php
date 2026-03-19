<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['gestor']);

$alunos = $pdo->query('SELECT u.id, u.numero_utilizador, u.nome, c.nome AS curso_nome FROM users u LEFT JOIN cursos c ON u.curso_id = c.id WHERE u.role = "aluno" ORDER BY u.id DESC')->fetchAll();
$pedidos = $pdo->query('SELECT p.*, u.nome AS aluno_nome FROM pedidos p INNER JOIN users u ON p.aluno_id = u.id ORDER BY p.id DESC')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<h2 class="mb-3">Painel do Gestor</h2>
<div class="mb-3">
    <a href="cursos.php" class="btn btn-primary">Gerir cursos</a>
    <a href="ucs.php" class="btn btn-primary">Gerir UCs</a>
    <a href="pedidos.php" class="btn btn-warning">Gerir pedidos</a>
    <a href="../funcionario/dashboard.php" class="btn btn-success">Ver pautas</a>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Alunos matriculados</h5>
                <table class="table table-bordered">
                    <tr><th>Número</th><th>Nome</th><th>Curso</th></tr>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><?= e($aluno['numero_utilizador']) ?></td>
                            <td><?= e($aluno['nome']) ?></td>
                            <td><?= e($aluno['curso_nome'] ?? 'Sem curso') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <h5>Pedidos recentes</h5>
                <table class="table table-bordered">
                    <tr><th>Aluno</th><th>Tipo</th><th>Estado</th></tr>
                    <?php foreach (array_slice($pedidos, 0, 8) as $pedido): ?>
                        <tr>
                            <td><?= e($pedido['aluno_nome']) ?></td>
                            <td><?= e($pedido['tipo_pedido']) ?></td>
                            <td><?= e($pedido['estado']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
