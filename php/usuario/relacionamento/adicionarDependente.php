<?php
include("../../config/conexao.php");

$idFamilia = $_POST['idFamilia'] ?? null;
$codigo = trim($_POST['codigo'] ?? '');

if (!$idFamilia || !$codigo) {
    exit("Dados inválidos");
}

/* =========================
   BUSCA USUÁRIO PELO CÓDIGO
========================= */
$sqlUsuario = $conn->prepare("
    SELECT idUsuario
    FROM tblusuario
    WHERE codigoVinculo = ?
");

if (!$sqlUsuario) {
    die("Erro SQL usuário: " . $conn->error);
}

$sqlUsuario->bind_param("s", $codigo);
$sqlUsuario->execute();

$result = $sqlUsuario->get_result();

if ($result->num_rows === 0) {
    exit("Código não encontrado");
}

$usuario = $result->fetch_assoc();
$idDependente = $usuario['idUsuario'];

echo "1 - passou código<br>";

$result->free();
$sqlUsuario->close();


/* =========================
   LIMITE DE 10 MEMBROS
========================= */
$sqlCount = $conn->prepare("
    SELECT COUNT(*) as total
FROM tblfamiliausuario
WHERE Familia_idFamilia = ?
AND statusMembro = 'ativo'
");

if (!$sqlCount) {
    die("Erro COUNT: " . $conn->error);
}

$sqlCount->bind_param("i", $idFamilia);
$sqlCount->execute();

$resultCount = $sqlCount->get_result();
$total = $resultCount->fetch_assoc()['total'];

$resultCount->free();
$sqlCount->close();

if ($total >= 10) {
    exit("Família já possui 10 membros");
}

echo "2 - passou limite<br>";


/* =========================
   VERIFICA DUPLICIDADE
========================= */
$sqlExiste = $conn->prepare("
    SELECT idFamiliaUsuario
    FROM tblfamiliausuario
    WHERE Familia_idFamilia = ? 
    AND Usuario_idUsuario = ?
");

if (!$sqlExiste) {
    die("Erro EXISTS: " . $conn->error);
}

$sqlExiste->bind_param("ii", $idFamilia, $idDependente);
$sqlExiste->execute();

$resultExiste = $sqlExiste->get_result();

if ($resultExiste->num_rows > 0) {
    exit("Usuário já está vinculado à família");
}

$resultExiste->free();
$sqlExiste->close();

echo "3 - passou duplicidade<br>";


/* =========================
   ADICIONA DEPENDENTE
========================= */
$sqlInsert = $conn->prepare("
    INSERT INTO tblfamiliausuario
    (Familia_idFamilia, Usuario_idUsuario, papel, statusMembro)
    VALUES (?, ?, 'dependente', 'pendente')
");

if (!$sqlInsert) {
    die("Erro INSERT FAMÍLIA: " . $conn->error);
}

$sqlInsert->bind_param("ii", $idFamilia, $idDependente);

if (!$sqlInsert->execute()) {
    die("ERRO INSERT FAMILIA: " . $sqlInsert->error);
}

$sqlInsert->close();

echo "4 - inseriu dependente<br>";


/* =========================
   PEGA USUÁRIO RESPONSÁVEL
========================= */
$sqlBuscaUsuarioResp = $conn->prepare("
    SELECT Usuario_idUsuario
    FROM tblfamiliausuario
    WHERE Familia_idFamilia = ?
    AND papel = 'responsavel'
    LIMIT 1
");

if (!$sqlBuscaUsuarioResp) {
    die("Erro busca responsável: " . $conn->error);
}

$sqlBuscaUsuarioResp->bind_param("i", $idFamilia);
$sqlBuscaUsuarioResp->execute();

$resultUsuarioResp = $sqlBuscaUsuarioResp->get_result();
$usuarioResp = $resultUsuarioResp->fetch_assoc();

if (!$usuarioResp) {
    die("Família sem responsável");
}

$idUsuarioResponsavel = $usuarioResp['Usuario_idUsuario'];

echo "5 - achou usuario responsavel<br>";

$resultUsuarioResp->free();
$sqlBuscaUsuarioResp->close();


/* =========================
   VERIFICA/CRIA RESPONSÁVEL
========================= */
$sqlCheckResponsavel = $conn->prepare("
    SELECT idResponsavel
    FROM tblresponsavel
    WHERE Login_Usuario_idUsuario = ?
");

if (!$sqlCheckResponsavel) {
    die("Erro check responsável: " . $conn->error);
}

$sqlCheckResponsavel->bind_param("i", $idUsuarioResponsavel);
$sqlCheckResponsavel->execute();

$resultResp = $sqlCheckResponsavel->get_result();

echo "6 - consultou tblresponsavel<br>";

if ($resultResp->num_rows === 0) {

    echo "6.1 - entrando criação responsável<br>";

    $sqlDados = $conn->prepare("
    SELECT 
        l.idLogin,
        u.nomeUsuario,
        u.telefoneUsuario,
        u.emailUsuario,
        u.enderecoUsuario
    FROM tblusuario u
    INNER JOIN tbllogin l
        ON l.Usuario_idUsuario = u.idUsuario
    WHERE u.idUsuario = ?
");

    if (!$sqlDados) {
        die("ERRO sqlDados prepare: " . $conn->error);
    }

    $sqlDados->bind_param("i", $idUsuarioResponsavel);
    $sqlDados->execute();

    echo "6.2 - executou sqlDados<br>";

    $resultDados = $sqlDados->get_result();
    $dados = $resultDados->fetch_assoc();

    var_dump($dados);

    if (!$dados) {
        die("Dados do usuário não encontrados");
    }

    echo "6.3 - pegou dados usuário<br>";

    $sqlInsertResp = $conn->prepare("
        INSERT INTO tblresponsavel
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

    if (!$sqlInsertResp) {
        die("ERRO prepare insert responsável: " . $conn->error);
    }

    echo "6.4 - preparou insert<br>";

    $sqlInsertResp->bind_param(
        "iissss",
        $dados['idLogin'],
        $idUsuarioResponsavel,
        $dados['nomeUsuario'],
        $dados['telefoneUsuario'],
        $dados['emailUsuario'],
        $dados['enderecoUsuario']
    );

    echo "6.5 - bind ok<br>";

    if (!$sqlInsertResp->execute()) {
        die("ERRO execute responsável: " . $sqlInsertResp->error);
    }

    echo "7 - criou responsável<br>";
}

$resultResp->free();
$sqlCheckResponsavel->close();


/* =========================
   BUSCA ID RESPONSÁVEL
========================= */
$sqlResponsavel = $conn->prepare("
    SELECT idResponsavel
    FROM tblresponsavel
    WHERE Login_Usuario_idUsuario = ?
    LIMIT 1
");

$sqlResponsavel->bind_param("i", $idUsuarioResponsavel);
$sqlResponsavel->execute();

$resultResponsavel = $sqlResponsavel->get_result();
$responsavel = $resultResponsavel->fetch_assoc();

if (!$responsavel) {
    die("NÃO ACHOU RESPONSÁVEL");
}

$idResponsavel = $responsavel['idResponsavel'];

$resultResponsavel->free();
$sqlResponsavel->close();

echo "8 - pegou idResponsavel<br>";


/* =========================
   CRIA CONVITE
========================= */
$sqlConvite = $conn->prepare("
    INSERT INTO tblConvite
    (Usuario_idUsuario, Responsavel_idResponsavel, statusConvite, validadeConvite)
    VALUES (?, ?, 'pendente', DATE_ADD(NOW(), INTERVAL 7 DAY))
");

if (!$sqlConvite) {
    die("Erro CONVITE PREPARE: " . $conn->error);
}

$sqlConvite->bind_param("ii", $idDependente, $idResponsavel);

if (!$sqlConvite->execute()) {
    die("ERRO CONVITE: " . $sqlConvite->error);
}

echo "9 - criou convite<br>";
echo "ok";

$sqlConvite->close();
$conn->close();
?>