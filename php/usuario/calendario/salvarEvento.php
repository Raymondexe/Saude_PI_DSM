<?php
session_start();
include("../../config/conexao.php");

$idEvento = $_POST['idEvento'] ?? null;
$idUsuario = $_POST['usuario'];

$local = $_POST['local'];
$tipo = $_POST['tipo'];
$medico = $_POST['medico'];
$horario = $_POST['horario'];
$dataEvento = $_POST['data'];
$levar = $_POST['levar'];

if (!empty($idEvento)) {

    $stmt = $conn->prepare("
        UPDATE tblEvento
        SET
            localEvento = ?,
            tipoEvento = ?,
            medicoEvento = ?,
            horarioEvento = ?,
            dataEvento = ?,
            levarEvento = ?
        WHERE idEvento = ?
    ");

    $stmt->bind_param(
        "ssssssi",
        $local,
        $tipo,
        $medico,
        $horario,
        $dataEvento,
        $levar,
        $idEvento
    );

} else {

    $stmt = $conn->prepare("
        INSERT INTO tblEvento
        (
            Usuario_idUsuario,
            localEvento,
            tipoEvento,
            medicoEvento,
            horarioEvento,
            dataEvento,
            levarEvento
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssss",
        $idUsuario,
        $local,
        $tipo,
        $medico,
        $horario,
        $dataEvento,
        $levar
    );
}

if ($stmt->execute()) {
    echo "ok";
} else {
    echo "erro";
}
?>