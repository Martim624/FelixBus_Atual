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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $idPerfil = $_POST['idPerfil'] ?? '';
    $validado = isset($_POST['validado']) ? 1 : 0;

    if ($username === '') $errors[] = "Nome de utilizador é obrigatório.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (!in_array($idPerfil, ['1','2','3','4'])) $errors[] = "Perfil inválido.";

    if (empty($errors)) {
        $stmt = mysqli_prepare($ligacao, "SELECT id FROM utilizador WHERE username=? OR email=?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Nome de utilizador ou email já existe.";
        }
        mysqli_stmt_close($stmt);

        if (empty($errors)) {
            $stmt = mysqli_prepare($ligacao, "INSERT INTO utilizador (username, email, idPerfil, validado) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssii", $username, $email, $idPerfil, $validado);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Utilizador adicionado com sucesso.";
            } else {
                $errors[] = "Erro ao adicionar utilizador: " . mysqli_error($ligacao);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Utilizador - FelixBus</title>
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
    <h1>Adicionar Novo Utilizador</h1>

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
        <form method="post" action="adicionar_utilizador.php">
            <label for="username">Nome de Utilizador:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="idPerfil">Perfil:</label>
            <select id="idPerfil" name="idPerfil" required>
                <option value="1">Visitante</option>
                <option value="2">Cliente</option>
                <option value="3">Funcionário</option>
                <option value="4">Administrador</option>
            </select>

            <label for="validado">Validado:</label>
            <input type="checkbox" id="validado" name="validado" value="1">

            <button type="submit" class="btn btn-primary">Adicionar Utilizador</button>
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
