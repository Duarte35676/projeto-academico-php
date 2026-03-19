<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /academico_simples/index.php');
        exit;
    }
}

function requireRole(array $roles): void {
    requireLogin();
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        header('Location: /academico_simples/acesso_negado.php');
        exit;
    }
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirectByRole(string $role): void {
    switch ($role) {
        case 'gestor':
            header('Location: /academico_simples/gestor/dashboard.php');
            break;
        case 'funcionario':
            header('Location: /academico_simples/funcionario/dashboard.php');
            break;
        default:
            header('Location: /academico_simples/aluno/dashboard.php');
            break;
    }
    exit;
}

function generateStudentNumber(PDO $pdo): string {
    $year = date('Y');
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'aluno' AND numero_utilizador LIKE ?");
    $stmt->execute([$year . '%']);
    $count = (int) $stmt->fetch()['total'] + 1;
    return $year . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
}

function saveUpload(array $file): ?string {
    if (!isset($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    $maxSize = 2 * 1024 * 1024;

    if (!isset($allowed[$file['type']]) || $file['size'] > $maxSize) {
        return null;
    }

    $extension = $allowed[$file['type']];
    $newName = uniqid('foto_', true) . '.' . $extension;
    $destination = __DIR__ . '/../uploads/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $newName;
    }

    return null;
}

function flashMessage(?string $type = null, ?string $message = null): ?array {
    if ($type !== null && $message !== null) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        return null;
    }

    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    return null;
}
