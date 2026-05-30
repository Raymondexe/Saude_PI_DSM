<?php

header('Content-Type: application/json');

include("../config/conexao.php");

$codigo = $_POST['codigo'] ?? '';

if (empty($codigo)) {

    echo json_encode([
        "status" => false,
        "mensagem" => "Código vazio"
    ]);

    exit;
}

$sql = "
SELECT *
FROM tblusuario
WHERE codigoVinculo = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "status" => false,
        "mensagem" => $conn->error
    ]);

    exit;
}

$stmt->bind_param("s", $codigo);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {

    $usuario = $resultado->fetch_assoc();

    echo json_encode([
        "status" => true,
        "usuario" => $usuario
    ]);

} else {

    echo json_encode([
        "status" => false,
        "mensagem" => "Paciente não encontrado"
    ]);

}