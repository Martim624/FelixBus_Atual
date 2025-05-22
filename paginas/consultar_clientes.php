<?php
session_start();
require_once("../basededados/basedados.h");

// Verifica se é funcionário ou administrador
if (!isset($_SESSION['id']) || !in_array($_SESSION['idPerfil'], [3, 4])) {
    header("Location: login.php");
    exit();
}

// Vai buscar todos os utilizadores com o nome do perfil associado
$query = "SELECT u.id, u.username, u.email, u.telemovel, p.designacao AS perfil
          FROM utilizador u
          JOIN perfil p ON u.idPerfil = p.idPerfil
          ORDER BY u.username ASC";

$result = mysqli_query($ligacao, $query);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Consultar Utilizadores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Lista de Utilizadores</h1>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Telemóvel</th>
                    <th>Perfil</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($utilizador = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($utilizador['id']) ?></td>
                        <td><?= htmlspecialchars($utilizador['username']) ?></td>
                        <td><?= htmlspecialchars($utilizador['email']) ?></td>
                        <td><?= htmlspecialchars($utilizador['telemovel']) ?></td>
                        <td><?= htmlspecialchars($utilizador['perfil']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Não existem utilizadores registados.</p>
    <?php endif; ?>
</body>
</html>
