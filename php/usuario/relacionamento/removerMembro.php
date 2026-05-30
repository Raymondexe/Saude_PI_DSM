<?php
include("../../config/conexao.php");

$id = $_POST['idFamiliaUsuario'] ?? null;

$stmt = $conn->prepare("
    SELECT papel
    FROM tblFamiliaUsuario
    WHERE Usuario_idUsuario = ?
    AND Familia_idFamilia = ?
");
$stmt->bind_param("ii", $idUsuarioRemover, $idFamilia);
$stmt->execute();

$resultado = $stmt->get_result()->fetch_assoc();

if ($resultado && $resultado['papel'] === 'responsavel') {
    die("Não é permitido remover o responsável.");
}
?>