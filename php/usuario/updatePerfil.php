<?php
session_start();
include("../config/conexao.php");

<<<<<<< HEAD
$id = $_SESSION['idLogin'];

$nome = $_POST['nome'];
$email = $_POST['email'];
$novaSenha = $_POST['novaSenha'];
=======
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
>>>>>>> c00d29eb8a4370918eab91ad61ff9b73999ac04c

$conn->begin_transaction();

try {

<<<<<<< HEAD
    $stmt = $conn->prepare("
        UPDATE tblUsuario 
        SET nomeUsuario = ?, emailUsuario = ?
        WHERE idUsuario = ?
    ");
    $stmt->bind_param("ssi", $nome, $email, $id);
    $stmt->execute();

    if (!empty($novaSenha)) {
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $stmt2 = $conn->prepare("
            UPDATE tblLogin 
            SET senha = ?
            WHERE Usuario_idUsuario = ?
        ");
        $stmt2->bind_param("si", $senhaHash, $id);
        $stmt2->execute();
=======
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
>>>>>>> c00d29eb8a4370918eab91ad61ff9b73999ac04c
    }

    $conn->commit();

<<<<<<< HEAD
    header("Location: ../../perfil.php");

} catch (Exception $e) {
    $conn->rollback();
    echo "Erro: " . $e->getMessage();
}
=======
    header("Location: ../../perfil.php?sucesso=1");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Erro: " . $e->getMessage());
}
?>
>>>>>>> c00d29eb8a4370918eab91ad61ff9b73999ac04c
