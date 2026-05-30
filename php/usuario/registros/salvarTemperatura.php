<?php
session_start();
include("../../config/conexao.php");

/*
SESSION
*/
$idUsuario = $_SESSION["idUsuario"];

/*
DADOS
*/
$temperatura = $_POST["valorTemperatura"] ?? null;

$data = $_POST["data"] ?? null;
$hora = $_POST["hora"] ?? null;

$observacao = $_POST["observacoes"] ?? "";

/*
VALIDAÇÃO
*/
if (
    empty($temperatura) ||
    empty($data) ||
    empty($hora)
) {
    die("Preencha todos os campos.");
}

/*
DATA + HORA
*/
$dataHora = $data . " " . $hora . ":00";

/*
INSERT
*/
$sql = "
INSERT INTO tbltemperatura
(
    Usuario_idUsuario,
    temperatura,
    data_hora,
    observacao
)
VALUES
(
    ?,
    ?,
    ?,
    ?
)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro no prepare: " . $conn->error);
}

/*
BIND
*/
$stmt->bind_param(
    "idss",
    $idUsuario,
    $temperatura,
    $dataHora,
    $observacao
);

/*
EXECUTA
*/
if ($stmt->execute()) {

    echo "
    <script>
        alert('Temperatura registrada com sucesso!');
        window.location.href='../../../temperatura.php';
    </script>
    ";

} else {

    die('Erro execute: ' . $stmt->error);

}

$stmt->close();
$conn->close();
?>