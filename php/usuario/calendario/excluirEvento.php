<?php
session_start();
include("../../config/conexao.php");

$idEvento = $_POST['idEvento'] ?? null;

if (!$idEvento) {
    exit("erro");
}

$stmt = $conn->prepare("
    DELETE FROM tblEvento
    WHERE idEvento = ?
");

$stmt->bind_param("i", $idEvento);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "erro";
}
?>