<?php 

// Versão inicial para atualização de registros

include("../config/conexao.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM tblUsuario WHERE idUsuario=$id";
    $result = $conn->query($sql);
    
    if ($result === false){
        echo "Erro: " . $conn->error;
    }
    else $row = $result->fetch_assoc();

    
}




?>

<form action="atualizar.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $row['idUsuario']; ?>">
    <label for="nome">Name:</label>
    <input type="text" name="nome" value="<?php echo $row['nomeUsuario']; ?>" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo $row['emailUsuario']; ?>"><br>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" value="<?php echo $row['telefoneUsuario']; ?>"><br>

    <input type="submit" value="Update">
</form>
