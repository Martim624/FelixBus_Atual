<?php
include("../basededados/basedados.h");
session_start();

// Verificar se é administrador
if (!isset($_SESSION['id']) || !isset($_SESSION['idPerfil']) || $_SESSION['idPerfil'] != 4) {
    header("Location: login.php");
    exit();
}

// Buscar todas as rotas
$sql = "SELECT * FROM rota ORDER BY dataViagem, hora";
$resultado = mysqli_query($ligacao, $sql);
$rotas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

// Mensagens de sucesso ou erro (opcionalmente recebidas via sessão)
$success = $_SESSION['success'] ?? '';
$errors = $_SESSION['errors'] ?? [];

unset($_SESSION['success'], $_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Rotas - FelixBus</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="body-secondary">
<nav>
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="gerir_rotas.php" class="active"><i class="fas fa-route"></i> Gerir Rotas</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Terminar Sessão</a>
    </div>
</nav>

<div class="container">
    <h1>Gerir Rotas</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $erro): ?>
                    <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <a href="adicionar_rota.php" class="btn btn-primary">Adicionar Nova Rota</a>

    <table class="rotas-table">
        <thead>
            <tr>
                <th>Origem</th>
                <th>Destino</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Preço (€)</th>
                <th>Capacidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rotas as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['origem']) ?></td>
                    <td><?= htmlspecialchars($r['destino']) ?></td>
                    <td><?= htmlspecialchars($r['dataViagem']) ?></td>
                    <td><?= htmlspecialchars($r['hora']) ?></td>
                    <td><?= number_format($r['preco'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($r['capacidade']) ?></td>
                    <td>
                        <a href="editar_rota.php?id=<?= $r['id'] ?>" class="btn btn-secondary">Editar</a>
                        <a href="eliminar_rota.php?id=<?= $r['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem a certeza que deseja eliminar esta rota?');">Eliminar</a>
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

<?php
mysqli_close($ligacao);
?>
