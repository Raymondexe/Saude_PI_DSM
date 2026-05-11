<?php
session_start();
include("php/config/conexao.php");

$logado = isset($_SESSION['idLogin']);

if (!$logado) {
    header("Location: login.html");
    exit;
}

$id = $_SESSION['idUsuario'];

$stmt = $conn->prepare("
    SELECT *
    FROM tblUsuario
    WHERE idUsuario = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$usuario = $result->fetch_assoc();


$nome = $usuario['nomeUsuario'] ?? '';
$email = $usuario['emailUsuario'] ?? '';
$telefone = $usuario['telefoneUsuario'] ?? '';
$cpf = $usuario['cpfUsuario'] ?? '';
$endereco = $usuario['enderecoUsuario'] ?? '';

if (empty($usuario['codigoVinculo'])) {
    $codigo = 'BSTR-' . strtoupper(substr(md5(uniqid()), 0, 6));

    $stmtCodigo = $conn->prepare("
        UPDATE tblUsuario 
        SET codigoVinculo = ? 
        WHERE idUsuario = ?
    ");
    $stmtCodigo->bind_param("si", $codigo, $id);
    $stmtCodigo->execute();

    $usuario['codigoVinculo'] = $codigo;
}

$codigoVinculo = $usuario['codigoVinculo'] ?? '';

$fotoBanco = $usuario['foto'] ?? null;

if (!empty($fotoBanco) && file_exists("uploads/" . $fotoBanco)) {
    $foto = "uploads/" . $fotoBanco;
} else {
    $foto = "Img/defaultUser.png";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-Estar 360 - Login</title>

    <!-- CSS externo -->
    <link rel="stylesheet" href="Css/estilo.css">
    <link rel="stylesheet" href="Css/estiloPerfilUser.css">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>

<body>
    <!-- Header -->
    <header class="TopoSite">
        <div class="Logo">
            <img class="ImgLogo" src="Img/bemEstar.webp" alt="Logo Bem Estar 360">
        </div>

        <button class="menu-toggle" aria-label="Abrir menu">☰</button>

        <nav class="Navegacao">
            <ul>
                <li><a href="./index.html" data-lang="home">Home</a></li>
                <li><a href="./monitoramento.html" data-lang="monitoring">Monitoramento</a></li>
                <li><a href="./servicos.html" data-lang="services">Serviços</a></li>
                <li><a href="./quemSomos.html" data-lang="about">Quem somos</a></li>

                <?php if ($logado): ?>

                    <li class="perfil-menu">
                        <a href="/Saude_PI_DSM-main/perfil.php" id="perfil-btn" class="perfil-link">
                            <img src="<?= $foto ?>" alt="Foto de perfil" class="foto-perfil">
                            <span class="nome-perfil"><?= $nome ?></span>
                        </a>

                        <button id="btnNotificacao" class="notificacao-btn">
                            <img id="iconeNotificacao" src="Img/Corres_Fechada.png" alt="Notificações" class="Notificacao">
                        </button>
                    </li>

                <?php else: ?>
                    <li><a href="./login.html" data-lang="login">Login</a></li>
                <?php endif; ?>

                <!-- Menu de Configurações -->
                <li class="config-menu">
                    <button id="config-btn" aria-haspopup="true" aria-expanded="false">⚙️</button>

                    <div class="dropdown" role="menu">
                        <button id="toggle-theme">🌙 Modo Escuro</button>
                        <button id="change-lang">🌎 Trocar Idioma</button>
                    </div>
                </li>
            </ul>
        </nav>


    </header>

    <script src="script.js"></script>
    <script src="scriptTraducao.js"></script>
    <script src="scriptShowLogin.js"></script>

    <script>

        // Abre accordio após clicar na Âncora 
        document.querySelectorAll('.menu a').forEach(link => {
            link.addEventListener('click', function () {
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);

                if (target && target.tagName === 'DETAILS') {
                    target.open = true;
                }
            });
        });


        // Code Copia código
        function copiarCodigo() {
            const codigo = document.getElementById('codigo').innerText;

            navigator.clipboard.writeText(codigo);

            alert('Código copiado!');
        }


        // Validação código (Relacionamento)
        const inputCodigo = document.getElementById("codigoDependente");
        const erro = document.getElementById("codigoErro");

        inputCodigo.addEventListener("input", function () {
            this.value = this.value.toUpperCase();

            const regex = /^BSTR-[A-Z0-9]{6}$/;

            if (this.value === "") {
                erro.textContent = "";
                return;
            }

            if (!regex.test(this.value)) {
                erro.textContent = "Formato inválido. Use: BSTR-F84FCD";
                erro.style.color = "red";
            } else {
                erro.textContent = "Código válido";
                erro.style.color = "green";
            }
        });
    </script>

    <div class="perfil-layout">

        <aside class="sidebar">
            <div class="profile-box">
                <img src="<?= $foto ?>" alt="Foto perfil" class="foto-sidebar">

                <form action="php/usuario/uploadFoto.php" method="POST" enctype="multipart/form-data"
                    class="upload-form">
                    <label for="fotoInput" class="btn-upload">
                        Alterar foto
                    </label>

                    <input type="file" id="fotoInput" name="foto" accept="image/png, image/jpeg, image/jpg, image/webp"
                        onchange="this.form.submit()" hidden>
                </form>

                <h2><?= $nome ?></h2>
                <p>Paciente</p>

                <div class="codigo-box">
                    <span>Seu código para criação de vínculo</span>

                    <div class="codigo-content">
                        <strong id="codigo">
                            <?= $codigoVinculo ?>
                        </strong>
                        <button type="button" onclick="copiarCodigo()">Copiar</button>
                    </div>
                </div>
            </div>

            <div class="sidebar-accordion">

                <details open class="sidebar-details">
                    <summary>Informações</summary>

                    <div class="menu">
                        <a href="#dados-pessoais">Dados Pessoais</a>
                        <a href="#dados-medicos">Dados Médicos</a>
                        <a href="#emergencia">Emergência e Responsável</a>
                        <a href="#endereco">Endereço</a>
                        <a href="#seguranca">Segurança</a>
                    </div>
                </details>

                <details class="sidebar-details">
                    <summary>Relacionamento</summary>

                    <div class="menu">
                        <a href="#relacionamento">Adicionar Dependente</a>
                        <a href="#historico-convites">Histórico</a>
                    </div>
                </details>

                <form action="php/usuario/logout.php" method="POST" class="logout-form">
                    <button type="submit" class="logout-btn">
                        Sair da Conta
                    </button>
                </form>
            </div>
        </aside>

        <main class="content">

            <!-- BLOCO INFORMAÇÕES -->
            <details class="main-accordion" open>
                <summary>Informações Pessoais</summary>

                <div class="card">
                    <form action="php/usuario/updatePerfil.php" method="POST">

                        <details id="dados-pessoais" class="accordion-item" open>
                            <summary>Dados Pessoais</summary>

                            <div class="grid">
                                <div class="field">
                                    <label>Nome completo</label>
                                    <input type="text" name="nome" value="<?= $nome ?>">
                                </div>

                                <div class="field">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?= $email ?>">
                                </div>

                                <div class="field">
                                    <label>Telefone</label>
                                    <input type="text" name="telefone" value="<?= $telefone ?>">
                                </div>

                                <div class="field">
                                    <label>CPF</label>
                                    <input type="text" name="cpf" value="<?= $cpf ?>">
                                </div>
                            </div>
                        </details>

                        <details id="dados-medicos" class="accordion-item">
                            <summary>Dados Médicos</summary>

                            <div class="grid">
                                <div class="field">
                                    <label>Tipo sanguíneo</label>
                                    <select name="tipoSanguineo">
                                        <option>O+</option>
                                    </select>
                                </div>

                                <div class="field">
                                    <label>Alergias</label>
                                    <textarea name="alergias"></textarea>
                                </div>
                            </div>
                        </details>

                        <details id="emergencia" class="accordion-item">
                            <summary>Emergência e Responsável</summary>

                            <div class="grid">
                                <div class="field">
                                    <label>Contato emergência</label>
                                    <input type="text" name="contatoEmergencia">
                                </div>
                            </div>
                        </details>

                        <details id="endereco" class="accordion-item">
                            <summary>Endereço</summary>

                            <div class="grid">
                                <div class="field">
                                    <label>Endereço</label>
                                    <input type="text" name="endereco" value="<?= $endereco ?>">
                                </div>
                            </div>
                        </details>

                        <details id="seguranca" class="accordion-item">
                            <summary>Segurança e Conta</summary>

                            <div class="grid">
                                <div class="field">
                                    <label>Nova senha</label>
                                    <input type="password" name="novaSenha">
                                </div>
                            </div>
                        </details>

                        <button class="save-btn" type="submit">
                            Salvar Alterações
                        </button>
                    </form>
                </div>
            </details>


            <!-- BLOCO RELACIONAMENTO -->
            <details id="relacionamento" class="main-accordion">
                <summary>Relacionamento</summary>

                <div class="card">
                    <form action="php/relacionamento/enviarConvite.php" method="POST">
                        <details class="accordion-item" open>
                            <summary>Adicionar Dependente</summary>

                            <div class="grid">
                                <div class="field">
                                    <label>Código do dependente</label>
                                    <input type="text" name="codigoDependente" id="codigoDependente"
                                        placeholder="BSTR-F84FCD" maxlength="11" pattern="^BSTR-[A-Z0-9]{6}$" required>
                                    <small id="codigoErro"></small>
                                </div>

                            </div>
                            <button class="save-btn" type="button">
                                Enviar convite
                            </button>
                        </details>
                    </form>

                    <details class="accordion-item">
                        <summary>Histórico de Convites</summary>

                        <div class="history-list">
                            <div class="history-item pending">
                                <strong>Julieta</strong>
                                <span>Pendente</span>
                            </div>

                            <div class="history-item accepted">
                                <strong>Maria</strong>
                                <span>Aceito</span>
                            </div>

                            <div class="history-item refused">
                                <strong>Carlos</strong>
                                <span>Recusado</span>
                            </div>
                        </div>
                    </details>

                </div>
            </details>

        </main>
    </div>

    <div class="card">
        <h3>Resumo de Saúde</h3>

        <div class="stats">
            <div class="stat-box">
                <h2>72kg</h2>
                <p>Peso</p>
            </div>

            <div class="stat-box">
                <h2>22.1</h2>
                <p>IMC</p>
            </div>

            <div class="stat-box">
                <h2>7h</h2>
                <p>Sono médio</p>
            </div>

            <div class="stat-box">
                <h2>2L</h2>
                <p>Água hoje</p>
            </div>
        </div>
    </div>
    </main>
    <br><br><br><br><br><br><br>





















<<<<<<< HEAD








=======
>>>>>>> c00d29eb8a4370918eab91ad61ff9b73999ac04c
    <!-- Rodapé -->
    <footer class="footer">
        <div class="footerContainer">
            <!-- Logo e nome -->
            <div class="footerBrand">
                <img src="Img/2.png" alt="Bem Estar 360" class="footerLogo">

            </div>

            <div class="footerLinks">
                <ul>
                    <li><a href="./index.html" data-lang="footerHome">Home</a></li>
                    <li><a href="./monitoramento.html" data-lang="footerMonitoring">Monitoramento</a></li>
                    <li><a href="./servicos.html" data-lang="footerServices">Serviços</a></li>
                    <li><a href="./quemSomos.html" data-lang="about">Quem somos</a></li>
                </ul>
            </div>

            <!-- Contato -->
            <div class="footerContato">
                <h4 data-lang="footerContactTitle">Contato</h4>
                <p data-lang="footerEmail">Email: contato@bemestar360.com</p>
                <p data-lang="footerPhone">Telefone: (11) 1234-5678</p>
                <div class="footerSocials">
                    <a href="#"><img src="./Img/face_icon.png" alt="Facebook"></a>
                    <a href="#"><img src="./Img/insta_icon.webp" alt="Instagram"></a>
                    <a href="#"><img src="./Img/X_icon.svg.png" alt="Twitter"></a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footerBottom">
            <p data-lang="footerCopy" data-lang="textFooter">&copy; 2025 Bem-Estar 360. Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="script.js"></script>
</body>

</html>