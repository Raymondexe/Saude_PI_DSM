<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Preparar SQL
    $stmt = $conn->prepare("
        SELECT l.idLogin, l.usuario, l.senha, l.tipo_usuario, u.nomeUsuario
        FROM tblLogin l
        INNER JOIN tblUsuario u ON u.idUsuario = l.Usuario_idUsuario
        WHERE l.usuario = ?
    ");

    if (!$stmt) {
        die("Erro no prepare: " . $conn->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $dados = $resultado->fetch_assoc();

        if (password_verify($senha, $dados['senha'])) {

            // Criar sessão
            $_SESSION['idLogin'] = $dados['idLogin'];
            $_SESSION['usuario'] = $dados['usuario'];
            $_SESSION['nome'] = $dados['nomeUsuario'];
            $_SESSION['tipo'] = $dados['tipo_usuario'];
            $_SESSION['foto'] = $dados['foto'] ?? 'Img/defaultUser.png';

            // Redirecionar de acordo com tipo de usuário (opcional)
            header("Location: ../../index.php");
            exit();

        } else {
            $_SESSION['login_error'] = "Senha incorreta!";
            header("Location: ../../login.html");
            exit();
        }

    } else {
        $_SESSION['login_error'] = "Usuário não encontrado!";
        header("Location: ../../login.html");
        exit();
    }
}
?>