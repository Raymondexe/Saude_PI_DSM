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
$idConvite = intval($_POST['idConvite'] ?? 0);

if ($idConvite <= 0) {
    die("Convite inválido.");
}

/* =========================
   1. BUSCAR CONVITE
========================= */
$stmt = $conn->prepare("
    SELECT *
    FROM tblConvite
    WHERE idConvite = ?
    AND Usuario_idUsuario = ?
    AND statusConvite = 'PENDENTE'
");
$stmt->bind_param("ii", $idConvite, $idUsuario);
$stmt->execute();

$result = $stmt->get_result();
$convite = $result->fetch_assoc();

if (!$convite) {
    die("Convite não encontrado ou já processado.");
}

/* =========================
   2. VALIDAR EXPIRAÇÃO
========================= */
$dataAtual = date("Y-m-d H:i:s");

if ($convite['validadeConvite'] < $dataAtual) {
    die("Este convite expirou.");
}

/* =========================
   3. IMPEDIR DUPLICIDADE
========================= */
$stmt = $conn->prepare("
    SELECT *
    FROM tblVerificaResponsavel
    WHERE Usuario_idUsuario = ?
");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Você já possui vínculo.");
}

/* =========================
   4. CRIAR VÍNCULO
========================= */
$stmt = $conn->prepare("
    INSERT INTO tblVerificaResponsavel
    (
        Usuario_idUsuario,
        Responsavel_idResponsavel
    )
    VALUES (?, ?)
");

$stmt->bind_param(
    "ii",
    $idUsuario,
    $convite['Responsavel_idResponsavel']
);

if (!$stmt->execute()) {
    die("Erro ao criar vínculo.");
}

/* =========================
   5. ATUALIZAR STATUS
========================= */
$stmt = $conn->prepare("
    UPDATE tblConvite
    SET statusConvite = 'ACEITO'
    WHERE idConvite = ?
");
$stmt->bind_param("i", $idConvite);

if (!$stmt->execute()) {
    die("Erro ao atualizar convite.");
}

/* =========================
   6. REDIRECIONAR
========================= */
header("Location: ../../perfil.php?sucesso=convite_aceito");
exit;
?>