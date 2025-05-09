<?php
session_start();
require_once("validar_sessao.php");  // Verificação de sessão ativa (proteção)

// Verificar se o perfil do usuário é Funcionário
if ($_SESSION['perfil'] != 2) {
    header("Location: index.php"); // Redirecionar se não for funcionário
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Funcionário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="logo">FelixBus - Funcionário</div>
        <div class="menu">
            <a href="funcionario_dashboard.php">Dashboard</a>
            <a href="consultar_rotas.php">Consultar Rotas</a>
            <a href="logout.php">Sair</a>
        </div>
    </nav>

    <div class="container">
        <h1>Bem-vindo ao painel do Funcionário</h1>
        <p>Consulte as rotas, gerencie as viagens e muito mais.</p>
    </div>

    <footer>
        &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
    </footer>
</body>
</html>
