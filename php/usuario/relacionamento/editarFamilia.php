<?php
session_start();
include("../../config/conexao.php");

$idFamilia = $_POST['idFamilia'] ?? null;
$novoNome = trim($_POST['novoNome'] ?? '');

if (!$idFamilia || !$novoNome) {
    die("Dados inválidos");
}

$stmt = $conn->prepare("
    UPDATE tblFamilia
    SET nomeFamilia = ?
    WHERE idFamilia = ?
");

$stmt->bind_param("si", $novoNome, $idFamilia);

echo $stmt->execute() ? "ok" : "erro";
?>