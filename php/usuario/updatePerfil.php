<?php
session_start();
include("../config/conexao.php");

if (!isset($_SESSION['idUsuario'])) {
    die("Usuário não autenticado.");
}

$id = $_SESSION['idUsuario'];

/* =========================
   FUNÇÕES
========================= */
function limparNumero($valor)
{
    return preg_replace('/\D/', '', $valor);
}

function formatarTelefone($telefone)
{
    $telefone = limparNumero($telefone);

    if (strlen($telefone) === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    }

    if (strlen($telefone) === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    }

    return $telefone;
}

function formatarCPF($cpf)
{
    $cpf = limparNumero($cpf);

    if (strlen($cpf) === 11) {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    return $cpf;
}

/* =========================
   DADOS RECEBIDOS
========================= */
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');

$telefoneLimpo = limparNumero($_POST['telefone'] ?? '');
$cpfLimpo = limparNumero($_POST['cpf'] ?? '');
$telefoneEmergenciaLimpo = limparNumero($_POST['telefoneEmergencia'] ?? '');

$telefone = formatarTelefone($_POST['telefone'] ?? '');
$cpf = formatarCPF($_POST['cpf'] ?? '');
$telefoneEmergencia = formatarTelefone($_POST['telefoneEmergencia'] ?? '');

$endereco = trim($_POST['endereco'] ?? '');
$tipoSanguineo = trim($_POST['tipoSanguineo'] ?? '');
$alergias = trim($_POST['alergias'] ?? '');
$doencasCronicas = trim($_POST['doencasCronicas'] ?? '');
$contatoEmergencia = trim($_POST['contatoEmergencia'] ?? '');

$novaSenha = trim($_POST['novaSenha'] ?? '');
$confirmarSenha = trim($_POST['confirmarSenha'] ?? '');

/* =========================
   VALIDAÇÃO DE SENHA
========================= */
if (!empty($novaSenha) || !empty($confirmarSenha)) {

    if (empty($novaSenha) || empty($confirmarSenha)) {
        header("Location: ../../perfil.php?erro=preenchaAmbasSenhas");
        exit;
    }

    if ($novaSenha !== $confirmarSenha) {
        header("Location: ../../perfil.php?erro=senhasDiferentes");
        exit;
    }
}

/* =========================
   BUSCA DADOS ATUAIS
========================= */
$stmt = $conn->prepare("SELECT * FROM tblUsuario WHERE idUsuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$usuarioAtual = $result->fetch_assoc();

if (!$usuarioAtual) {
    die("Usuário não encontrado.");
}

/* =========================
   NORMALIZA BANCO
========================= */
$telefoneBanco = limparNumero($usuarioAtual['telefoneUsuario'] ?? '');
$cpfBanco = limparNumero($usuarioAtual['cpfUsuario'] ?? '');
$telefoneEmergenciaBanco = limparNumero($usuarioAtual['telefoneEmergencia'] ?? '');

/* =========================
   VERIFICA ALTERAÇÕES
========================= */
$houveAlteracao =
    $nome !== trim($usuarioAtual['nomeUsuario'] ?? '') ||
    $email !== trim($usuarioAtual['emailUsuario'] ?? '') ||
    $telefoneLimpo !== $telefoneBanco ||
    $cpfLimpo !== $cpfBanco ||
    $endereco !== trim($usuarioAtual['enderecoUsuario'] ?? '') ||
    $tipoSanguineo !== trim($usuarioAtual['tipoSanguineo'] ?? '') ||
    $alergias !== trim($usuarioAtual['alergias'] ?? '') ||
    $doencasCronicas !== trim($usuarioAtual['doencasCronicas'] ?? '') ||
    $contatoEmergencia !== trim($usuarioAtual['contatoEmergencia'] ?? '') ||
    $telefoneEmergenciaLimpo !== $telefoneEmergenciaBanco ||
    !empty($novaSenha);

if (!$houveAlteracao) {
    header("Location: ../../perfil.php?semAlteracoes=1");
    exit;
}

/* =========================
   UPDATE
========================= */
$conn->begin_transaction();

try {

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

    /* =========================
       ALTERAR SENHA
    ========================= */
    if (!empty($novaSenha) && !empty($confirmarSenha)) {

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