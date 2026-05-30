<?php

session_start();

header('Content-Type: application/json');

include("../config/conexao.php");

$idUsuarioLogado = $_SESSION['idUsuario'] ?? 0;

if (!$idUsuarioLogado) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Usuário não autenticado."
    ]);

    exit;
}


/* =========================
   BUSCAR PROFISSIONAL
========================= */

$sqlProfissional = "

SELECT idProfissionalSaude

FROM tblProfissionalSaude

WHERE Usuario_idUsuario = ?

LIMIT 1

";

$stmtProf = $conn->prepare($sqlProfissional);

$stmtProf->bind_param("i", $idUsuarioLogado);

$stmtProf->execute();

$resultProf = $stmtProf->get_result();

$profissional = $resultProf->fetch_assoc();

if (!$profissional) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Profissional não encontrado."
    ]);

    exit;
}

$idProfissional = $profissional['idProfissionalSaude'];


/* =========================
   RECEBER DADOS
========================= */

$dados = json_decode(file_get_contents("php://input"), true);

$idUsuario = $dados['idUsuario'] ?? 0;

$nomeMedicamento = trim($dados['nomeMedicamento'] ?? '');
$dosagem = trim($dados['dosagem'] ?? '');
$viaAdministracao = trim($dados['viaAdministracao'] ?? '');
$finalidade = trim($dados['finalidade'] ?? '');
$horario = trim($dados['horario'] ?? '');
$frequencia = trim($dados['frequencia'] ?? '');
$observacao = trim($dados['observacao'] ?? '');
$usoContinuo = $dados['usoContinuo'] ?? 0;


/* =========================
   VALIDAÇÃO
========================= */

if (
    empty($idUsuario) ||
    empty($nomeMedicamento)
) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Preencha os campos obrigatórios."
    ]);

    exit;
}


/* =========================
   INSERT
========================= */

$sql = "

INSERT INTO tblmedicamento (

    Usuario_idUsuario,
    ProfissionalSaude_idProfissionalSaude,
    nomeMedicamento,
    dosagem,
    viaAdministracao,
    finalidade,
    horario,
    frequencia,
    observacao,
    usoContinuo,
    ativo,
    criadoEm

) VALUES (

    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW()

)

";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "status" => false,
        "mensagem" => $conn->error
    ]);

    exit;
}

$stmt->bind_param(

    "iisssssssi",

    $idUsuario,
    $idProfissional,
    $nomeMedicamento,
    $dosagem,
    $viaAdministracao,
    $finalidade,
    $horario,
    $frequencia,
    $observacao,
    $usoContinuo

);


/* =========================
   EXECUTAR
========================= */

if ($stmt->execute()) {

    echo json_encode([
        "status" => true,
        "mensagem" => "Medicamento salvo com sucesso."
    ]);

} else {

    echo json_encode([
        "status" => false,
        "mensagem" => $stmt->error
    ]);

}
?>