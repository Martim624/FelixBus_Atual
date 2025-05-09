<?php


$host = "localhost";
$user = "root";
$password = "";
$dbname = "felixbus";

$ligacao = mysqli_connect($host, $user, $password, $dbname);

// Verificar ligação
if (!$ligacao) {
    die("Erro na ligação à base de dados: " . mysqli_connect_error());
}

// Garantir codificação UTF-8
mysqli_set_charset($ligacao, "utf8");
?>
