<?php
session_start();
require_once("validar_sessao.php");  // Verificação de sessão ativa (proteção)
require_once("../basededados/basedados.h");

$id = $_SESSION["id"];  // Pega o ID do utilizador logado

// Verificação se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = floatval($_POST["valor"]);  // Valor a ser adicionado ou retirado
    $tipo = $_POST["tipo"]; // Tipo: "adicionar" ou "levantar"

    // Verificar se o valor é positivo
    if ($valor <= 0) {
        $erro = "O valor deve ser positivo."; // Erro se o valor for inválido
    } else {
        // Verificar se o tipo é 'adicionar' ou 'levantar'
        if ($tipo == "levantar") {
            // Buscar o saldo atual antes de efetuar o levantamento
            $carteira_sql = "SELECT saldo FROM carteira WHERE idUtilizador = $id";
            $carteira_resultado = mysqli_query($ligacao, $carteira_sql);
            $carteira = mysqli_fetch_assoc($carteira_resultado);

            // Verificar se o saldo é suficiente para levantar
            if ($carteira['saldo'] < $valor) {
                $erro = "Saldo insuficiente para levantar esse valor.";  // Erro se o saldo for insuficiente
            }
        }

        // Determinar operação de adicionar ou levantar
        $op = ($tipo === "adicionar") ? "+" : "-";

        // Atualizar o saldo na tabela de carteira
        $novo_saldo_sql = "UPDATE carteira SET saldo = saldo $op $valor WHERE idUtilizador = $id";
        if (mysqli_query($ligacao, $novo_saldo_sql)) {
            // Gravar a operação de auditoria
            $descricao = "$tipo saldo: $valor";
            $auditoria_sql = "INSERT INTO auditoria (operacao, valor, idOrigem, descricao) VALUES ('$tipo', $valor, $id, '$descricao')";
            mysqli_query($ligacao, $auditoria_sql);
        } else {
            $erro = "Erro ao atualizar o saldo.";  // Erro ao tentar atualizar o saldo
        }
    }
}

// Buscar o saldo atual da carteira
$carteira_sql = "SELECT saldo FROM carteira WHERE idUtilizador = $id";
$carteira_resultado = mysqli_query($ligacao, $carteira_sql);

if ($carteira_resultado) {
    // Se a consulta foi bem-sucedida, verificar se há resultados
    $carteira = mysqli_fetch_assoc($carteira_resultado);
    if (!$carteira) {
        $erro = "Não foi possível encontrar o saldo do utilizador.";
    }
} else {
    $erro = "Erro na consulta ao saldo.";
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Saldo - FelixBus</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input, select, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        button {
            background-color: #32CD32;
            color: white;
            cursor: pointer;
            font-weight: bold;
            border: none;
        }

        button:hover {
            background-color: #28a745;
        }

        .error {
            color: red;
            text-align: center;
        }

        p.saldo {
            font-size: 1.2em;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Container do Formulário -->
<div class="container">
    <h2>Atualizar Saldo</h2>
    
    <!-- Exibição do saldo atual -->
    <?php if (isset($erro)) { ?>
        <p class="error"><?= $erro ?></p>
    <?php } else { ?>
        <p class="saldo">Saldo atual: €<?= number_format($carteira['saldo'], 2) ?></p>
    <?php } ?>
    
    <!-- Formulário de adição ou levantamento -->
    <form method="post">
        <input type="number" name="valor" step="0.01" min="0" placeholder="Valor" required>
        
        <select name="tipo" required>
            <option value="adicionar">Adicionar</option>
            <option value="levantar">Levantar</option>
        </select>
        
        <button type="submit">Submeter</button>
    </form>
</div>

</body>
</html>
