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
$bpm = $_POST["bpm"] ?? null;

$data = $_POST["data"] ?? null;
$hora = $_POST["hora"] ?? null;

$observacao = $_POST["observacoes"] ?? "";

/*
VALIDAÇÃO
*/
if (
    empty($bpm) ||
    empty($data) ||
    empty($hora)
) {
    die("Preencha todos os campos.");
}

/*
JUNTA DATA + HORA
*/
$dataHora = $data . " " . $hora . ":00";

/*
INSERT
*/
$sql = "
INSERT INTO tblbatimentos
(
    Usuario_idUsuario,
    bpm,
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
    "iiss",
    $idUsuario,
    $bpm,
    $dataHora,
    $observacao
);

/*
EXECUTA
*/
if ($stmt->execute()) {

    echo "
    <script>
        alert('Batimentos registrados com sucesso!');
        window.location.href = '../../../batimentosCardiacos.php';
    </script>
    ";

} else {

    die('Erro execute: ' . $stmt->error);

}

$stmt->close();
$conn->close();
?>