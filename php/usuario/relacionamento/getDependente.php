<?php
session_start();
include("../../config/conexao.php");

$idDependente = $_GET['id'] ?? 0;

if (!$idDependente) {
    die("Dependente inválido.");
}

/*
========================
BUSCA DADOS DO USUÁRIO
========================
*/
$sqlUsuario = "
SELECT *
FROM tblusuario
WHERE idUsuario = ?
";

$stmtUsuario = $conn->prepare($sqlUsuario);
if (!$stmtUsuario) {
    die($conn->error);
}

$stmtUsuario->bind_param("i", $idDependente);
$stmtUsuario->execute();

$resultUsuario = $stmtUsuario->get_result();

if ($resultUsuario->num_rows <= 0) {
    die("Usuário não encontrado.");
}

$usuario = $resultUsuario->fetch_assoc();

/*
========================
FOTO
========================
*/
$foto = "../../Img/defaultUser.png";

if (
    !empty($usuario['foto']) &&
    file_exists("../../uploads/" . $usuario['foto'])
) {
    $foto = "../../uploads/" . $usuario['foto'];
}

/*
========================
ÚLTIMA GLICEMIA
========================
*/
$sqlGlicemia = "
SELECT *
FROM tblglicemia
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtGlicemia = $conn->prepare($sqlGlicemia);
$stmtGlicemia->bind_param("i", $idDependente);
$stmtGlicemia->execute();

$ultimaGlicemia = $stmtGlicemia
    ->get_result()
    ->fetch_assoc();

/*
========================
ÚLTIMA PRESSÃO
========================
*/
$sqlPressao = "
SELECT *
FROM tblpressao
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtPressao = $conn->prepare($sqlPressao);
$stmtPressao->bind_param("i", $idDependente);
$stmtPressao->execute();

$ultimaPressao = $stmtPressao
    ->get_result()
    ->fetch_assoc();

/*
========================
ÚLTIMA TEMPERATURA
========================
*/
$sqlTemperatura = "
SELECT *
FROM tbltemperatura
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtTemperatura = $conn->prepare($sqlTemperatura);
$stmtTemperatura->bind_param("i", $idDependente);
$stmtTemperatura->execute();

$ultimaTemperatura = $stmtTemperatura
    ->get_result()
    ->fetch_assoc();

/*
========================
ÚLTIMO BATIMENTO
========================
*/
$sqlBatimento = "
SELECT *
FROM tblbatimentos
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtBatimento = $conn->prepare($sqlBatimento);
$stmtBatimento->bind_param("i", $idDependente);
$stmtBatimento->execute();

$ultimoBatimento = $stmtBatimento
    ->get_result()
    ->fetch_assoc();

/*
========================
PRÓXIMO EVENTO
========================
*/
$sqlEvento = "
SELECT *
FROM tblevento
WHERE Usuario_idUsuario = ?
AND dataEvento >= CURDATE()
ORDER BY dataEvento ASC, horarioEvento ASC
LIMIT 1
";

$stmtEvento = $conn->prepare($sqlEvento);
$stmtEvento->bind_param("i", $idDependente);
$stmtEvento->execute();

$proximoEvento = $stmtEvento
    ->get_result()
    ->fetch_assoc();

?>
