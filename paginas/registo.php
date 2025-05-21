<?php
session_start();
require_once("../basededados/basedados.h");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = hash('sha256', $_POST["password"]);
    $email = $_POST["email"];

    // Verificar se o utilizador já existe
    $sql = "SELECT * FROM utilizador WHERE username = ?";
    $stmt = mysqli_prepare($ligacao, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        $erro = "Nome de utilizador já existe.";
    } else {
        // Inserir o novo utilizador
        $sql = "INSERT INTO utilizador (username, password, email, idPerfil, validado) VALUES (?, ?, ?, 2, 0)";
        $stmt = mysqli_prepare($ligacao, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $password, $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $idUtilizador = mysqli_insert_id($ligacao);

            $sqlCarteira = "INSERT INTO carteira (idUtilizador, saldo) VALUES (?, 0.0)";
            $stmtCarteira = mysqli_prepare($ligacao, $sqlCarteira);
            mysqli_stmt_bind_param($stmtCarteira, "i", $idUtilizador);

            if (mysqli_stmt_execute($stmtCarteira)) {
                $_SESSION["username"] = $username;
                $_SESSION["perfil"] = 1;
                $_SESSION["id"] = $idUtilizador;
                header("Location: login.php");
                exit();
            } else {
                $erro = "Erro ao criar a carteira. Tente novamente mais tarde.";
            }
        } else {
            $erro = "Erro ao registar. Tente novamente mais tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>FelixBus - Registo</title>
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
        <a href="rotas.php"><i class="fas fa-route"></i> Rotas</a>
        <a href="#"><i class="fas fa-phone"></i> Contactos</a>
    </div>
</nav>

<!-- Formulário de Registo -->
<div class="login-container">
    <div class="login-box glass-effect">
        <div class="login-header">
            <h2>Criar Nova Conta</h2>
        </div>
        
        <form method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Nome de utilizador" required class="form-control">
            </div>
            
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required class="form-control">
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Palavra-passe" required class="form-control">
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-user-plus"></i> Registar
            </button>
            
            <div class="login-footer">
                <p>Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
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