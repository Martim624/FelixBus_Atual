<?php
include("../basededados/basedados.h");
session_start();

if (!isset($_SESSION['id']) || $_SESSION['idPerfil'] != 4) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = mysqli_prepare($ligacao, "DELETE FROM rota WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: listar_rotas.php?msg=deleted");
    exit();
}

$sql = "SELECT * FROM rota ORDER BY dataViagem, hora";
$resultado = mysqli_query($ligacao, $sql);
$rotas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

$msg = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Rotas - FelixBus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="listar_rotas.php" class="active">Gerir Rotas</a>
        <a href="logout.php">Terminar Sessão</a>
    </div>
</nav>

<div class="container">
    <h1>Gerir Rotas</h1>

    <?php if ($msg === 'deleted'): ?>
        <div class="alert alert-success">Rota eliminada com sucesso.</div>
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
                        <a href="listar_rotas.php?delete=<?= $r['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem a certeza que deseja eliminar esta rota?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<footer>
    &copy; <?= date("Y") ?> FelixBus. Todos os direitos reservados.
</footer>
</body>
</html>
