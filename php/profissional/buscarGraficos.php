<?php

header('Content-Type: application/json');

include("../config/conexao.php");

$idUsuario = $_POST['idUsuario'] ?? 0;

$dadosGrafico = [

    "batimentos" => [],
    "temperatura" => [],
    "glicemia" => [],
    "pressao" => []

];


/*
BATIMENTOS
*/

$sql = "
SELECT 
    DATE(data_hora) as data,
    bpm
FROM tblbatimentos
WHERE Usuario_idUsuario = ?
ORDER BY data_hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $dadosGrafico["batimentos"][] = [
        "data" => $row["data"],
        "valor" => $row["bpm"]
    ];

}


/*
TEMPERATURA
*/

$sql = "
SELECT 
    DATE(data_hora) as data,
    temperatura
FROM tbltemperatura
WHERE Usuario_idUsuario = ?
ORDER BY data_hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $dadosGrafico["temperatura"][] = [
        "data" => $row["data"],
        "valor" => $row["temperatura"]
    ];

}


/*
GLICEMIA
*/

$sql = "
SELECT 
    DATE(data_hora) as data,
    valor_glicemia
FROM tblglicemia
WHERE Usuario_idUsuario = ?
ORDER BY data_hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $dadosGrafico["glicemia"][] = [
        "data" => $row["data"],
        "valor" => $row["valor_glicemia"]
    ];

}


/*
PRESSÃO
*/

$sql = "
SELECT 
    DATE(data_hora) as data,
    sistolica,
    diastolica
FROM tblpressao
WHERE Usuario_idUsuario = ?
ORDER BY data_hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $dadosGrafico["pressao"][] = [
        "data" => $row["data"],
        "sistolica" => $row["sistolica"],
        "diastolica" => $row["diastolica"]
    ];

}

echo json_encode([
    "status" => true,
    "dados" => $dadosGrafico
]);