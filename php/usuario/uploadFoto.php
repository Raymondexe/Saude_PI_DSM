<?php
session_start();
include("../config/conexao.php");

$id = $_SESSION['idLogin'];

if (isset($_FILES['foto'])) {

    $arquivo = $_FILES['foto'];

    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $permitidos)) {
        die("Formato inválido");
    }

    $nomeArquivo = uniqid() . "." . $ext;
    $caminho = "../../uploads/" . $nomeArquivo;

    move_uploaded_file($arquivo['tmp_name'], $caminho);

    $stmt = $conn->prepare("UPDATE tblUsuario SET foto = ? WHERE idUsuario = ?");
    $stmt->bind_param("si", $nomeArquivo, $id);
    $stmt->execute();

    header("Location: ../../perfil.php");
}