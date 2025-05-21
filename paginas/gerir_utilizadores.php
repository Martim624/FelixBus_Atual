<?php
include("../basededados/basedados.h");
session_start();

// Verificar se o utilizador está autenticado e é administrador
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil']) || $_SESSION['idPerfil'] != 4) {
    header("Location: login.php");
    exit();
}

// Função para obter o nome do perfil por ID
function obterNomePerfil($idPerfil) {
    switch($idPerfil) {
        case 1: return 'Visitante';
        case 2: return 'Cliente';
        case 3: return 'Funcionário';
        case 4: return 'Administrador';
        default: return 'Desconhecido';
    }
}

// Atualização do estado de validação
if (isset($_GET['id']) && isset($_GET['validado'])) {
    $idUtilizador = (int) $_GET['id'];
    $validado = (int) $_GET['validado'];

    $stmt = mysqli_prepare($ligacao, "UPDATE utilizador SET validado = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $validado, $idUtilizador);
    if (mysqli_stmt_execute($stmt)) {
        $sucesso = "Estado de validação atualizado com sucesso.";
    } else {
        $erros[] = "Erro ao atualizar estado de validação: " . mysqli_error($ligacao);
    }
    mysqli_stmt_close($stmt);
}

// Consulta dos utilizadores
$sql = "SELECT id, username, email, idPerfil, ativo, validado FROM utilizador ORDER BY username";
$resultado = mysqli_query($ligacao, $sql);
$users = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Utilizadores - FelixBus</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="body-secondary">
<nav>
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="gerir_utilizadores.php" class="active"><i class="fas fa-users"></i> Gerir Utilizadores</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Terminar Sessão</a>
    </div>
</nav>

<div class="container">
    <h1>Gerir Utilizadores</h1>

    <a href="adicionar_utilizador.php" class="btn btn-primary">Adicionar Novo Utilizador</a>

    <?php if (!empty($sucesso)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <?php if (!empty($erros)): ?>
        <?php foreach ($erros as $erro): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <table class="rotas-table">
        <thead>
            <tr>
                <th>Nome de Utilizador</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Estado</th>
                <th>Validado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= obterNomePerfil($u['idPerfil']) ?></td>
                    <td>
                        <span class="status-label <?= $u['ativo'] ? 'ativo' : 'inativo' ?>">
                            <?= $u['ativo'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td>
                        <a href="?id=<?= $u['id'] ?>&validado=<?= $u['validado'] == 1 ? 0 : 1 ?>"
                           class="btn <?= $u['validado'] == 1 ? 'btn-success' : 'btn-warning' ?>">
                            <?= $u['validado'] == 1 ? 'Validado' : 'Não Validado' ?>
                        </a>
                    </td>
                    <td>
                        <a href="editar_utilizador.php?id=<?= $u['id'] ?>" class="btn btn-secondary">Editar</a>
                        <a href="eliminar_utilizador.php?id=<?= $u['id'] ?>" class="btn btn-danger"
                           onclick="return confirm('Tem a certeza que deseja eliminar este utilizador?');">
                           Eliminar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<footer class="footer-transparent">
    &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
