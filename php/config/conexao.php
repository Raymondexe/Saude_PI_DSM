<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "BemEstar360";

$conn = new mysqli($host, $user, $pass, $db); // sem 3307

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>