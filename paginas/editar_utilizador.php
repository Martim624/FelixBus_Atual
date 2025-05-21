<?php
include("../basededados/basedados.h");
session_start();

// Check if user is logged in and is administrator
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil']) || $_SESSION['idPerfil'] != 4) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = '';
$idUser = $_GET['id'] ?? null;

if (!$idUser) {
    header("Location: gerir_utilizadores.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $idPerfil = $_POST['idPerfil'] ?? '';
    $validado = isset($_POST['validado']) ? 1 : 0;

    if ($username === '') $errors[] = "Nome de utilizador é obrigatório.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (!in_array($idPerfil, ['1','2','3','4'])) $errors[] = "Perfil inválido.";

    if (empty($errors)) {
        $stmt = mysqli_prepare($ligacao, "UPDATE utilizador SET username=?, email=?, idPerfil=?, validado=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssiis", $username, $email, $idPerfil, $validado, $idUser);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Utilizador atualizado com sucesso.";
        } else {
            $errors[] = "Erro ao atualizar utilizador: " . mysqli_error($ligacao);
        }
        mysqli_stmt_close($stmt);
    }
}

$stmt = mysqli_prepare($ligacao, "SELECT * FROM utilizador WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $idUser);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    header("Location: gerir_utilizadores.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Utilizador - FelixBus</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="body-secondary">
<nav>
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="gerir_utilizadores.php"><i class="fas fa-users"></i> Gerir Utilizadores</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Terminar Sessão</a>
    </div>
</nav>

<div class="container">
    <h1>Editar Utilizador</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $erro): ?>
                    <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <a href="gerir_utilizadores.php" class="btn btn-primary">Voltar à lista de utilizadores</a>
    <?php else: ?>
        <form method="post" action="editar_utilizador.php?id=<?= htmlspecialchars($idUser) ?>">
            <label for="username">Nome de Utilizador:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="idPerfil">Perfil:</label>
            <select id="idPerfil" name="idPerfil" required>
                <option value="1" <?= ($user['idPerfil'] == 1) ? 'selected' : '' ?>>Visitante</option>
                <option value="2" <?= ($user['idPerfil'] == 2) ? 'selected' : '' ?>>Cliente</option>
                <option value="3" <?= ($user['idPerfil'] == 3) ? 'selected' : '' ?>>Funcionário</option>
                <option value="4" <?= ($user['idPerfil'] == 4) ? 'selected' : '' ?>>Administrador</option>
            </select>

            <label for="validado">Validado:</label>
            <input type="checkbox" id="validado" name="validado" value="1" <?= ($user['validado']) ? 'checked' : '' ?>>

            <button type="submit" class="btn btn-primary">Atualizar Utilizador</button>
            <a href="gerir_utilizadores.php" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php endif; ?>
</div>

<footer class="footer-transparent">
    &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
