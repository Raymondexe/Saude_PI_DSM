<?php

session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

$idUsuario = $_SESSION['idUsuario'];

$titulo       = trim($_POST['titulo'] ?? '');
$tipoEvento   = trim($_POST['tipoEvento'] ?? '');
$dataEvento   = $_POST['dataEvento'] ?? '';
$horarioEvento = $_POST['horarioEvento'] ?? '';
$localEvento  = trim($_POST['localEvento'] ?? '');

if (
    empty($titulo) ||
    empty($tipoEvento) ||
    empty($dataEvento) ||
    empty($horarioEvento)
) {
    die("Preencha todos os campos.");
}

$sql = "
INSERT INTO tblevento
(
    Usuario_idUsuario,
    localEvento,
    tipoEvento,
    medicoEvento,
    horarioEvento,
    dataEvento
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?,
    ?
)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die($conn->error);
}

$stmt->bind_param(
    "isssss",
    $idUsuario,
    $localEvento,
    $tipoEvento,
    $titulo,
    $horarioEvento,
    $dataEvento
);

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();