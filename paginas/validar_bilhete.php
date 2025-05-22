<?php
session_start();
require_once("../basededados/basedados.h");

// Verifica se é funcionário ou admin
if (!isset($_SESSION['id']) || !in_array($_SESSION['idPerfil'], [3, 4])) {
    header("Location: login.php");
    exit();
}

// Verifica se foi enviado um ID válido
if (isset($_POST['idBilhete']) && is_numeric($_POST['idBilhete'])) {
    $idBilhete = $_POST['idBilhete'];

    // Prepara e executa o update
    $stmt = mysqli_prepare($ligacao, "UPDATE bilhete SET bilheteValidado = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $idBilhete);
    mysqli_stmt_execute($stmt);

    // Verifica se foi atualizado com sucesso
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['msg'] = "Bilhete validado com sucesso.";
    } else {
        $_SESSION['msg'] = "Erro ao validar o bilhete.";
    }

    mysqli_stmt_close($stmt);
}

// Redireciona de volta à página de gestão
header("Location: gerir_bilhetes.php");
exit();
?>
