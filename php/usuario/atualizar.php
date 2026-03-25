<?php 

// Versão inicial para atualização de registros page 2

include("../config/conexao.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['idUsuario'] ?? null;
    $name = $_POST['nomeUsuario'] ?? null;
    $email = $_POST['emailUsuario'] ?? null;
    $phone = $_POST['telefoneUsuario'] ?? null;

    $sql = "UPDATE tblUsuario SET nomeUsuario='$name', emailUsuario='$email', telefoneUsuario='$phone' WHERE idUsuario='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

?>
