<?php
session_start();
include("../../config/conexao.php");

/*
SESSION
*/
$idUsuario = $_SESSION["idUsuario"] ?? null;

/*
VALIDAÇÃO DE LOGIN
*/
if (!$idUsuario) {
    die("Usuário não autenticado.");
}

/*
DADOS
*/
$sistolica = $_POST["sistolica"] ?? null;
$diastolica = $_POST["diastolica"] ?? null;

$data = $_POST["data"] ?? null;
$hora = $_POST["hora"] ?? null;

$observacao = $_POST["observacoes"] ?? "";

/*
VALIDAÇÃO
*/
if (
    empty($sistolica) ||
    empty($diastolica) ||
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
INSERT NA NOVA TABELA
*/
$sql = "
INSERT INTO tblpressao
(
    Usuario_idUsuario,
    sistolica,
    diastolica,
    data_hora,
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
    "iiiss",
    $idUsuario,
    $sistolica,
    $diastolica,
    $dataHora,
    $observacao
);

/*
EXECUTA
*/
if ($stmt->execute()) {

    echo "
    <script>
        alert('Pressão registrada com sucesso!');
        window.location.href = '../dashboard.php';
    </script>
    ";

} else {

    echo "Erro ao salvar: " . $stmt->error;

}

$stmt->close();
$conn->close();
?>