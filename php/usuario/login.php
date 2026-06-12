<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("../config/conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Preparar SQL
    $sql = "
SELECT 
    l.idlogin,
    l.usuario,
    l.senha,
    l.tipo_usuario,
    u.idusuario,
    u.nomeusuario,
    u.foto
FROM tbllogin l#
INNER JOIN tblusuario u
    ON u.idusuario = l.usuario_idusuario
WHERE l.usuario = $1
";

$resultado = pg_query_params($conn, $sql, array($usuario));

if (!$resultado) {
    die("Erro na consulta: " . pg_last_error($conn));
}

    if (!$stmt) {
        die("Erro no prepare: " . $conn->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if (pg_num_rows($resultado) > 0) {

        $dados = pg_fetch_assoc($resultado);

        if (password_verify($senha, $dados['senha'])) {

            // Criar sessão
            $_SESSION['idUsuario'] = $dados['idUsuario'];
            $_SESSION['idLogin'] = $dados['idLogin'];
            $_SESSION['usuario'] = $dados['usuario'];
            $_SESSION['nome'] = $dados['nomeUsuario'];
            $_SESSION['tipo'] = $dados['tipo_usuario'];
            $_SESSION['foto'] =
                !empty($dados['foto'])
                ? $dados['foto']
                : 'Img/defaultUser.png';

            // Redirecionar de acordo com tipo de usuário (opcional)
            if ($dados['tipo_usuario'] == 2)  {
                header("Location: ../../indexProfissional.php");
                exit();
            } else {
                header("Location: ../../index.php");
                exit();
            }

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
