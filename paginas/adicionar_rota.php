<?php
include("../basededados/basedados.h");
session_start();

if (!isset($_SESSION['id']) || $_SESSION['idPerfil'] != 4) {
    header("Location: login.php");
    exit();
}

$errors = [];

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
        $stmt = mysqli_prepare($ligacao, "INSERT INTO rota (origem, destino, dataViagem, hora, preco, capacidade) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssdi", $origem, $destino, $dataViagem, $hora, $preco, $capacidade);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: listar_rotas.php?msg=added");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Rota - FelixBus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Adicionar Nova Rota</h1>

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
        <input type="text" name="origem" required>

        <label>Destino:</label>
        <input type="text" name="destino" required>

        <label>Data da Viagem:</label>
        <input type="date" name="dataViagem" required>

        <label>Hora:</label>
        <input type="time" name="hora" required>

        <label>Preço (€):</label>
        <input type="number" step="0.01" name="preco" required>

        <label>Capacidade:</label>
        <input type="number" name="capacidade" required>

        <button type="submit" class="btn btn-primary">Adicionar</button>
        <a href="listar_rotas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
<?php
// Close the database connection
mysqli_close($ligacao);
?>