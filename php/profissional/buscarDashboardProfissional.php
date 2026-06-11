<?php
session_start();
include("../config/conexao.php");

header('Content-Type: application/json');

$idProfissional = $_SESSION['idUsuario'] ?? 0;

if (!$idProfissional) {
    echo json_encode([
        "erro" => "Profissional não autenticado."
    ]);
    exit;
}

/*
=========================
CONSULTAS HOJE
=========================
*/

$sqlHoje = "
SELECT COUNT(*) total
FROM tblconsulta
WHERE Profissional_idUsuario = ?
AND DATE(dataConsulta) = CURDATE()
";

$stmt = $conn->prepare($sqlHoje);
$stmt->bind_param("i", $idProfissional);
$stmt->execute();

$consultasHoje = $stmt
    ->get_result()
    ->fetch_assoc()['total'] ?? 0;


/*
=========================
CONSULTAS SEMANA
=========================
*/

$sqlSemana = "
SELECT COUNT(*) total
FROM tblconsulta
WHERE Profissional_idUsuario = ?
AND YEARWEEK(dataConsulta,1)=YEARWEEK(CURDATE(),1)
";

$stmt = $conn->prepare($sqlSemana);
$stmt->bind_param("i", $idProfissional);
$stmt->execute();

$consultasSemana = $stmt
    ->get_result()
    ->fetch_assoc()['total'] ?? 0;


/*
=========================
PACIENTES ÚNICOS
=========================
*/

$sqlPacientes = "
SELECT COUNT(DISTINCT Paciente_idUsuario) total
FROM tblconsulta
WHERE Profissional_idUsuario = ?
AND YEARWEEK(dataConsulta,1)=YEARWEEK(CURDATE(),1)
";

$stmt = $conn->prepare($sqlPacientes);
$stmt->bind_param("i", $idProfissional);
$stmt->execute();

$pacientesSemana = $stmt
    ->get_result()
    ->fetch_assoc()['total'] ?? 0;


/*
=========================
PRÓXIMO COMPROMISSO
=========================
*/

$sqlEvento = "
SELECT *
FROM tblevento
WHERE Usuario_idUsuario = ?
AND dataEvento >= CURDATE()
ORDER BY dataEvento ASC, horarioEvento ASC
LIMIT 1
";

$stmt = $conn->prepare($sqlEvento);
$stmt->bind_param("i", $idProfissional);
$stmt->execute();

$evento = $stmt
    ->get_result()
    ->fetch_assoc();


/*
=========================
GRÁFICO 7 DIAS
=========================
*/

$sqlGrafico = "
SELECT
DATE(dataConsulta) dia,
COUNT(*) total
FROM tblconsulta
WHERE Profissional_idUsuario = ?
AND dataConsulta >= CURDATE() - INTERVAL 6 DAY
GROUP BY DATE(dataConsulta)
ORDER BY dia
";

$stmt = $conn->prepare($sqlGrafico);
$stmt->bind_param("i", $idProfissional);
$stmt->execute();

$result = $stmt->get_result();

$grafico = [];

while($linha = $result->fetch_assoc()){
    $grafico[] = $linha;
}


/*
=========================
RETORNO JSON
=========================
*/

echo json_encode([
    "consultasHoje" => $consultasHoje,
    "consultasSemana" => $consultasSemana,
    "pacientesSemana" => $pacientesSemana,
    "evento" => $evento,
    "grafico" => $grafico
]);