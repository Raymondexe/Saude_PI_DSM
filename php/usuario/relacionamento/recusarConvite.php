<?php
session_start();
include("../../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

$idUsuario = $_SESSION['idUsuario'];
$idConvite = intval($_POST['idConvite'] ?? 0);

/* recusa convite */
$stmt = $conn->prepare("
    UPDATE tblConvite
    SET statusConvite = 'RECUSADO'
    WHERE idConvite = ?
    AND Usuario_idUsuario = ?
");
$stmt->bind_param("ii", $idConvite, $idUsuario);
$stmt->execute();

/* remove vínculo pendente */
$stmt = $conn->prepare("
    DELETE FROM tblFamiliaUsuario
    WHERE Usuario_idUsuario = ?
    AND statusMembro = 'pendente'
");
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

echo "ok";
?>