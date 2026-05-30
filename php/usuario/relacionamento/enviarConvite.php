<?php
session_start();
include("../../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("1 - Usuário não autenticado.");
}

$idUsuarioLogado = $_SESSION['idUsuario'];
$idFamilia = $_POST['idFamilia'] ?? null;
$codigo = strtoupper(trim($_POST['codigoDependente'] ?? ''));

if (!$idFamilia || empty($codigo)) {
    die("2 - Dados inválidos.");
}

/*
|--------------------------------------------------------------------------
| BUSCA O ID DO RESPONSÁVEL REAL
|--------------------------------------------------------------------------
*/


$stmt = $conn->prepare("
    SELECT idResponsavel
FROM tblResponsavel
WHERE Login_Usuario_idUsuario = ?
");

if (!$stmt) {
    die("Erro ao preparar busca responsável: " . $conn->error);
}

$stmt->bind_param("i", $idUsuarioLogado);
$stmt->execute();

$responsavel = $stmt->get_result()->fetch_assoc();

if (!$responsavel) {
    die("Responsável não encontrado.");
}

$idResponsavel = $responsavel['idResponsavel'];

/*
|--------------------------------------------------------------------------
| BUSCA O DEPENDENTE PELO CÓDIGO
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT idUsuario
    FROM tblUsuario
    WHERE codigoVinculo = ?
");

if (!$stmt) {
    die("3 - Erro prepare usuário: " . $conn->error);
}

$stmt->bind_param("s", $codigo);
$stmt->execute();

$dependente = $stmt->get_result()->fetch_assoc();

if (!$dependente) {
    die("4 - Usuário não encontrado.");
}

$idDependente = $dependente['idUsuario'];

/*
|--------------------------------------------------------------------------
| INSERE CONVITE
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    INSERT INTO tblConvite
(
    Usuario_idUsuario,
    Responsavel_idResponsavel,
    Familia_idFamilia,
    codigo,
    tipoConvite,
    validadeConvite,
    statusConvite
)
VALUES (?, ?, ?, ?, 'familia', ?, 'PENDENTE')
");

if (!$stmt) {
    die("5 - Erro prepare convite: " . $conn->error);
}

$codigoConvite = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
$validade = date("Y-m-d H:i:s", strtotime("+7 days"));

$stmt->bind_param(
    "iiiss",
    $idDependente,
    $idResponsavel,
    $idFamilia,
    $codigoConvite,
    $validade
);

if (!$stmt->execute()) {
    die("6 - Erro execute convite: " . $stmt->error);
}

$mensagem = "Você recebeu um convite para entrar em uma família.";
$dataHora = date("Y-m-d H:i:s");

$stmt = $conn->prepare("
    INSERT INTO tblNotificacao
    (
        Usuario_idUsuario,
        Responsavel_idResponsavel,
        data_hora,
        mensagem,
        statusNotificacao
    )
    VALUES (?, ?, ?, ?, 'PENDENTE')
");

if (!$stmt) {
    die("Erro prepare notificacao: " . $conn->error);
}

$stmt->bind_param(
    "iiss",
    $idDependente,
    $idResponsavel,
    $dataHora,
    $mensagem
);

if (!$stmt->execute()) {
    die("Erro execute notificacao: " . $stmt->error);
}

echo "Notificação criada com sucesso";
?>