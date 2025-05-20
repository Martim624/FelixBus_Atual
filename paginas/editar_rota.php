<?php
include("../basededados/basedados.h");
session_start();

if (!isset($_SESSION['id']) || $_SESSION['idPerfil'] != 4) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: listar_rotas.php");
    exit();
}

$errors = [];

$stmt = mysqli_prepare($ligacao, "SELECT * FROM rota WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rota = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$rota) {
    header("Location: listar_rotas.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origem = trim($_POST['origem'] ?? '');
    $destino = trim($_POST['destino'] ?? '');
    $dataViagem = trim($_POST['dataViagem'] ?? '');
    $hora = trim($_POST['hora'] ?? '');
    $preco = trim($_POST['preco'] ?? '');
    $capacidade = trim($_POST['capacidade'] ?? '');

    if ($origem === '') $errors[] = "Origem é obrigatória.";
    if ($destino === '') $errors[] = "Destino é obrigatório.";
    if ($dataViagem === '') $errors[] = "Data da viagem é obrigatória.";
    if ($hora === '') $errors[] = "Hora é obrigatória.";
    if ($preco === '' || !is_numeric($preco) || $preco < 0) $errors[] = "Preço inválido.";
    if ($capacidade === '' || !ctype_digit($capacidade) || $capacidade < 1) $errors[] = "Capacidade inválida.";

    if (empty($errors)) {
        $stmt = mysqli_prepare($ligacao, "UPDATE rota SET origem=?, destino=?, dataViagem=?, hora=?, preco=?, capacidade=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssdii", $origem, $destino, $dataViagem, $hora, $preco, $capacidade, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: listar_rotas.php?msg=edited");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Rota - FelixBus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Editar Rota</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $erro): ?>
                    <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Origem:</label>
        <input type="text" name="origem" value="<?= htmlspecialchars($rota['origem']) ?>" required>

        <label>Destino:</label>
        <input type="text" name="destino" value="<?= htmlspecialchars($rota['destino']) ?>" required>

        <label>Data da Viagem:</label>
        <input type="date" name="dataViagem" value="<?= htmlspecialchars($rota['dataViagem']) ?>" required>

        <label>Hora:</label>
        <input type="time" name="hora" value="<?= htmlspecialchars($rota['hora']) ?>" required>

        <label>Preço (€):</label>
        <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($rota['preco']) ?>" required>

        <label>Capacidade:</label>
        <input type="number" name="capacidade" value="<?= htmlspecialchars($rota['capacidade']) ?>" required>

        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="listar_rotas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
<?php
mysqli_close($ligacao);
?>