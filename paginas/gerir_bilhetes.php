<?php
session_start();
require_once("../basededados/basedados.h");

// Verifica se é funcionário ou admin
if (!isset($_SESSION['id']) || !in_array($_SESSION['idPerfil'], [3, 4])) {
    header("Location: login.php");
    exit();
}

// Query para obter todos os bilhetes
$query = "SELECT b.id, b.dataCompra, b.validado,
                 u.username AS cliente_nome,
                 v.dataViagem, v.horaViagem, v.idRota
          FROM bilhete b
          JOIN utilizador u ON b.idUtilizador = u.id
          JOIN viagem v ON b.idViagem = v.id
          ORDER BY b.dataCompra DESC";

$result = mysqli_query($ligacao, $query);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerir Bilhetes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .validado { color: green; font-weight: bold; }
        .nao-validado { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Gestão de Bilhetes</h1>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Rota</th>
                    <th>Data da Viagem</th>
                    <th>Hora</th>
                    <th>Data de Compra</th>
                    <th>Validado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($bilhete = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($bilhete['id']) ?></td>
                        <td><?= htmlspecialchars($bilhete['cliente_nome']) ?></td>
                        <td><?= htmlspecialchars($bilhete['idRota']) ?></td>
                        <td><?= htmlspecialchars($bilhete['dataViagem']) ?></td>
                        <td><?= htmlspecialchars($bilhete['horaViagem']) ?></td>
                        <td><?= htmlspecialchars($bilhete['dataCompra']) ?></td>
                        <td>
                            <?= $bilhete['validado']
                                ? '<span class="validado">Sim</span>'
                                : '<span class="nao-validado">Não</span>' ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Não existem bilhetes registados.</p>
    <?php endif; ?>
</body>
</html>
