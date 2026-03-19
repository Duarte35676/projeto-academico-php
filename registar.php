<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $dataNascimento = $_POST['data_nascimento'] ?? '';
    $cursoId = (int) ($_POST['curso_id'] ?? 0);
    $password = $_POST['password'] ?? '';

    if ($nome === '' || $dataNascimento === '' || $cursoId <= 0 || $password === '') {
        flashMessage('danger', 'Preencha todos os campos obrigatórios.');
        header('Location: registar.php');
        exit;
    }

    $foto = saveUpload($_FILES['foto']);
    $numero = generateStudentNumber($pdo);
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (numero_utilizador, nome, data_nascimento, foto, password, role, curso_id, ativo) VALUES (?, ?, ?, ?, ?, "aluno", ?, 1)');
    $stmt->execute([$numero, $nome, $dataNascimento, $foto, $hash, $cursoId]);

    flashMessage('success', 'Perfil criado com sucesso. O teu número de aluno é: ' . $numero);
    header('Location: index.php');
    exit;
}

$cursos = $pdo->query('SELECT * FROM cursos WHERE ativo = 1 ORDER BY nome')->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <h2 class="mb-3">Criar perfil de aluno</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data de nascimento</label>
                        <input type="date" name="data_nascimento" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Curso pretendido</label>
                        <select name="curso_id" class="form-select" required>
                            <option value="">Selecione</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= $curso['id'] ?>"><?= e($curso['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fotografia (JPG/PNG, máx. 2MB)</label>
                        <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Palavra-passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-success">Registar</button>
                    <a href="index.php" class="btn btn-secondary">Voltar</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
