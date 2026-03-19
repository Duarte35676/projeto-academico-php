<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['funcionario', 'gestor']);

$pautas = $pdo->query('SELECT p.*, c.nome AS curso_nome, u.nome AS uc_nome, us.nome AS criador
FROM pautas p
INNER JOIN cursos c ON p.curso_id = c.id
INNER JOIN ucs u ON p.uc_id = u.id
INNER JOIN users us ON p.criado_por = us.id
ORDER BY p.id DESC')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Área de Pautas</h2>
    <a href="criar_pauta.php" class="btn btn-primary">Criar pauta</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr><th>ID</th><th>Curso</th><th>UC</th><th>Ano letivo</th><th>Época</th><th>Criado por</th><th>Ações</th></tr>
                <?php foreach ($pautas as $pauta): ?>
                    <tr>
                        <td><?= $pauta['id'] ?></td>
                        <td><?= e($pauta['curso_nome']) ?></td>
                        <td><?= e($pauta['uc_nome']) ?></td>
                        <td><?= e($pauta['ano_letivo']) ?></td>
                        <td><?= e($pauta['epoca']) ?></td>
                        <td><?= e($pauta['criador']) ?></td>
                        <td><a class="btn btn-sm btn-warning" href="editar_notas.php?id=<?= $pauta['id'] ?>">Lançar notas</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$pautas): ?>
                    <tr><td colspan="7">Sem pautas.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
