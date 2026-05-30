<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../utils/alert.php';
include("../config/conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nomeUsuario'] ?? null;
    $dataNascimento = $_POST['dataNascimento'] ?? null;
    $sexo = $_POST['sexo'] ?? null;
    $endereco = $_POST['enderecoUsuario'] ?? null;
    $telefone = $_POST['telefoneUsuario'] ?? null;
    $email = $_POST['emailUsuario'] ?? null;

    $usuario = $_POST['emailUsuario'] ?? null;

    $senha = isset($_POST['senha'])
        ? password_hash($_POST['senha'], PASSWORD_DEFAULT)
        : null;

    $tipo = $_POST['tipoUsuario'] ?? 1;

    if (!$usuario || !$senha) {
        die("Erro: dados de login incompletos");
    }

    $conn->begin_transaction();

    try {

        // =========================================
        // VERIFICA TELEFONE DUPLICADO
        // =========================================

        $verificaTelefone = $conn->prepare("
            SELECT idUsuario
            FROM tblUsuario
            WHERE telefoneUsuario = ?
        ");

        $verificaTelefone->bind_param(
            "s",
            $telefone
        );

        $verificaTelefone->execute();

        $verificaTelefone->store_result();

        if ($verificaTelefone->num_rows > 0) {

            echo "
            <script>

                alert('O telefone informado já pertence a outro cadastro.');

                window.history.back();

            </script>
            ";

            exit();
        }

        $verificaTelefone->close();

        // INSERT USUARIO
        $stmt = $conn->prepare("
            INSERT INTO tblUsuario 
            (
                nomeUsuario,
                dataNascimento,
                sexo,
                enderecoUsuario,
                telefoneUsuario,
                emailUsuario,
                dataCadastroUsuario
            )
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            throw new Exception(
                "Erro tblUsuario: " . $conn->error
            );
        }

        $stmt->bind_param(
            "ssssss",
            $nome,
            $dataNascimento,
            $sexo,
            $endereco,
            $telefone,
            $email
        );

        if (!$stmt->execute()) {
            throw new Exception(
                "Erro ao inserir usuário: " .
                $stmt->error
            );
        }

        $idUsuario = $conn->insert_id;



        // INSERT LOGIN
        $stmt2 = $conn->prepare("
 INSERT INTO tbllogin (Usuario_idUsuario, usuario, senha, tipo_usuario)
 VALUES (?, ?, ?, ?)
");

        if (!$stmt2) {
            throw new Exception(
                "Erro tblLogin: " .
                $conn->error
            );
        }

        $stmt2->bind_param(
            "isss",
            $idUsuario,
            $usuario,
            $senha,
            $tipo
        );

        if (!$stmt2->execute()) {
            throw new Exception(
                "Erro ao inserir login: " .
                $stmt2->error
            );
        }

        error_log("TIPO RECEBIDO: " . $tipo);

        if ((int) $tipo === 2) {

            $crm = $_POST['crm'] ?? null;
            $especialidade = $_POST['especialidade'] ?? null;
            $instituicao = $_POST['instituicao'] ?? null;

            error_log("INSERINDO PROFISSIONAL ID: " . $idUsuario);

            $stmtProf = $conn->prepare("
        INSERT INTO tblprofissionalsaude
        (Usuario_idUsuario, tipo, crm, especialidade, instituicao)
        VALUES (?, ?, ?, ?, ?)
    ");

            if (!$stmtProf) {
                throw new Exception("Erro prepare profissional: " . $conn->error);
            }

            $stmtProf->bind_param(
                "issss",
                $idUsuario,
                $tipo,
                $crm,
                $especialidade,
                $instituicao
            );

            if (!$stmtProf->execute()) {
                throw new Exception("Erro insert profissional: " . $stmtProf->error);
            }

            error_log("PROFISSIONAL INSERIDO COM SUCESSO");
        }


        if ($tipo == 2) {

            if (!preg_match('/^\d{6}\/[A-Z]{2}$/', strtoupper($crm))) {
                throw new Exception("CRM inválido. Formato esperado: 123456/SP");
            }
        }
        
        // COMMIT FINAL
        $conn->commit();

        meuAlerta(
            "Cadastro realizado com sucesso!",
            "../../login.html",
            "Página de login"
        );

    } catch (Exception $e) {

        $conn->rollback();

        echo "Erro no cadastro: " .
            $e->getMessage();
    }
}
?>