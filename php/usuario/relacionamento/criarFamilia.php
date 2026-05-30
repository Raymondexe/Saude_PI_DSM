<?php
session_start();
include("../../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método inválido");
}

$idUsuario = $_SESSION['idUsuario'];
$nomeFamilia = trim($_POST['nomeFamilia'] ?? '');

if (empty($nomeFamilia)) {
    die("Nome da família obrigatório");
}

/* REGRA: máximo 2 famílias */
$stmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM tblFamiliaUsuario
    WHERE Usuario_idUsuario = ?
    AND papel = 'responsavel'
");

$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['total'] >= 2) {
    die("Você já atingiu o limite de 2 famílias.");
}

/* cria família */
$stmt = $conn->prepare("
    INSERT INTO tblFamilia (nomeFamilia)
    VALUES (?)
");
$stmt->bind_param("s", $nomeFamilia);

if (!$stmt->execute()) {
    die("Erro ao criar família.");
}

$idFamilia = $conn->insert_id;

/* adiciona usuário logado como responsável na família */
$stmt = $conn->prepare("
    INSERT INTO tblFamiliaUsuario
    (
        Familia_idFamilia,
        Usuario_idUsuario,
        papel,
        statusMembro
    )
    VALUES (?, ?, 'responsavel', 'ativo')
");

$stmt->bind_param("ii", $idFamilia, $idUsuario);

if (!$stmt->execute()) {
    die("Erro ao vincular usuário.");
}

/* verifica se já existe em tblResponsavel */
$stmt = $conn->prepare("
    SELECT idResponsavel
    FROM tblResponsavel
    WHERE Login_Usuario_idUsuario = ?
");

$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$responsavel = $stmt->get_result()->fetch_assoc();

/* se não existir, cria automaticamente */
if (!$responsavel) {

    // pega dados do usuário
    $stmt = $conn->prepare("
        SELECT nomeUsuario, telefoneUsuario, emailUsuario, enderecoUsuario
        FROM tblUsuario
        WHERE idUsuario = ?
    ");

    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    // pega login do usuário
    $stmt = $conn->prepare("
        SELECT idLogin
        FROM tblLogin
        WHERE Usuario_idUsuario = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $login = $stmt->get_result()->fetch_assoc();

    if (!$login) {
        die("Login não encontrado.");
    }

    $idLogin = $login['idLogin'];
    $nome = $usuario['nomeUsuario'];
    $telefone = $usuario['telefoneUsuario'];
    $email = $usuario['emailUsuario'];
    $endereco = $usuario['enderecoUsuario'];

    $stmt = $conn->prepare("
        INSERT INTO tblResponsavel
        (
            Login_idLogin,
            Login_Usuario_idUsuario,
            nomeResponsavel,
            telefoneResponsavel,
            emailResponsavel,
            parentesco,
            enderecoResponsavel
        )
        VALUES (?, ?, ?, ?, ?, 'Responsável', ?)
    ");

    $stmt->bind_param(
        "iissss",
        $idLogin,
        $idUsuario,
        $nome,
        $telefone,
        $email,
        $endereco
    );

    if (!$stmt->execute()) {
        die("Erro ao cadastrar responsável.");
    }
}

echo "ok";
?>