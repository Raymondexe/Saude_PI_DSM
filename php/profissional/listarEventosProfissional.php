<?php

session_start();
include("../config/conexao.php");

header('Content-Type: application/json');

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode([]);
    exit;
}

$idUsuario = $_SESSION['idUsuario'];

$sql = "
SELECT
    idEvento,
    tipoEvento,
    localEvento,
    horarioEvento,
    dataEvento
FROM tblevento
WHERE Usuario_idUsuario = ?
ORDER BY dataEvento ASC, horarioEvento ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

$eventos = [];

while ($evento = $result->fetch_assoc()) {
    $eventos[] = $evento;
}

echo json_encode($eventos);

$stmt->close();
$conn->close();