<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
<<<<<<< HEAD

=======
require_once '../utils/alert.php';
>>>>>>> c00d29eb8a4370918eab91ad61ff9b73999ac04c


include("../config/conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nomeUsuario'] ?? null;
    $dataNascimento = $_POST['dataNascimento'] ?? null;
    $sexo = $_POST['sexo'] ?? null;
    $endereco = $_POST['enderecoUsuario'] ?? null;
    $telefone = $_POST['telefoneUsuario'] ?? null;
    $email = $_POST['emailUsuario'] ?? null;

    
    $usuario = $_POST['emailUsuario'] ?? null;

    $senha = isset($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
    $tipo = $_POST['tipoUsuario'] ?? null;


    if (!$usuario || !$senha || !$tipo) {
        die("Erro: dados de login incompletos");
    }

    $conn->begin_transaction();

    try {
        
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

<<<<<<< HEAD
        echo "Cadastro realizado com sucesso!";
        // header("Location: ../../login.html");
=======
        //echo "Cadastro realizado com sucesso!";
        // header("Location: ../../login.html");
        meuAlerta("Cadastro realizado com sucesso!", "../../login.html", "Página de login");
>>>>>>> c00d29eb8a4370918eab91ad61ff9b73999ac04c

    } catch (Exception $e) {
        $conn->rollback();
        echo "Erro no cadastro: " . $e->getMessage();
    }
}