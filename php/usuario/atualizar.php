<?php 

// Versão inicial para atualização de registros page 2

include("../config/conexao.php");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['idUsuario'] ?? null;
    $name = $_POST['nomeUsuario'] ?? null;
    $email = $_POST['emailUsuario'] ?? null;
    $phone = $_POST['telefoneUsuario'] ?? null;
    

    // Debug: confirma os valores antes de executar
    echo "ID: $id | Nome: $name | Email: $email | Telefone: $phone <br>";

    if (!$id) {
        echo "Erro: ID do usuário não recebido.";
        exit;
    }
    
    $stmt = $conn->prepare("UPDATE tblUsuario SET nomeUsuario=?, emailUsuario=?, telefoneUsuario=? WHERE idUsuario=?");
    $stmt->bind_param("sssi", $name, $email, $phone, $id);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        //echo "Error: " . $sql . "<br>" . $conn->error;
        echo "Error: " . $stmt->error;
    }
}

?>
