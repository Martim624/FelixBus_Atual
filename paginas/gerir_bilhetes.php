<?php
session_start();
require_once("../basededados/basedados.h");

// Verifica se é funcionário ou admin
if (!isset($_SESSION['id']) || !in_array($_SESSION['idPerfil'], [3, 4])) {
    header("Location: login.php");
    exit();
}

// Query para obter todos os bilhetes com dados da rota
$query = "SELECT b.id, b.dataCompra, b.bilheteValidado,
                 u.username AS cliente_nome,
                 r.origem, r.destino, r.dataViagem, r.hora
          FROM bilhete b
          JOIN utilizador u ON b.idUtilizador = u.id
          JOIN rota r ON b.idRota = r.id
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
                    <th>Origem</th>
                    <th>Destino</th>
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
                        <td><?= htmlspecialchars($bilhete['origem']) ?></td>
                        <td><?= htmlspecialchars($bilhete['destino']) ?></td>
                        <td><?= htmlspecialchars($bilhete['dataViagem']) ?></td>
                        <td><?= htmlspecialchars($bilhete['hora']) ?></td>
                        <td><?= htmlspecialchars($bilhete['dataCompra']) ?></td>
                        <td>
                            <?php if ($bilhete['bilheteValidado']): ?>
                                <span class="validado">Sim</span>
                            <?php else: ?>
                                <span class="nao-validado">Não</span>
                                <form action="validar_bilhete.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="idBilhete" value="<?= $bilhete['id'] ?>">
                                    <button type="submit">Validar</button>
                                </form>
                            <?php endif; ?>
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
