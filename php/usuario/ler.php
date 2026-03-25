<?php 

// Versão inicial para leitura de registros

include("../config/conexao.php");



$sql = "SELECT * FROM tblUsuario";  // potencial para colocar string values no comando SQL para se tornar dinâmico
$result = $conn->query($sql); // retorna a consulta

if($result->num_rows > 0){ // se as linhas forem maior que 0, ou seja; ter pelo menos UM registro

    echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>"; //cria uma tabela html
    
    while($row = $result->fetch_assoc()){ // pega todas associações e um loop entre elas

    echo "<tr><td>" . $row["idUsuario"]. "</td><td>" . $row["nomeUsuario"]. "</td><td>" . $row["emailUsuario"]. "</td><td>" . $row["telefoneUsuario"]. "</td><td>
        <a href='editar.php?id=" . $row["idUsuario"] . "'>Edit</a> | <a href='deletar.php?id=" . $row["idUsuario"] . "'>Delete</a></td></tr>";

    }
    
    echo "<table>"; // fim da tabela 
}
else {
    echo "Nenhum registro encontrado";
}


?>