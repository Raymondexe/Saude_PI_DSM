<?php


include("../config/conexao.php");

// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nomeUsuario'] ?? null;
    $dataNascimento = $_POST['dataNascimento'] ?? null;
    $sexo = $_POST['sexo'] ?? null;
    $endereco = $_POST['enderecoUsuario'] ?? null;
    $telefone = $_POST['telefoneUsuario'] ?? null;
    $email = $_POST['emailUsuario'] ?? null;

    $usuario = $_POST['usuario'] ?? null;
    $senha = isset($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
    $tipo = $_POST['tipoUsuario'] ?? null;

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            INSERT INTO tblUsuario 
            (nomeUsuario, dataNascimento, sexo, enderecoUsuario, telefoneUsuario, emailUsuario, dataCadastroUsuario)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            throw new Exception("Erro no prepare da tblUsuario: " . $conn->error);
        }

        $stmt->bind_param("ssssss", $nome, $dataNascimento, $sexo, $endereco, $telefone, $email);
        $stmt->execute();

        $idUsuario = $conn->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO tblLogin (Usuario_idUsuario, usuario, senha, tipo_usuario)
        VALUES (?, ?, ?, ?)
        ");

        if (!$stmt2) {
            throw new Exception("Erro no prepare da tblLogin: " . $conn->error);
        }

        // Bind correto: 1 inteiro + 3 strings
        $stmt2->bind_param("isss", $idUsuario, $usuario, $senha, $tipo);
        $stmt2->execute();

        $stmt2->bind_param("isss", $idUsuario, $usuario, $senha, $tipo);
        $stmt2->execute();

        $conn->commit();
        echo "Cadastro realizado com sucesso!";

    } catch (Exception $e) {
        $conn->rollback();
        echo "Erro no cadastro: " . $e->getMessage();
    }
}