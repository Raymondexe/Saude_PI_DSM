<?php
session_start();
include("../../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

$idUsuario = $_SESSION['idUsuario'];
$idConvite = $_POST['idConvite'] ?? null;

if (!$idConvite) {
    die("Convite inválido.");
}

/* =========================
   BUSCA CONVITE
========================= */
$sqlConvite = $conn->prepare("
    SELECT 
        idConvite,
        Responsavel_idResponsavel,
        Usuario_idUsuario,
        statusConvite
    FROM tblConvite
    WHERE idConvite = ?
    AND Usuario_idUsuario = ?
    LIMIT 1
");

$sqlConvite->bind_param("ii", $idConvite, $idUsuario);
$sqlConvite->execute();

$resultConvite = $sqlConvite->get_result();
$convite = $resultConvite->fetch_assoc();

if (!$convite) {
    die("Convite não encontrado.");
}

if ($convite['statusConvite'] !== 'pendente') {
    die("Convite já processado.");
}

$idResponsavel = $convite['Responsavel_idResponsavel'];

/* =========================
   ACEITA CONVITE
========================= */
$sqlAceitar = $conn->prepare("
    UPDATE tblConvite
    SET statusConvite = 'aceito'
    WHERE idConvite = ?
");

$sqlAceitar->bind_param("i", $idConvite);

if (!$sqlAceitar->execute()) {
    die("Erro ao aceitar convite.");
}


/* =========================
   DESCOBRE FAMÍLIA DO RESPONSÁVEL
========================= */
$sqlFamilia = $conn->prepare("
    SELECT fu.Familia_idFamilia
    FROM tblFamiliaUsuario fu
    INNER JOIN tblResponsavel r
        ON r.Login_Usuario_idUsuario = fu.Usuario_idUsuario
    WHERE r.idResponsavel = ?
    AND fu.papel = 'responsavel'
    LIMIT 1
");

$sqlFamilia->bind_param("i", $idResponsavel);
$sqlFamilia->execute();

$resultFamilia = $sqlFamilia->get_result();
$familia = $resultFamilia->fetch_assoc();

if (!$familia) {
    die("Família não encontrada.");
}

$idFamilia = $familia['Familia_idFamilia'];


/* =========================
   ATIVA MEMBRO
========================= */
$sqlAtivar = $conn->prepare("
    UPDATE tblFamiliaUsuario
    SET statusMembro = 'ativo'
    WHERE Usuario_idUsuario = ?
    AND Familia_idFamilia = ?
");

$sqlAtivar->bind_param("ii", $idUsuario, $idFamilia);

if (!$sqlAtivar->execute()) {
    die("Erro ao ativar membro.");
}

header("Location: ../../../perfil.php");
exit;
?>