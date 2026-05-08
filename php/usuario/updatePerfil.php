<?php
session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

$id = $_SESSION['idUsuario'];

/* DADOS RECEBIDOS DO FORM */
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');

$tipoSanguineo = trim($_POST['tipoSanguineo'] ?? '');
$alergias = trim($_POST['alergias'] ?? '');
$doencasCronicas = trim($_POST['doencasCronicas'] ?? '');

$contatoEmergencia = trim($_POST['contatoEmergencia'] ?? '');
$telefoneEmergencia = trim($_POST['telefoneEmergencia'] ?? '');

$novaSenha = trim($_POST['novaSenha'] ?? '');

/* BUSCA DADOS ATUAIS */
$stmt = $conn->prepare("SELECT * FROM tblUsuario WHERE idUsuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$usuarioAtual = $result->fetch_assoc();

if (!$usuarioAtual) {
    die("Usuário não encontrado.");
}

/* VERIFICA SE HOUVE ALTERAÇÃO */
$houveAlteracao =
    $nome !== $usuarioAtual['nomeUsuario'] ||
    $email !== $usuarioAtual['emailUsuario'] ||
    $telefone !== $usuarioAtual['telefoneUsuario'] ||
    $cpf !== $usuarioAtual['cpfUsuario'] ||
    $endereco !== $usuarioAtual['enderecoUsuario'] ||
    $tipoSanguineo !== $usuarioAtual['tipoSanguineo'] ||
    $alergias !== $usuarioAtual['alergias'] ||
    $doencasCronicas !== $usuarioAtual['doencasCronicas'] ||
    $contatoEmergencia !== $usuarioAtual['contatoEmergencia'] ||
    $telefoneEmergencia !== $usuarioAtual['telefoneEmergencia'] ||
    !empty($novaSenha);

if (!$houveAlteracao) {
    header("Location: ../../perfil.php?semAlteracoes=1");
    exit;
}

$conn->begin_transaction();

try {

    /* UPDATE USUÁRIO */
    $stmtUpdate = $conn->prepare("
        UPDATE tblUsuario
        SET
            nomeUsuario = ?,
            emailUsuario = ?,
            telefoneUsuario = ?,
            cpfUsuario = ?,
            enderecoUsuario = ?,
            tipoSanguineo = ?,
            alergias = ?,
            doencasCronicas = ?,
            contatoEmergencia = ?,
            telefoneEmergencia = ?
        WHERE idUsuario = ?
    ");

    $stmtUpdate->bind_param(
        "ssssssssssi",
        $nome,
        $email,
        $telefone,
        $cpf,
        $endereco,
        $tipoSanguineo,
        $alergias,
        $doencasCronicas,
        $contatoEmergencia,
        $telefoneEmergencia,
        $id
    );

    $stmtUpdate->execute();

    /* UPDATE SENHA */
    if (!empty($novaSenha)) {
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $stmtSenha = $conn->prepare("
            UPDATE tblLogin
            SET senha = ?
            WHERE Usuario_idUsuario = ?
        ");

        $stmtSenha->bind_param("si", $senhaHash, $id);
        $stmtSenha->execute();
    }

    $conn->commit();

    header("Location: ../../perfil.php?sucesso=1");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Erro: " . $e->getMessage());
}
?>