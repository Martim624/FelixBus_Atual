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
        // Definir as variáveis de sessão
        $_SESSION["id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["perfil"] = $user["idPerfil"];
        
        // Redirecionar com base no perfil
        if ($user["idPerfil"] == 3) {
            // Admin
            header("Location: admin_pagina.php");
        } elseif ($user["idPerfil"] == 2) {
            // Funcionário
            header("Location: funcionario_pagina.php");
        } else {
            // Cliente
            header("Location: cliente_pagina.php");
        }
        exit();
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
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            /* Verifique o caminho correto da imagem */
            background: url('banner.jpg') center/cover no-repeat !important;
            background-size: cover !important;
            color: #333;
        }

        /* Estilos de navegação fixa */
        nav {
            background: linear-gradient(135deg, #006400, #32CD32);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 999;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: background 0.3s ease;
        }

        .logo {
            color: white;
            font-size: 1.8em;
            font-weight: bold;
            text-transform: uppercase;
            margin-left: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-weight: bold;
            font-size: 1.1em;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #4CAF50;
            border-radius: 5px;
        }

        /* Estilos para o login */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: rgba(255, 255, 255, 0.8); /* Semi-transparente */
        }

        .login-box {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            color: #333;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            background-color: #32CD32;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-box button:hover {
            background-color: #28a745;
        }

        .login-box .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .login-box {
                padding: 20px;
            }

            .login-box h2 {
                font-size: 1.5em;
            }

            nav a {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>

<!-- Barra de navegação fixa no topo -->
<nav>
    <!-- Torne a logo clicável -->
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <a href="login.php">Login</a>
        <a href="registo.php">Registo</a>
        <a href="consultar_rotas.php">Rotas</a>
        <a href="#">Contactos</a>
    </div>
</nav>

<!-- Formulário de Login -->
<div class="login-container">
    <div class="login-box">
        <h2>Entrar</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Utilizador" required>
            <input type="password" name="password" placeholder="Palavra-passe" required>
            <button type="submit">Entrar</button>
        </form>
        <?php if (isset($erro)) echo "<p class='error'>$erro</p>"; ?>
    </div>
</div>

<!-- Rodapé -->
<footer>
    &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
</footer>

</body>
</html>
