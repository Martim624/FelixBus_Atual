<?php
include("../basededados/basedados.h");
session_start();

if (!isset($_SESSION['id']) || $_SESSION['idPerfil'] != 2) {
    header("Location: login.php");
    exit();
}

$idUtilizador = $_SESSION['id'];

// Obter bilhetes do cliente com info da rota
$sql = "
    SELECT b.dataCompra, r.origem, r.destino, r.dataViagem, r.hora
    FROM bilhete b
    INNER JOIN rota r ON b.idRota = r.id
    WHERE b.idUtilizador = $idUtilizador
    ORDER BY b.dataCompra DESC
";

$resultado = mysqli_query($ligacao, $sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Meus Bilhetes - FelixBus</title>
</head>
<body>
    <h1>Meus Bilhetes</h1>

    <?php if (mysqli_num_rows($resultado) === 0): ?>
        <p>Não tens bilhetes comprados.</p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Origem</th>
                <th>Destino</th>
                <th>Data da Viagem</th>
                <th>Hora</th>
                <th>Data de Compra</th>
            </tr>
            <?php while ($bilhete = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?= htmlspecialchars($bilhete['origem']) ?></td>
                    <td><?= htmlspecialchars($bilhete['destino']) ?></td>
                    <td><?= $bilhete['dataViagem'] ?></td>
                    <td><?= $bilhete['hora'] ?></td>
                    <td><?= $bilhete['dataCompra'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard.php">Voltar ao Dashboard</a></p>
</body>
</html>
