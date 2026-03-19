<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Académico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/academico_simples/assets/css/style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/academico_simples/index.php">Sistema Académico</a>
        <div class="d-flex">
            <?php if (isLoggedIn()): ?>
                <span class="navbar-text text-white me-3">
                    <?= e($_SESSION['user']['nome']) ?> (<?= e($_SESSION['user']['role']) ?>)
                </span>
                <a class="btn btn-outline-light btn-sm" href="/academico_simples/logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">
<?php $flash = flashMessage(); ?>
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
