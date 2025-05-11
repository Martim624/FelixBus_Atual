<?php
session_start();
require_once("../basededados/basedados.h");

// Verifica autenticação e perfil
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil'])) {
    header("Location: login.php");
    exit();
}

// Obter dados do usuário
$idUtilizador = $_SESSION['id'];

// Função para obter o nome do perfil
function obterNomePerfil($idPerfil) {
    switch($idPerfil) {
        case 1: return 'Visitante';
        case 2: return 'Cliente';
        case 3: return 'Funcionário';
        case 4: return 'Administrador';
        default: return 'Desconhecido';
    }
}

// Função para obter os dados do utilizador
function obterDadosUtilizador($ligacao, $idUtilizador) {
    $query = "SELECT username, email, morada, telemovel FROM utilizador WHERE id = ?";
    $stmt = mysqli_prepare($ligacao, $query);
    
    if (!$stmt) {
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, "i", $idUtilizador);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return null;
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $dados = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $dados;
}

// Obter dados do utilizador
$dadosUtilizador = obterDadosUtilizador($ligacao, $idUtilizador);

if (!$dadosUtilizador) {
    $erro = "Não foi possível carregar os dados do utilizador.";
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Meus Dados | FelixBus</title>
</head>
<body>

<div class="container">
    <h1>Meus Dados</h1>
    
    <?php if (isset($erro)): ?>
        <div class="erro"><?= $erro ?></div>
    <?php else: ?>
        <div class="dados">
            <p><strong>Nome:</strong> <?= $dadosUtilizador['username'] ?></p>
            <p><strong>Email:</strong> <?= $dadosUtilizador['email'] ?></p>
            <p><strong>Morada:</strong> <?= $dadosUtilizador['morada'] ?></p>
            <p><strong>Telemóvel:</strong> <?= $dadosUtilizador['telemovel'] ?></p>
            <p><strong>Perfil:</strong> <?= obterNomePerfil($_SESSION['idPerfil']) ?></p>
        </div>
        
        <a href="editar_perfil.php" class="botao-editar">Editar Perfil</a>
    <?php endif; ?>
</div>

</body>
</html>
