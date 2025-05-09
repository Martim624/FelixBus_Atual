<?php
session_start();
require_once("validar_sessao.php");  // Verificação de sessão ativa (proteção)

// Verificar se o perfil do usuário é Admin
if ($_SESSION['perfil'] != 3) {
    header("Location: index.php"); // Redirecionar se não for admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="logo">FelixBus - Admin</div>
        <div class="menu">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="gestao_utilizadores.php">Gestão de Utilizadores</a>
            <a href="logout.php">Sair</a>
        </div>
    </nav>

    <div class="container">
        <h1>Bem-vindo ao painel de administração</h1>
        <p>Gerencie os usuários, veja relatórios e outras configurações do sistema.</p>
    </div>

    <footer>
        &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
    </footer>
</body>
</html>
