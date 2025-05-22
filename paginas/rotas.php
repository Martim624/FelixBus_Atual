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
$rotas = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Rotas Disponíveis - FelixBus</title>
    <link rel="shortcut icon" type="image/png" href="logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="body-secondary">
<!-- Barra de navegação fixa no topo -->
 <nav>
    <a href="index.php" class="logo">FelixBus</a>
    <div class="menu">
        <?php if (isset($_SESSION['id'])): ?>   
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <?php else: ?>
            <a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="registo.php"><i class="fas fa-user-plus"></i> Registo</a>
        <?php endif; ?>
            <a href="consultar_rotas.php"><i class="fas fa-route"></i> Rotas</a>
            <a href="#"><i class="fas fa-phone"></i> Contactos</a>
    </div>
</nav>


<!-- Conteúdo principal -->
<div class="container" >
    <h1>Rotas Disponíveis</h1>
    
    <table class="rotas-table">
        <thead>
            <tr>
                <th>Origem</th>
                <th>Destino</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Preço</th>
                <th>Lugares</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rotas as $rota): ?>
                <tr>
                    <td><?= $rota['origem'] ?></td>
                    <td><?= $rota['destino'] ?></td>
                    <td><?= $rota['dataViagem'] ?></td>
                    <td><?= $rota['hora'] ?></td>
                    <td><?= $rota['preco'] ?>€</td>
                    <td class="<?= $rota['lugares_disponiveis'] <= 0 ? 'esgotado' : 'lugares-disponiveis' ?>">
                        <?= $rota['lugares_disponiveis'] <= 0 ? 'Esgotado' : $rota['lugares_disponiveis'] ?>
                    </td>
                    <td>
                        <?php if ($rota['lugares_disponiveis'] <= 0): ?>
                            <span class="esgotado">Esgotado</span>
                        <?php elseif ($idPerfil == 2): ?>
                            <form action="comprar_bilhete.php" method="post" style="display: inline;">
                                <input type="hidden" name="idRota" value="<?= $rota['id'] ?>">
                                <button type="submit" class="btn btn-primary">Comprar</button>
                            </form>
                        <?php else: ?>
                            <span class="auth-message">Autentique-se como cliente para comprar</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (isset($_SESSION['id'])): ?>
        <a href="dashboard.php" class="btn btn-secondary voltar-link">Voltar ao Dashboard</a>
    <?php else: ?>
        <a href="login.php" class="btn btn-primary voltar-link">Iniciar Sessão</a>
    <?php endif; ?>
</div>


</body>


</html>
