<?php

session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

$idUsuario = $_SESSION['idUsuario'];

$idEvento = $_POST['idEvento'] ?? 0;

if (!$idEvento) {
    die("Evento inválido.");
}

$sql = "
DELETE FROM tblevento
WHERE idEvento = ?
AND Usuario_idUsuario = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $idEvento,
    $idUsuario
);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        echo "ok";
    } else {
        echo "Evento não encontrado.";
    }

} else {

    echo "Erro: " . $stmt->error;

}

$stmt->close();
$conn->close();