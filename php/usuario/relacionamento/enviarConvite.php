<?php
session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

$idResponsavel = $_SESSION['idUsuario'];

$nomeFamilia = trim($_POST['nomeFamilia'] ?? '');
$codigo = strtoupper(trim($_POST['codigoDependente'] ?? ''));

if (empty($nomeFamilia) || empty($codigo)) {
    die("Preencha todos os campos.");
}

if (!preg_match('/^BSTR-[A-Z0-9]{6}$/', $codigo)) {
    die("Código inválido.");
}

/* localizar dependente */
$stmt = $conn->prepare("
    SELECT idUsuario
    FROM tblUsuario
    WHERE codigoVinculo = ?
");
$stmt->bind_param("s", $codigo);
$stmt->execute();

$result = $stmt->get_result();
$dependente = $result->fetch_assoc();

if (!$dependente) {
    die("Usuário não encontrado.");
}

$idDependente = $dependente['idUsuario'];

if ($idDependente == $idResponsavel) {
    die("Você não pode convidar a si mesmo.");
}

/* limite 2 famílias */
$stmt = $conn->prepare("
    SELECT COUNT(*) total
    FROM tblFamiliaUsuario
    WHERE Usuario_idUsuario = ?
    AND papel = 'responsavel'
");
$stmt->bind_param("i", $idResponsavel);
$stmt->execute();

$total = $stmt->get_result()->fetch_assoc()['total'];

if ($total >= 2) {
    die("Você atingiu o limite de 2 famílias.");
}

/* cria família */
$stmt = $conn->prepare("
    INSERT INTO tblFamilia (nomeFamilia)
    VALUES (?)
");
$stmt->bind_param("s", $nomeFamilia);
$stmt->execute();

$idFamilia = $conn->insert_id;

/* responsável */
$stmt = $conn->prepare("
    INSERT INTO tblFamiliaUsuario
    (Familia_idFamilia, Usuario_idUsuario, papel, statusMembro)
    VALUES (?, ?, 'responsavel', 'ativo')
");
$stmt->bind_param("ii", $idFamilia, $idResponsavel);
$stmt->execute();

/* dependente pendente */
$stmt = $conn->prepare("
    INSERT INTO tblFamiliaUsuario
    (Familia_idFamilia, Usuario_idUsuario, papel, statusMembro)
    VALUES (?, ?, 'dependente', 'pendente')
");
$stmt->bind_param("ii", $idFamilia, $idDependente);
$stmt->execute();

/* convite */
$codigoConvite = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
$tipoConvite = "familia";
$validade = date("Y-m-d H:i:s", strtotime("+7 days"));

$stmt = $conn->prepare("
    INSERT INTO tblConvite
    (
        Usuario_idUsuario,
        Responsavel_idResponsavel,
        codigo,
        tipoConvite,
        validadeConvite,
        statusConvite
    )
    VALUES (?, ?, ?, ?, ?, 'pendente')
");

$stmt->bind_param(
    "iisss",
    $idDependente,
    $idResponsavel,
    $codigoConvite,
    $tipoConvite,
    $validade
);

$stmt->execute();

echo "ok";