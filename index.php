<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirectByRole($_SESSION['user']['role']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        flashMessage('danger', 'Preencha o login e a palavra-passe.');
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE numero_utilizador = ? AND ativo = 1 LIMIT 1');
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            redirectByRole($user['role']);
        } else {
            flashMessage('danger', 'Credenciais inválidas.');
        }
    }

    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-3">Login</h2>
                <p class="text-muted">Alunos, funcionários e gestores entram com o número de utilizador e palavra-passe.</p>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Número de utilizador</label>
                        <input type="text" name="login" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Palavra-passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Entrar</button>
                </form>
                <hr>
                <a href="registar.php" class="btn btn-success w-100">Criar perfil de aluno</a>
                <div class="mt-3 small text-muted">
                    <strong>Utilizadores de teste:</strong><br>
                    Gestor: <code>gestor1</code> / <code>admin123</code><br>
                    Funcionário: <code>func1</code> / <code>func123</code>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
