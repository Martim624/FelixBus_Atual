<?php
include("../basededados/basedados.h");
session_start();

// Verificar se o utilizador está autenticado e é cliente (perfil 2)
if (!isset($_SESSION['id']) || $_SESSION['idPerfil'] != 2) {
    header("Location: login.php");
    exit();
}

$idUtilizador = $_SESSION['id'];
$idRota = intval($_POST['idRota'] ?? 0);

// Obter informações da rota selecionada
$sqlRota = "SELECT * FROM rota WHERE id = $idRota";
$resRota = mysqli_query($ligacao, $sqlRota);
$rota = mysqli_fetch_assoc($resRota);

if (!$rota) {
    echo "Rota inválida.";
    exit();
}

// Verificar lugares disponíveis na rota
$sqlLugares = "SELECT COUNT(*) AS total FROM bilhete WHERE idRota = $idRota";
$resLugares = mysqli_query($ligacao, $sqlLugares);
$comprados = mysqli_fetch_assoc($resLugares)['total'];

if ($comprados >= $rota['capacidade']) {
    echo "Esta viagem está esgotada.";
    exit();
}

// Verificar saldo do cliente
$sqlSaldo = "SELECT saldo FROM carteira WHERE idUtilizador = $idUtilizador";
$resSaldo = mysqli_query($ligacao, $sqlSaldo);
$saldoCliente = mysqli_fetch_assoc($resSaldo)['saldo'];

$preco = $rota['preco'];
if ($saldoCliente < $preco) {
    echo "Saldo insuficiente para comprar este bilhete.";
    exit();
}

// Iniciar transação
mysqli_begin_transaction($ligacao);
try {
    // Deduzir saldo do cliente
    mysqli_query($ligacao, "UPDATE carteira SET saldo = saldo - $preco WHERE idUtilizador = $idUtilizador");

    // Adicionar saldo à carteira da empresa
    mysqli_query($ligacao, "UPDATE carteira_empresa SET saldo = saldo + $preco WHERE id = 1");

    // Registar auditoria da compra
    mysqli_query($ligacao, "
        INSERT INTO auditoria (operacao, valor, idOrigem, idDestino, descricao)
        VALUES ('compra', $preco, $idUtilizador, 0, 'Compra de bilhete para rota $idRota')
    ");

    // Inserir o bilhete
    mysqli_query($ligacao, "
        INSERT INTO bilhete (idUtilizador, idRota)
        VALUES ($idUtilizador, $idRota)
    ");

    // Confirmar todas as alterações
    mysqli_commit($ligacao);

    echo "✅ Bilhete comprado com sucesso.<br>";
    echo "<a href='meus_bilhetes.php'>Ver bilhetes</a>";

} catch (Exception $e) {
    mysqli_rollback($ligacao);
    echo "❌ Erro ao comprar bilhete: " . $e->getMessage();
}
?>
