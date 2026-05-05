<?php
session_start();
include("../config/conexao.php");

$id = $_SESSION['idLogin'];

$confirmNome = $_POST['confirmNome'];
$senha = $_POST['senha'];

// Buscar dados reais
$stmt = $conn->prepare("
    SELECT u.nomeUsuario, l.senha 
    FROM tblUsuario u
    INNER JOIN tblLogin l ON l.Usuario_idUsuario = u.idUsuario
    WHERE u.idUsuario = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// VALIDAÇÕES 🔥
if ($confirmNome !== $result['nomeUsuario']) {
    die("Nome não confere.");
}

if (!password_verify($senha, $result['senha'])) {
    die("Senha incorreta.");
}

// DELETAR
$conn->begin_transaction();

try {
    $conn->query("DELETE FROM tblLogin WHERE Usuario_idUsuario = $id");
    $conn->query("DELETE FROM tblUsuario WHERE idUsuario = $id");

    $conn->commit();

    session_destroy();
    header("Location: ../../index.php");

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao deletar: " . $e->getMessage();
}