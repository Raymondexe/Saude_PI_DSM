<?php
session_start();
include("../../config/conexao.php");

$idSessao = $_SESSION['idUsuario'];
$idUsuario = $_GET['usuario'] ?? $idSessao;

/* verifica papel do usuário logado */
$stmt = $conn->prepare("
    SELECT papel
    FROM tblFamiliaUsuario
    WHERE Usuario_idUsuario = ?
    LIMIT 1
");

$stmt->bind_param("i", $idSessao);
$stmt->execute();

$result = $stmt->get_result()->fetch_assoc();
$papel = $result['papel'] ?? '';

/* dependente só pode acessar própria agenda */
if ($papel !== 'responsavel' && $idSessao != $idUsuario) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

/* busca eventos */
$stmt = $conn->prepare("
    SELECT *
    FROM tblEvento
    WHERE Usuario_idUsuario = ?
    ORDER BY dataEvento, horarioEvento
");

$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

$eventos = [];

while ($row = $result->fetch_assoc()) {

    $data = $row['dataEvento'];

    if (!isset($eventos[$data])) {
        $eventos[$data] = [];
    }

    $eventos[$data][] = [
        "id" => $row["idEvento"],
        "local" => $row["localEvento"],
        "tipo" => $row["tipoEvento"],
        "medico" => $row["medicoEvento"],
        "horario" => $row["horarioEvento"],
        "levar" => explode(",", $row["levarEvento"])
    ];
}

header('Content-Type: application/json');
echo json_encode($eventos);
?>