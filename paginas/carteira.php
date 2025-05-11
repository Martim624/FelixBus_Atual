<?php
session_start();
require_once("../basededados/basedados.h");

$id = $_SESSION["id"];  // ID do utilizador logado
$erro = null;
$carteira = ['saldo' => 0.0]; // valor por defeito

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $valor = isset($_POST["valor"]) ? floatval($_POST["valor"]) : 0;
    $tipo = $_POST["tipo"] ?? '';

    if ($valor <= 0) {
        $erro = "O valor deve ser um número positivo.";
    } else {
        // Obter saldo atual
        $stmt = $ligacao->prepare("SELECT saldo FROM carteira WHERE idUtilizador = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $carteira = $resultado->fetch_assoc();
        $stmt->close();

        if (!$carteira) {
            $erro = "Carteira não encontrada.";
        } elseif ($tipo === "levantar" && $carteira["saldo"] < $valor) {
            $erro = "Saldo insuficiente para levantamento.";
        } else {
            $novoSaldo = ($tipo === "adicionar") ? $carteira["saldo"] + $valor : $carteira["saldo"] - $valor;

            // Atualizar saldo
            $stmt = $ligacao->prepare("UPDATE carteira SET saldo = ? WHERE idUtilizador = ?");
            $stmt->bind_param("di", $novoSaldo, $id);
            $stmtSuccess = $stmt->execute();
            $stmt->close();

            if ($stmtSuccess) {
                // Registar auditoria
                $descricao = "$tipo saldo: $valor";
                $stmt = $ligacao->prepare("INSERT INTO auditoria (operacao, valor, idOrigem, descricao) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sdis", $tipo, $valor, $id, $descricao);
                $stmt->execute();
                $stmt->close();

                // Atualizar saldo visível após operação
                $carteira["saldo"] = $novoSaldo;
            } else {
                $erro = "Erro ao atualizar o saldo.";
            }
        }
    }
} else {
    // Obter saldo atual se for GET
    $stmt = $ligacao->prepare("SELECT saldo FROM carteira WHERE idUtilizador = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $carteira = $resultado->fetch_assoc();
    $stmt->close();

    if (!$carteira) {
        $erro = "Não foi possível encontrar o saldo.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Atualizar Saldo - FelixBus</title>
</head>
<body>
<div class="container">
    <h2>Atualizar Saldo</h2>

    <?php if ($erro): ?>
        <p class="error"><?= htmlspecialchars($erro) ?></p>
    <?php else: ?>
        <p class="saldo">Saldo atual: €<?= number_format($carteira['saldo'], 2) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="number" name="valor" step="0.01" min="0.01" placeholder="Valor" required>
        <select name="tipo" required>
            <option value="adicionar">Adicionar</option>
            <option value="levantar">Levantar</option>
        </select>
        <button type="submit">Submeter</button>
    </form>
</div>
</body>
</html>
