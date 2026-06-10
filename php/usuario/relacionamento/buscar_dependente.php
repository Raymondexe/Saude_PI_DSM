<?php

header('Content-Type: application/json');

include("../../config/conexao.php");

$idUsuario = intval($_GET['id'] ?? 0);

if (!$idUsuario) {

    echo json_encode([
        "erro" => "Usuário inválido"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| USUÁRIO
|--------------------------------------------------------------------------
*/

$sqlUsuario = "
SELECT *
FROM tblusuario
WHERE idUsuario = ?
";

$stmtUsuario = $conn->prepare($sqlUsuario);

$stmtUsuario->bind_param(
    "i",
    $idUsuario
);

$stmtUsuario->execute();

$usuario =
    $stmtUsuario
        ->get_result()
        ->fetch_assoc();

/*
|--------------------------------------------------------------------------
| GLICEMIA
|--------------------------------------------------------------------------
*/

$sqlGlicemia = "
SELECT valor_glicemia
FROM tblglicemia
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtGlicemia = $conn->prepare($sqlGlicemia);

$stmtGlicemia->bind_param(
    "i",
    $idUsuario
);

$stmtGlicemia->execute();

$glicemia =
    $stmtGlicemia
        ->get_result()
        ->fetch_assoc();

/*
|--------------------------------------------------------------------------
| PRESSÃO
|--------------------------------------------------------------------------
*/

$sqlPressao = "
SELECT sistolica, diastolica
FROM tblpressao
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtPressao = $conn->prepare($sqlPressao);

$stmtPressao->bind_param(
    "i",
    $idUsuario
);

$stmtPressao->execute();

$pressao =
    $stmtPressao
        ->get_result()
        ->fetch_assoc();

/*
|--------------------------------------------------------------------------
| TEMPERATURA
|--------------------------------------------------------------------------
*/

$sqlTemperatura = "
SELECT temperatura
FROM tbltemperatura
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtTemperatura = $conn->prepare($sqlTemperatura);

$stmtTemperatura->bind_param(
    "i",
    $idUsuario
);

$stmtTemperatura->execute();

$temperatura =
    $stmtTemperatura
        ->get_result()
        ->fetch_assoc();

/*
|--------------------------------------------------------------------------
| BATIMENTOS
|--------------------------------------------------------------------------
*/

$sqlBpm = "
SELECT bpm
FROM tblbatimentos
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtBpm = $conn->prepare($sqlBpm);

$stmtBpm->bind_param(
    "i",
    $idUsuario
);

$stmtBpm->execute();

$bpm =
    $stmtBpm
        ->get_result()
        ->fetch_assoc();

/*
|--------------------------------------------------------------------------
| EVENTO MAIS PRÓXIMO
|--------------------------------------------------------------------------
*/

$sqlEvento = "
SELECT *
FROM tblevento
WHERE Usuario_idUsuario = ?
AND dataEvento >= CURDATE()
ORDER BY dataEvento ASC
LIMIT 1
";

$stmtEvento = $conn->prepare($sqlEvento);

$stmtEvento->bind_param(
    "i",
    $idUsuario
);

$stmtEvento->execute();

$evento =
    $stmtEvento
        ->get_result()
        ->fetch_assoc();

/*
|--------------------------------------------------------------------------
| RETORNO
|--------------------------------------------------------------------------
*/

echo json_encode([

    "usuario" => $usuario,

    "indicador" => [

        "glicemia" =>
            $glicemia['valor_glicemia'] ?? null,

        "pressao" =>
            isset($pressao)
                ? $pressao['sistolica'] . "/" . $pressao['diastolica']
                : null,

        "temperatura" =>
            $temperatura['temperatura'] ?? null,

        "bpm" =>
            $bpm['bpm'] ?? null
    ],

    "evento" => $evento

]);