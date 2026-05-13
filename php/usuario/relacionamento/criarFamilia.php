<?php
session_start();
include("../../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método inválido");
}

$idUsuario = $_SESSION['idUsuario'];
$nomeFamilia = trim($_POST['nomeFamilia'] ?? '');

if (empty($nomeFamilia)) {
    die("Nome da família obrigatório");
}

/* REGRA: máximo 2 famílias */
$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM tblFamiliaUsuario
    WHERE Usuario_idUsuario = ?
    AND papel = 'responsavel'
");

$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['total'] >= 2) {
    die("Você já atingiu o limite de 2 famílias.");
}

/* cria família */
$stmt = $conn->prepare("
    INSERT INTO tblFamilia (nomeFamilia)
    VALUES (?)
");
$stmt->bind_param("s", $nomeFamilia);

if (!$stmt->execute()) {
    die("Erro ao criar família.");
}

$idFamilia = $conn->insert_id;

/* adiciona usuário logado como responsável */
$stmt = $conn->prepare("
    INSERT INTO tblFamiliaUsuario
    (
        Familia_idFamilia,
        Usuario_idUsuario,
        papel,
        statusMembro
    )
    VALUES (?, ?, 'responsavel', 'ativo')
");

$stmt->bind_param("ii", $idFamilia, $idUsuario);

if ($stmt->execute()) {
    echo "ok";
} else {
    die("Erro ao vincular usuário.");
}
?>