<?php

header('Content-Type: application/json');

include("../config/conexao.php");

$idUsuario = $_POST['idUsuario'] ?? 0;

$historico = [];



/* =========================
   BATIMENTOS
========================= */

$sql = "
SELECT
    'Batimentos' AS tipo,
    CONCAT(bpm, ' BPM') AS valor,
    data_hora,
    observacao
FROM tblbatimentos
WHERE Usuario_idUsuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $historico[] = [
        "tipo" => $row['tipo'],
        "valor" => $row['valor'],
        "data" => $row['data_hora'],
        "observacao" => $row['observacao']
    ];
}



/* =========================
   GLICEMIA
========================= */

$sql = "
SELECT
    'Glicemia' AS tipo,
    CONCAT(valor_glicemia, ' mg/dL') AS valor,
    data_hora,
    observacao
FROM tblglicemia
WHERE Usuario_idUsuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $historico[] = [
        "tipo" => $row['tipo'],
        "valor" => $row['valor'],
        "data" => $row['data_hora'],
        "observacao" => $row['observacao']
    ];
}



/* =========================
   PRESSÃO
========================= */

$sql = "
SELECT
    'Pressão' AS tipo,
    CONCAT(sistolica, '/', diastolica) AS valor,
    data_hora,
    observacao
FROM tblpressao
WHERE Usuario_idUsuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $historico[] = [
        "tipo" => $row['tipo'],
        "valor" => $row['valor'],
        "data" => $row['data_hora'],
        "observacao" => $row['observacao']
    ];
}



/* =========================
   TEMPERATURA
========================= */

$sql = "
SELECT
    'Temperatura' AS tipo,
    CONCAT(temperatura, ' °C') AS valor,
    data_hora,
    observacao
FROM tbltemperatura
WHERE Usuario_idUsuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $historico[] = [
        "tipo" => $row['tipo'],
        "valor" => $row['valor'],
        "data" => $row['data_hora'],
        "observacao" => $row['observacao']
    ];
}



/* =========================
   ORDENAR POR DATA
========================= */

usort($historico, function($a, $b) {

    return strtotime($b['data']) -
           strtotime($a['data']);
});



/* =========================
   FORMATAR DATA
========================= */

foreach ($historico as &$item) {

    $item['data'] = date(
        "d/m/Y H:i",
        strtotime($item['data'])
    );
}



/* =========================
   RETORNO
========================= */

echo json_encode([
    "status" => count($historico) > 0,
    "historico" => $historico
]);