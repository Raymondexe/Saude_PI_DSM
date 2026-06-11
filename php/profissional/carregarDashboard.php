<?php

session_start();
include("../../config/conexao.php");

header('Content-Type: application/json');

if (!isset($_SESSION["idUsuario"])) {
    http_response_code(401);

    echo json_encode([
        "erro" => "Não autenticado"
    ]);

    exit;
}

$idProfissional = $_SESSION["idUsuario"];

$resposta = [];