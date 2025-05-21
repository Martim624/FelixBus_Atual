<?php
session_start();
require_once("../basededados/basedados.h");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = hash('sha256', $_POST["password"]);

    $sql = "SELECT * FROM utilizador WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($ligacao, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($resultado)) {
        // Verifica se o utilizador está validado
        // Se o utilizador não estiver validado, exibe uma mensagem de erro
        // Se o utilizador estiver validado, define as variáveis de sessão
        if (!$user['validado'] || $user['validado'] == 0) {
            $erro = "A sua conta ainda não foi validada pelo administrador.";
        } elseif (isset($user['ativo']) && $user['ativo'] == 0) {
            $erro = "A sua conta está inativa. Contacte o administrador.";
        } else {
            // Definir as variáveis de sessão
            $_SESSION["id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["idPerfil"] = $user["idPerfil"];
            
            // Redireciona para a dashboard com base no perfil
            header("Location: dashboard.php");
            exit();
        }
    } else {
        $erro = "Credenciais inválidas.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>FelixBus - Login</title>
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="body-auth">

<!-- Barra de navegação fixa no topo -->
<nav>
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Login</a>
        <a href="registo.php"><i class="fas fa-user-plus"></i> Registo</a>
        <a href="consultar_rotas.php"><i class="fas fa-route"></i> Rotas</a>
        <a href="#"><i class="fas fa-phone"></i> Contactos</a>
    </div>
</nav>

<!-- Formulário de Login -->
<div class="login-container">
    <div class="login-box glass-effect">
        <div class="login-header">
            <h2>Entrar na sua conta</h2>
        </div>
        
        <form method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Utilizador" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Palavra-passe" required>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
            
            <div class="login-footer">
                <p>Não tem conta? <a href="registo.php">Registe-se aqui</a></p>
                <a href="#" class="forgot-password">Esqueceu a palavra-passe?</a>
            </div>
        </form>
        
        <?php if (isset($erro)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= $erro ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Rodapé -->
<footer class="footer-transparent">
    &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
</footer>

</body>
</html>
