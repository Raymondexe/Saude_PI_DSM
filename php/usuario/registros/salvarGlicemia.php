<?php

session_start();

include("../../config/conexao.php");

/*
RESPONSÁVEL LOGADO
*/
$idResponsavel = $_SESSION["idUsuario"] ?? null;

if (!$idResponsavel) {
    die("Usuário não autenticado.");
}

/*
USUÁRIO QUE RECEBERÁ O REGISTRO
*/
$idUsuarioRegistro =
    $_POST["idUsuarioRegistro"] ?? $idResponsavel;

/*
DADOS
*/
$valorGlicemia =
    $_POST["valorGlicemia"] ?? null;

$tipoMedicao =
    $_POST["tipoMedicao"] ?? null;

$data =
    $_POST["data"] ?? null;

$hora =
    $_POST["hora"] ?? null;

$observacao =
    $_POST["observacoes"] ?? "";

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
VALIDA RESPONSÁVEL/DEPENDENTE
*/

$ehDependente = false;

if ($idUsuarioRegistro != $idResponsavel) {

    $sqlValidacao = "
    SELECT 1
    FROM tblfamiliausuario fuResp

    INNER JOIN tblfamiliausuario fuDep
        ON fuResp.Familia_idFamilia =
           fuDep.Familia_idFamilia

    WHERE fuResp.Usuario_idUsuario = ?
    AND fuResp.papel = 'responsavel'

    AND fuDep.Usuario_idUsuario = ?
    AND fuDep.papel = 'dependente'

    LIMIT 1
    ";

    $stmtValida =
        $conn->prepare($sqlValidacao);

    $stmtValida->bind_param(
        "ii",
        $idResponsavel,
        $idUsuarioRegistro
    );

    $stmtValida->execute();

    $ehDependente =
        $stmtValida
            ->get_result()
            ->num_rows > 0;
}

if (
    $idUsuarioRegistro != $idResponsavel &&
    !$ehDependente
) {
    die("Usuário inválido.");
}

/*
DATA + HORA
*/
$dataHora =
    $data . " " . $hora . ":00";

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
    $idUsuarioRegistro,
    $dataHora,
    $valorGlicemia,
    $tipoMedicao,
    $observacao
);

/*
EXECUTA
*/
if ($stmt->execute()) {

    echo "sucesso";

} else {

    echo "Erro ao salvar: " . $stmt->error;

}

$stmt->close();
$conn->close();

?>