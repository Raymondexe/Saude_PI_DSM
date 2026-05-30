<?php

header('Content-Type: application/json');

include("../config/conexao.php");

$idUsuario = $_POST['idUsuario'] ?? 0;

$sql = "

SELECT

    m.*,

    u.nomeUsuario,
    ps.crm,

    ps.especialidade

FROM tblmedicamento m

INNER JOIN tblProfissionalSaude ps
    ON ps.idProfissionalSaude =
       m.ProfissionalSaude_idProfissionalSaude

INNER JOIN tblUsuario u
    ON u.idUsuario =
       ps.Usuario_idUsuario

WHERE m.Usuario_idUsuario = ?
AND m.ativo = 1

ORDER BY m.criadoEm DESC

";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $idUsuario);

$stmt->execute();

$result = $stmt->get_result();

$medicamentos = [];

while ($row = $result->fetch_assoc()) {

    $row['criadoEm'] = date(
        "d/m/Y H:i",
        strtotime($row['criadoEm'])
    );

    $medicamentos[] = $row;
}

echo json_encode([

    "status" => count($medicamentos) > 0,

    "medicamentos" => $medicamentos

]);