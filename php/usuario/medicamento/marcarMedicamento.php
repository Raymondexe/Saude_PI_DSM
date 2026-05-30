<?php

session_start();

include("../../config/conexao.php");

$idUsuario = $_SESSION['idUsuario'];

$idMedicamento = $_POST['idMedicamento'];
$status = $_POST['status'];

$sql = "
INSERT INTO tblmedicamento_registro
(
    Medicamento_idMedicamento,
    Usuario_idUsuario,
    dataRegistro,
    statusMedicamento,
    horarioRegistro
)
VALUES
(
    ?,
    ?,
    CURDATE(),
    ?,
    NOW()
)
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iis",
    $idMedicamento,
    $idUsuario,
    $status
);

if($stmt->execute())
{
    echo "Medicamento registrado!";
}
else
{
    echo "Erro ao registrar.";
}