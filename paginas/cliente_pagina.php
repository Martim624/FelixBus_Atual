<?php
session_start();
require_once("validar_sessao.php");  // Verificação de sessão ativa (proteção)

// Verificar se o perfil do usuário é Cliente
if ($_SESSION['perfil'] != 1) {
    header("Location: index.php"); // Redirecionar se não for cliente
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Cliente</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="logo">FelixBus - Cliente</div>
        <div class="menu">
            <a href="cliente_pagina.php">Dashboard</a>
            <a href="carteira.php">Minha Carteira</a>
            <a href="logout.php">Sair</a>
        </div>
    </nav>

    <div class="container">
        <h1>Bem-vindo ao painel do Cliente</h1>
        <p>Gerencie sua conta, veja suas transações e muito mais.</p>
    </div>

    <footer>
        &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
    </footer>
</body>
</html>
