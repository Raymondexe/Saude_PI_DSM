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

/* recusa convite */
$sql = $conn->prepare("
    UPDATE tblConvite
    SET statusConvite = 'recusado'
    WHERE idConvite = ?
    AND Usuario_idUsuario = ?
");

$sql->bind_param("ii", $idConvite, $idUsuario);

if (!$sql->execute()) {
    die("Erro ao recusar convite");
}

/* remove membro pendente */
$sqlDelete = $conn->prepare("
    DELETE FROM tblFamiliaUsuario
    WHERE Usuario_idUsuario = ?
    AND statusMembro = 'pendente'
");

$sqlDelete->bind_param("i", $idUsuario);
$sqlDelete->execute();

header("Location: ../../../perfil.php");
exit;
?>