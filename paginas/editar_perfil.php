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
    $query = "SELECT username, email, idPerfil FROM utilizador WHERE id = ?";
    $stmt = mysqli_prepare($ligacao, $query);
    mysqli_stmt_bind_param($stmt, "i", $idUtilizador);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Função para atualizar os dados do utilizador
function atualizarDadosUtilizador($ligacao, $idUtilizador, $username, $email, $idPerfil) {
    $query = "UPDATE utilizador SET username = ?, email = ?, idPerfil = ? WHERE id = ?";
    $stmt = mysqli_prepare($ligacao, $query);
    mysqli_stmt_bind_param($stmt, "ssii", $username, $email, $idPerfil, $idUtilizador);
    return mysqli_stmt_execute($stmt);
}

// Obter dados do utilizador
$idUtilizador = $_SESSION['id'];
$dadosUtilizador = obterDadosUtilizador($ligacao, $idUtilizador);
$username = $dadosUtilizador['username'];
$email = $dadosUtilizador['email'];
$idPerfil = $dadosUtilizador['idPerfil'];

// Se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $idPerfil = $_POST['perfil'];

    if (atualizarDadosUtilizador($ligacao, $idUtilizador, $username, $email, $idPerfil)) {
        $mensagem = "Dados atualizados com sucesso!";
        $_SESSION['username'] = $username;
        $_SESSION['idPerfil'] = $idPerfil;
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
    <title>Editar Perfil | FelixBus</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #28a745;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-submit {
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .message {
            text-align: center;
            font-size: 1.2em;
            padding: 10px;
            margin: 20px 0;
        }
        .success {
            color: #28a745;
            background-color: #e0f3e0;
        }
        .error {
            color: #dc3545;
            background-color: #f8d7da;
        }
    </style>
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
            <label for="perfil">Perfil:</label>
            <select id="perfil" name="perfil">
                <option value="1" <?= $idPerfil == 1 ? 'selected' : '' ?>>Visitante</option>
                <option value="2" <?= $idPerfil == 2 ? 'selected' : '' ?>>Cliente</option>
                <option value="3" <?= $idPerfil == 3 ? 'selected' : '' ?>>Funcionário</option>
                <option value="4" <?= $idPerfil == 4 ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Atualizar</button>
    </form>
</div>

</body>
</html>