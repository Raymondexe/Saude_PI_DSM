<?php

header('Content-Type: application/json');

include("../config/conexao.php");

$dados = json_decode(
    file_get_contents("php://input"),
    true
);

$sql = "

UPDATE tblmedicamento

SET

    nomeMedicamento = ?,
    dosagem = ?,
    viaAdministracao = ?,
    finalidade = ?,
    horario = ?,
    frequencia = ?,
    observacao = ?,
    usoContinuo = ?

WHERE idMedicamento = ?

";

$stmt = $conn->prepare($sql);

$stmt->bind_param(

    "sssssssii",

    $dados['nomeMedicamento'],
    $dados['dosagem'],
    $dados['viaAdministracao'],
    $dados['finalidade'],
    $dados['horario'],
    $dados['frequencia'],
    $dados['observacao'],
    $dados['usoContinuo'],
    $dados['idMedicamento']

);

if ($stmt->execute()) {

    echo json_encode([
        "status" => true
    ]);

} else {

    echo json_encode([
        "status" => false,
        "mensagem" => $stmt->error
    ]);

}