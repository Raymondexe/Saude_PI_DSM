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
$valorGlicemia = $_POST["valorGlicemia"] ?? null;
$tipoMedicao = $_POST["tipoMedicao"] ?? null;

$data = $_POST["data"] ?? null;
$hora = $_POST["hora"] ?? null;

$observacao = $_POST["observacoes"] ?? "";

/*
VALIDAÇÃO
*/
if (
    empty($valorGlicemia) ||
    empty($tipoMedicao) ||
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
INSERT INTO tblglicemia
(
    Usuario_idUsuario,
    data_hora,
    valor_glicemia,
    tipo_medicao,
    observacao
)
VALUES
(
    ?,
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
    "isiss",
    $idUsuario,
    $dataHora,
    $valorGlicemia,
    $tipoMedicao,
    $observacao
);

/*
EXECUTA
*/
if ($stmt->execute()) {

    echo "
    <script>
        alert('Glicemia registrada com sucesso!');
        window.location.href = '../../../glicemia.php';
    </script>
    ";

} else {

    die('Erro execute: ' . $stmt->error);

}

$stmt->close();
$conn->close();
?>