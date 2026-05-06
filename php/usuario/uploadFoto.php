<?php
session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

$id = $_SESSION['idUsuario'];

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {

    $arquivo = $_FILES['foto'];

    /* =========================
       VALIDAR TAMANHO
    ========================= */
    if ($arquivo['size'] > 5 * 1024 * 1024) {
        die("Arquivo muito grande. Máximo 5MB.");
    }

    /* =========================
       VALIDAR EXTENSÃO
    ========================= */
    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $permitidos)) {
        die("Formato inválido");
    }

    /* =========================
       BUSCAR FOTO ANTIGA
    ========================= */
    $stmtOld = $conn->prepare("
        SELECT foto 
        FROM tblUsuario 
        WHERE idUsuario = ?
    ");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();

    $resultOld = $stmtOld->get_result();
    $oldUser = $resultOld->fetch_assoc();

    /* =========================
       CRIAR PASTA SE NÃO EXISTIR
    ========================= */
    if (!is_dir("../../uploads")) {
        mkdir("../../uploads", 0777, true);
    }

    /* =========================
       GERAR NOVO NOME
    ========================= */
    $nomeArquivo = uniqid() . "." . $ext;
    $caminho = "../../uploads/" . $nomeArquivo;

    /* =========================
       SALVAR NOVA FOTO
    ========================= */
    if (!move_uploaded_file($arquivo['tmp_name'], $caminho)) {
        die("Erro ao salvar imagem.");
    }

    /* =========================
       APAGAR FOTO ANTIGA
    ========================= */
    if (
        !empty($oldUser['foto']) &&
        file_exists("../../uploads/" . $oldUser['foto'])
    ) {
        unlink("../../uploads/" . $oldUser['foto']);
    }

    /* =========================
       UPDATE BANCO
    ========================= */
    $stmt = $conn->prepare("
        UPDATE tblUsuario 
        SET foto = ?
        WHERE idUsuario = ?
    ");

    $stmt->bind_param("si", $nomeArquivo, $id);

    if ($stmt->execute()) {
        header("Location: ../../perfil.php");
        exit;
    } else {
        die("Erro ao atualizar banco.");
    }

} else {
    die("Nenhuma imagem enviada.");
}
?>