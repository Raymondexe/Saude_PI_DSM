<?php
session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("erro");
}

$id = $_SESSION['idUsuario'];

$conn->begin_transaction();

try {

    /* remove login */
    $stmtLogin = $conn->prepare("
        DELETE FROM tblLogin
        WHERE Usuario_idUsuario = ?
    ");
    $stmtLogin->bind_param("i", $id);
    $stmtLogin->execute();

    /* remove usuário */
    $stmtUsuario = $conn->prepare("
        DELETE FROM tblUsuario
        WHERE idUsuario = ?
    ");
    $stmtUsuario->bind_param("i", $id);
    $stmtUsuario->execute();

    $conn->commit();

    session_destroy();

    echo "ok";

} catch (Exception $e) {
    $conn->rollback();
    echo "erro";
}
?>