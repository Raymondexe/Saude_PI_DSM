<?php
session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("erro");
}

$id = $_SESSION['idUsuario'];

$tipo = $_POST['tipo'] ?? '';
$valor = trim($_POST['valor'] ?? '');

if (!$tipo || !$valor) {
    die("erro");
}

$campo = ($tipo === 'alergia') ? 'alergias' : 'doencasCronicas';

/* busca valor atual */
$stmt = $conn->prepare("SELECT $campo FROM tblUsuario WHERE idUsuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$listaAtual = $usuario[$campo] ?? '';

$array = array_filter(array_map('trim', explode(',', $listaAtual)));

/* remove item específico */
$array = array_filter($array, function ($item) use ($valor) {
    return $item !== $valor;
});

$novoValor = implode(',', $array);

/* atualiza banco */
$update = $conn->prepare("UPDATE tblUsuario SET $campo = ? WHERE idUsuario = ?");
$update->bind_param("si", $novoValor, $id);

if ($update->execute()) {
    echo "ok";
} else {
    echo "erro";
}
?>