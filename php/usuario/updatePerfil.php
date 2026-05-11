<?php
session_start();
include("../config/conexao.php");

$id = $_SESSION['idLogin'];

$nome = $_POST['nome'];
$email = $_POST['email'];
$novaSenha = $_POST['novaSenha'];

$conn->begin_transaction();

try {

    $stmt = $conn->prepare("
        UPDATE tblUsuario 
        SET nomeUsuario = ?, emailUsuario = ?
        WHERE idUsuario = ?
    ");
    $stmt->bind_param("ssi", $nome, $email, $id);
    $stmt->execute();

    if (!empty($novaSenha)) {
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $stmt2 = $conn->prepare("
            UPDATE tblLogin 
            SET senha = ?
            WHERE Usuario_idUsuario = ?
        ");
        $stmt2->bind_param("si", $senhaHash, $id);
        $stmt2->execute();
    }

    $conn->commit();

    header("Location: ../../perfil.php");

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro: " . $e->getMessage();
}
