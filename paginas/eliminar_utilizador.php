<?php
include("../basededados/basedados.h");
session_start();

// Verificar se o utilizador está autenticado e é administrador
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil']) || $_SESSION['idPerfil'] != 4) {
    header("Location: login.php");
    exit();
}

$idUser = $_GET['id'] ?? null;

if (!$idUser) {
    header("Location: gerir_utilizadores.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $stmt = mysqli_prepare($ligacao, "UPDATE utilizador SET ativo=0 WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $idUser);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Utilizador inativado com sucesso.";
            mysqli_stmt_close($stmt);
            header("Location: gerir_utilizadores.php");
            exit();
        } else {
            $error = "Erro ao inativar utilizador: " . mysqli_error($ligacao);
            mysqli_stmt_close($stmt);
        }
    } else {
        header("Location: gerir_utilizadores.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Utilizador - FelixBus</title>
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
    <h1>Eliminar Utilizador</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php else: ?>
        <p>Tem a certeza que deseja eliminar este utilizador?</p>
        <form method="post" action="eliminar_utilizador.php?id=<?= htmlspecialchars($idUser) ?>">
            <button type="submit" name="confirm" value="yes" class="btn btn-danger">Sim, eliminar</button>
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
