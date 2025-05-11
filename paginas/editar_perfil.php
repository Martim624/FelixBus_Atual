<?php
session_start();
require_once("../basededados/basedados.h");

// Verifica autenticação e perfil
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil'])) {
    header("Location: login.php");
    exit();
}

// Função para obter os dados do utilizador
function obterDadosUtilizador($ligacao, $idUtilizador) {
    $query = "SELECT username, email, morada, telemovel, idPerfil FROM utilizador WHERE id = ?";
    $stmt = mysqli_prepare($ligacao, $query);
    mysqli_stmt_bind_param($stmt, "i", $idUtilizador);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Função para atualizar os dados do utilizador
function atualizarDadosUtilizador($ligacao, $idUtilizador, $username, $email, $morada , $telemovel, $idPerfil) {
    $query = "UPDATE utilizador SET username = ?, email = ?, morada = ?, telemovel = ?, idPerfil = ? WHERE id = ?";
    $stmt = mysqli_prepare($ligacao, $query);
    mysqli_stmt_bind_param($stmt, "sssiii", $username, $email, $morada , $telemovel, $idPerfil, $idUtilizador);
    return mysqli_stmt_execute($stmt);
}

// Obter dados do utilizador
$idUtilizador = $_SESSION['id'];
$dadosUtilizador = obterDadosUtilizador($ligacao, $idUtilizador);
$username = $dadosUtilizador['username'];
$email = $dadosUtilizador['email'];
$morada = $dadosUtilizador['morada'];
$telemovel = $dadosUtilizador['telemovel'];
$idPerfil = $dadosUtilizador['idPerfil'];

// Se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $morada = $_POST['morada'];
    $telemovel = $_POST['telemovel'];
    $idPerfil = $_SESSION['idPerfil'];

    if (atualizarDadosUtilizador($ligacao, $idUtilizador, $username, $email, $morada, $telemovel, $idPerfil)) {
        $mensagem = "Dados atualizados com sucesso!";
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['morada'] = $morada;
        $_SESSION['telemovel'] = $telemovel;
    } else {
        $mensagem = "Erro ao atualizar os dados.";
    }
}

// Função para obter o nome do perfil
function obterNomePerfil($idPerfil) {
    $perfis = [
        1 => 'Visitante',
        2 => 'Cliente',
        3 => 'Funcionário',
        4 => 'Administrador'
    ];
    return isset($perfis[$idPerfil]) ? $perfis[$idPerfil] : 'Desconhecido';
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Editar Perfil | FelixBus</title>
</head>
<body>

<div class="container">
    <h1>Editar Perfil</h1>

    <?php if (isset($mensagem)): ?>
        <div class="message <?= strpos($mensagem, 'sucesso') !== false ? 'success' : 'error' ?>">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Nome de Utilizador:</label>
            <input type="text" id="username" name="username" value="<?= $username ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= $email ?>" required>
        </div>

        <div class="form-group">
            <label for="morada">Morada:</label>
            <input type="text" id="morada" name="morada" value="<?= $morada ?>">
        </div>

        <div class="form-group">
            <label for="telemovel">Telemóvel:</label>
            <input type="text" id="telemovel" name="telemovel" value="<?= $telemovel ?>">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-submit">Atualizar</button>

        <form action="meus_dados.php" method="get">
           <a href="meus_dados.php" class="btn-submit">Voltar</a>
        </form>

        <form action="dashboard.php" method="get">
            <input type="submit" class="btn-submit" value="Dashboard" />
        </form>
        </div>
    </form>
</div>

</body>
</html>