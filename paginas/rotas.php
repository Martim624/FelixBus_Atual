<?php
include("../basededados/basedados.h");
session_start();

// Obter perfil atual (ou visitante por defeito)
$idPerfil = $_SESSION['idPerfil'] ?? 0; // 0 = visitante

// Obter rotas com cálculo de lugares disponíveis
$sql = "SELECT r.*, 
       (r.capacidade - COUNT(b.id)) AS lugares_disponiveis
       FROM rota r
       LEFT JOIN bilhete b ON r.id = b.idRota
       GROUP BY r.id";
$resultado = mysqli_query($ligacao, $sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Rotas Disponíveis - FelixBus</title>
</head>
<body>
    <h1>Rotas Disponíveis</h1>
    <table border="1">
        <tr>
            <th>Origem</th>
            <th>Destino</th>
            <th>Data</th>
            <th>Hora</th>
            <th>Preço</th>
            <th>Lugares Disponíveis</th>
            <th>Ação</th>
        </tr>
        <?php while ($rota = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?= htmlspecialchars($rota['origem']) ?></td>
                <td><?= htmlspecialchars($rota['destino']) ?></td>
                <td><?= $rota['dataViagem'] ?></td>
                <td><?= $rota['hora'] ?></td>
                <td><?= $rota['preco'] ?>€</td>
                <td><?= $rota['lugares_disponiveis'] ?></td>
                <td>
                    <?php if ($rota['lugares_disponiveis'] <= 0): ?>
                        Esgotado
                    <?php elseif ($idPerfil == 2): ?>
                        <form action="comprar_bilhete.php" method="post">
                            <input type="hidden" name="idRota" value="<?= $rota['id'] ?>">
                            <input type="submit" value="Comprar">
                        </form>
                    <?php else: ?>
                        <em>Autentique-se como cliente para comprar</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <?php if (isset($_SESSION['id'])): ?>
        <p><a href="dashboard.php">Voltar ao Dashboard</a></p>
    <?php else: ?>
        <p><a href="login.php">Iniciar Sessão</a></p>
    <?php endif; ?>
</body>
</html>
