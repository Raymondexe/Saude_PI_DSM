<?php

include("../config/conexao.php");


$id = $_GET['idUsuario'];
$sql = "DELETE FROM tblUsuario WHERE idUsuario=%id";

if ($conn->query($sql) === TRUE){
    header("Location: ler.php");
}
else echo "Erro: " . $conn->error:


?>