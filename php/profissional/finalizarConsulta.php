<?php
session_start();

include("../config/conexao.php");

if (!isset($_SESSION["idUsuario"])) {
    die("Usuário não autenticado.");
}

$idProfissional = $_SESSION["idUsuario"];

$idPaciente = $_POST["paciente"] ?? null;

$observacoes = trim(
    $_POST["observacoes"] ?? ""
);

if (empty($idPaciente)) {
    die("Paciente inválido.");
}


/*
BUSCA O ID DA TABELA PROFISSIONAL
*/

$sqlProfissional = "
SELECT idProfissionalSaude
FROM tblprofissionalsaude
WHERE Usuario_idUsuario = ?
LIMIT 1
";

$stmtProf = $conn->prepare($sqlProfissional);

$stmtProf->bind_param(
    "i",
    $idProfissional
);

$stmtProf->execute();

$profissional = $stmtProf
    ->get_result()
    ->fetch_assoc();

if (!$profissional) {
    die("Profissional não encontrado.");
}

$idProfissionalSaude =
    $profissional["idProfissionalSaude"];


/*
SALVA CONSULTA
*/

$sql = "
INSERT INTO tblconsulta
(
    Profissional_idUsuario,
    Paciente_idUsuario,
    observacoes,
    statusConsulta,
    ProfissionalSaude_idProfissionalSaude
)
VALUES
(
    ?,
    ?,
    ?,
    'FINALIZADA',
    ?
)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die($conn->error);
}

$stmt->bind_param(
    "iisi",
    $idProfissional,
    $idPaciente,
    $observacoes,
    $idProfissionalSaude
);

if ($stmt->execute()) {

    echo "sucesso";

} else {

    echo $stmt->error;

}

$stmt->close();

$conn->close();
?>