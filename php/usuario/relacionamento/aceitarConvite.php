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

/* busca convite */
$stmt = $conn->prepare("
    SELECT *
    FROM tblConvite
    WHERE idConvite = ?
    AND Usuario_idUsuario = ?
");

$stmt->bind_param("ii", $idConvite, $idUsuario);
$stmt->execute();

$result = $stmt->get_result();
$convite = $result->fetch_assoc();

if (!$convite) {
    die("Convite não encontrado.");
}

$idResponsavel = $convite['Responsavel_idResponsavel'];

/* descobre a família do responsável */
$stmt = $conn->prepare("
    SELECT fu.Familia_idFamilia
    FROM tblFamiliaUsuario fu
    INNER JOIN tblResponsavel r
        ON r.Login_Usuario_idUsuario = fu.Usuario_idUsuario
    WHERE r.idResponsavel = ?
    AND fu.papel = 'responsavel'
    LIMIT 1
");

$stmt->bind_param("i", $idResponsavel);
$stmt->execute();

$result = $stmt->get_result();
$familia = $result->fetch_assoc();

if (!$familia) {
    die("Família não encontrada.");
}

$idFamilia = $familia['Familia_idFamilia'];

/* cria vínculo direto */
$stmt = $conn->prepare("
    INSERT INTO tblFamiliaUsuario
    (
        Familia_idFamilia,
        Usuario_idUsuario,
        papel,
        statusMembro
    )
    VALUES (?, ?, 'dependente', 'ativo')
");

$stmt->bind_param("ii", $idFamilia, $idUsuario);

if (!$stmt->execute()) {
    die("Erro ao adicionar membro: " . $stmt->error);
}

/* aceita convite */
$stmt = $conn->prepare("
    UPDATE tblConvite
    SET statusConvite = 'ACEITO'
    WHERE idConvite = ?
");

$stmt->bind_param("i", $idConvite);

if (!$stmt->execute()) {
    die("Erro ao atualizar convite.");
}

header("Location: ../../../perfil.php");
exit;
?>