<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



include("../config/conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nomeUsuario'] ?? null;
    $dataNascimento = $_POST['dataNascimento'] ?? null;
    $sexo = $_POST['sexo'] ?? null;
    $endereco = $_POST['enderecoUsuario'] ?? null;
    $telefone = $_POST['telefoneUsuario'] ?? null;
    $email = $_POST['emailUsuario'] ?? null;

    // 👉 usa email como login (RECOMENDADO)
    $usuario = $_POST['emailUsuario'] ?? null;

    $senha = isset($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
    $tipo = $_POST['tipoUsuario'] ?? null;

    // 🔥 validação básica
    if (!$usuario || !$senha || !$tipo) {
        die("Erro: dados de login incompletos");
    }

    $conn->begin_transaction();

    try {
        // 🔹 INSERE USUÁRIO
        $stmt = $conn->prepare("
            INSERT INTO tblUsuario 
            (nomeUsuario, dataNascimento, sexo, enderecoUsuario, telefoneUsuario, emailUsuario, dataCadastroUsuario)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            throw new Exception("Erro tblUsuario: " . $conn->error);
        }

        $stmt->bind_param("ssssss", $nome, $dataNascimento, $sexo, $endereco, $telefone, $email);
        $stmt->execute();

        $idUsuario = $conn->insert_id;

        // 🔹 INSERE LOGIN
        $stmt2 = $conn->prepare("
            INSERT INTO tblLogin (Usuario_idUsuario, usuario, senha, tipo_usuario)
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt2) {
            throw new Exception("Erro tblLogin: " . $conn->error);
        }

        $stmt2->bind_param("isss", $idUsuario, $usuario, $senha, $tipo);

        if (!$stmt2->execute()) {
            throw new Exception("Erro ao inserir login: " . $stmt2->error);
        }

        $conn->commit();

        echo "Cadastro realizado com sucesso!";
        // header("Location: ../../login.html");

    } catch (Exception $e) {
        $conn->rollback();
        echo "Erro no cadastro: " . $e->getMessage();
    }
}