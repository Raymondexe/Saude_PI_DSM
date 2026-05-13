<?php
session_start();
include("../../config/conexao.php");

$idFamilia = $_POST['idFamilia'] ?? null;

if (!$idFamilia) {
    die("Família inválida");
}

$stmt = $conn->prepare("
    DELETE FROM tblFamilia
    WHERE idFamilia = ?
");

$stmt->bind_param("i", $idFamilia);

echo $stmt->execute() ? "ok" : "erro";
?>