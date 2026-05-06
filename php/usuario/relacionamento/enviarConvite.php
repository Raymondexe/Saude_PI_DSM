<?php
session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acesso inválido.");
}

$idUsuario = $_SESSION['idUsuario'];
$codigo = strtoupper(trim($_POST['codigoDependente'] ?? ''));

/* =========================
   1. VALIDAR FORMATO
========================= */
if (!preg_match('/^BSTR-[A-Z0-9]{6}$/', $codigo)) {
    die("Código inválido.");
}

/* =========================
   2. PEGAR CÓDIGO DO USUÁRIO LOGADO
========================= */
$stmt = $conn->prepare("
    SELECT codigoVinculo
    FROM tblUsuario
    WHERE idUsuario = ?
");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();
$usuarioLogado = $result->fetch_assoc();

if (!$usuarioLogado) {
    die("Usuário não encontrado.");
}

$meuCodigo = $usuarioLogado['codigoVinculo'];

/* =========================
   3. IMPEDIR AUTO CONVITE
========================= */
if ($codigo === $meuCodigo) {
    die("Você não pode enviar convite para si mesmo.");
}

/* =========================
   4. VERIFICAR EXISTÊNCIA DO DESTINATÁRIO
========================= */
$stmt = $conn->prepare("
    SELECT idUsuario
    FROM tblUsuario
    WHERE codigoVinculo = ?
");
$stmt->bind_param("s", $codigo);
$stmt->execute();

$result = $stmt->get_result();
$destinatario = $result->fetch_assoc();

if (!$destinatario) {
    die("Código não encontrado.");
}

$idDestino = $destinatario['idUsuario'];

/* =========================
   5. IMPEDIR DUPLICIDADE DE RELAÇÃO
========================= */
$stmt = $conn->prepare("
    SELECT *
    FROM tblVerificaResponsavel
    WHERE Usuario_idUsuario = ?
");
$stmt->bind_param("i", $idDestino);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Esse usuário já possui vínculo.");
}

/* =========================
   6. IMPEDIR CONVITE PENDENTE DUPLICADO
========================= */
$stmt = $conn->prepare("
    SELECT *
    FROM tblConvite
    WHERE Usuario_idUsuario = ?
    AND statusConvite = 'pendente'
");
$stmt->bind_param("i", $idDestino);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Já existe convite pendente para esse usuário.");
}

/* =========================
   7. GERAR CONVITE
========================= */
$codigoConvite = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
$tipoConvite = "dependente_para_responsavel";
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
    VALUES (?, NULL, ?, ?, ?, 'pendente')
");

$stmt->bind_param(
    "isss",
    $idDestino,
    $codigoConvite,
    $tipoConvite,
    $validade
);

if ($stmt->execute()) {
    header("Location: ../../perfil.php?sucesso=convite_enviado");
    exit;
} else {
    die("Erro ao enviar convite.");
}
?>