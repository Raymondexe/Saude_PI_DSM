<?php

header('Content-Type: application/json');

include("../config/conexao.php");

$dados = json_decode(
    file_get_contents("php://input"),
    true
);

$idMedicamento =
    $dados['idMedicamento'] ?? 0;

if (!$idMedicamento) {

    echo json_encode([
        "status" => false,
        "mensagem" => "ID inválido."
    ]);

    exit;
}

$sql = "

DELETE FROM tblMedicamento

WHERE idMedicamento = ?

";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $idMedicamento);

if ($stmt->execute()) {

    echo json_encode([
        "status" => true
    ]);

} else {

    echo json_encode([
        "status" => false,
        "mensagem" => "Erro ao excluir."
    ]);

}