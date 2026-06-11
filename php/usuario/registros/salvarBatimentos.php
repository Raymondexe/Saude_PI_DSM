<?php

session_start();

include("../../config/conexao.php");

/*
|--------------------------------------------------------------------------
| USUÁRIO LOGADO
|--------------------------------------------------------------------------
*/

$idResponsavel = $_SESSION["idUsuario"] ?? null;

if (!$idResponsavel) {

    die("Usuário não autenticado.");
}

/*
|--------------------------------------------------------------------------
| USUÁRIO SELECIONADO
|--------------------------------------------------------------------------
*/

$idUsuarioRegistro = $_POST["idUsuarioRegistro"] ?? null;

/*
|--------------------------------------------------------------------------
| DADOS
|--------------------------------------------------------------------------
*/

$bpm = $_POST["bpm"] ?? null;

$data = $_POST["data"] ?? null;
$hora = $_POST["hora"] ?? null;

$observacao = $_POST["observacoes"] ?? "";

/*
|--------------------------------------------------------------------------
| VALIDAÇÃO
|--------------------------------------------------------------------------
*/

if (
    empty($idUsuarioRegistro) ||
    empty($bpm) ||
    empty($data) ||
    empty($hora)
) {

    die("Preencha todos os campos.");
}

/*
|--------------------------------------------------------------------------
| VERIFICA PERMISSÃO
|--------------------------------------------------------------------------
*/

$permitido = false;

/* Pode registrar para si mesmo */

if ($idUsuarioRegistro == $idResponsavel) {

    $permitido = true;

} else {

    $sqlValidacao = "
    SELECT 1
    FROM tblfamiliausuario fuResp

    INNER JOIN tblfamiliausuario fuDep
        ON fuResp.Familia_idFamilia = fuDep.Familia_idFamilia

    WHERE fuResp.Usuario_idUsuario = ?
    AND fuResp.papel = 'responsavel'

    AND fuDep.Usuario_idUsuario = ?
    AND fuDep.papel = 'dependente'
    AND fuDep.statusMembro = 'ativo'

    LIMIT 1
    ";

    $stmtValida = $conn->prepare($sqlValidacao);

    if (!$stmtValida) {

        die("Erro SQL: " . $conn->error);
    }

    $stmtValida->bind_param(
        "ii",
        $idResponsavel,
        $idUsuarioRegistro
    );

    $stmtValida->execute();

    $permitido = $stmtValida
        ->get_result()
        ->num_rows > 0;

    $stmtValida->close();
}

if (!$permitido) {

    die("Usuário inválido.");
}

/*
|--------------------------------------------------------------------------
| DATA E HORA
|--------------------------------------------------------------------------
*/

$dataHora = $data . " " . $hora . ":00";

/*
|--------------------------------------------------------------------------
| INSERT
|--------------------------------------------------------------------------
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

$stmt->bind_param(
    "iiss",
    $idUsuarioRegistro,
    $bpm,
    $dataHora,
    $observacao
);

if ($stmt->execute()) {

    echo "Batimentos registrados com sucesso!";

} else {

    echo "Erro ao salvar: " . $stmt->error;
}

$stmt->close();

$conn->close();

?>