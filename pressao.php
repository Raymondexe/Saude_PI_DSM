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


/*
|--------------------------------------------------------------------------
| IDENTIFICA PAPEL DO USUÁRIO
|--------------------------------------------------------------------------
*/

$idUsuario = $_SESSION['idUsuario'];

$sqlPapel = "
SELECT papel
FROM tblfamiliausuario
WHERE Usuario_idUsuario = ?
AND statusMembro = 'ativo'
LIMIT 1
";

$stmtPapel = $conn->prepare($sqlPapel);

if (!$stmtPapel) {
    die("Erro SQL Papel: " . $conn->error);
}

$stmtPapel->bind_param("i", $idUsuario);
$stmtPapel->execute();

$resultPapel = $stmtPapel->get_result();
$dadosPapel = $resultPapel->fetch_assoc();

$papelUsuario = $dadosPapel['papel'] ?? null;


/*
|--------------------------------------------------------------------------
| BUSCAR DEPENDENTES (SOMENTE SE FOR RESPONSÁVEL)
|--------------------------------------------------------------------------
*/

$dependentes = null;

if ($papelUsuario === 'responsavel') {

    $sqlDependentes = "
    SELECT
        u.idUsuario,
        u.nomeUsuario,
        u.foto
    FROM tblfamiliausuario fuResp

    INNER JOIN tblfamiliausuario fuDep
        ON fuResp.Familia_idFamilia = fuDep.Familia_idFamilia

    INNER JOIN tblusuario u
        ON u.idUsuario = fuDep.Usuario_idUsuario

    WHERE fuResp.Usuario_idUsuario = ?
    AND fuResp.papel = 'responsavel'
    AND fuResp.statusMembro = 'ativo'

    AND fuDep.papel = 'dependente'
    AND fuDep.statusMembro = 'ativo'
    ";

    $stmtDep = $conn->prepare($sqlDependentes);

    if (!$stmtDep) {
        die("Erro SQL Dependentes: " . $conn->error);
    }

    $stmtDep->bind_param("i", $idUsuario);
    $stmtDep->execute();

    $dependentes = $stmtDep->get_result();
}


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-Estar 360 - Indicador (Pressão) </title>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const menuToggle = document.querySelector('.menu-toggle');
            const navegacao = document.querySelector('.Navegacao');

            menuToggle.addEventListener('click', () => {
                navegacao.classList.toggle('ativo');
            });

            document.querySelectorAll('.Navegacao a').forEach(link => {
                link.addEventListener('click', () => {
                    navegacao.classList.remove('ativo');
                });
            });
        });
    </script>

    <!-- API (Usabilidade) -->
    <script src="https://seeb-widget.pages.dev/widget.js" defer></script>

    <!-- Favicon -->
    <link rel="shortcut icon" href="icon/icon_BemEstar360.ico">

    <!-- CSS externo -->
    <link rel="stylesheet" href="Css/estilo.css">
    <link rel="stylesheet" href="Css/estiloIndicadores.css">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward" />

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Header -->
    <header class="TopoSite">
        <div class="Logo">
            <img class="ImgLogo" src="Img/logoBemEstar.png" alt="Logo Bem Estar 360">
        </div>

        <button class="menu-toggle" aria-label="Abrir menu">☰</button>

        <nav class="Navegacao">
            <ul>
                <li><a href="./index.php" data-lang="home">Home</a></li>
                <li><a href="./monitoramento.php" data-lang="monitoring">Monitoramento</a></li>
                <li><a href="./calendario.php" data-lang="">Agenda</a></li>
                <li><a href="./servicos.php" data-lang="services">Serviços</a></li>
                <li><a href="./quemSomos.php" data-lang="about">Quem somos</a></li>

                <?php if ($logado): ?>
                    <li class="perfil-menu">
                        <a href="/Saude_PI_DSM-main/perfil.php" id="perfil-btn" class="perfil-link">
                            <img src="<?= $foto ?>" alt="Foto de perfil" class="foto-perfil">
                            <span class="nome-perfil"><?= $nome ?></span>
                        </a>
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
                        <button id="logout-btn">🚪 Sair da Conta</button>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <script src="script.js"></script>
    <!-- Pressão Arterial -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>

    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>



    <section class="registroIndicador">
        <div class="container">
            <div class="leftSide">
                <h1>Registre a sua pressão arterial</h1>

                <p class="descricaoIndicador">
                    Monitorar sua pressão arterial regularmente ajuda na prevenção
                    de problemas cardiovasculares e no acompanhamento da sua saúde.
                    Registre suas medições de forma simples e acompanhe sua evolução.
                </p>

                <div class="leftImg">
                    <img src="./Img/medindoPressao.jpg" alt="Pressão">
                </div>
            </div>

            <div class="rightForms">
                <div class="family-dropdown">

                    <div class="family-selected" id="familySelected">

                        <img src="<?= $foto ?>" class="family-avatar">

                        <div class="family-info">
                            <strong><?= htmlspecialchars($nome) ?></strong>
                            <span>Responsável</span>
                        </div>

                        <span class="family-arrow">⌄</span>

                    </div>

                    <div class="family-list" id="familyList">

                        <div class="family-option ativo" data-id="<?= $idUsuario ?>">

                            <img src="<?= $foto ?>" class="family-avatar">

                            <div class="family-info">
                                <strong><?= htmlspecialchars($nome) ?></strong>
                                <span>Responsável</span>
                            </div>

                        </div>

                        <?php while ($dep = $dependentes->fetch_assoc()): ?>

                            <?php
                            $fotoDep = !empty($dep['foto'])
                                ? "uploads/" . $dep['foto']
                                : "Img/defaultUser.png";
                            ?>

                            <div class="family-option" data-id="<?= $dep['idUsuario'] ?>">

                                <img src="<?= $fotoDep ?>" class="family-avatar">

                                <div class="family-info">
                                    <strong><?= htmlspecialchars($dep['nomeUsuario']) ?></strong>
                                    <span>Dependente</span>
                                </div>

                            </div>

                        <?php endwhile; ?>

                    </div>

                </div>

                <form id="pressaoForm">

                    <input type="hidden" id="idUsuarioRegistro" name="idUsuarioRegistro" value="<?= $idUsuario ?>">
                    <div class="inputs-row">
                        <div class="input-group">
                            <label for="sistolica">Pressão Sistólica (mmHg)</label>
                            <input type="number" id="sistolica" name="sistolica" placeholder="Ex: 120" min="0" required>
                        </div>

                        <div class="input-group">
                            <label for="diastolica">Pressão Diastólica (mmHg)</label>
                            <input type="number" id="diastolica" name="diastolica" placeholder="Ex: 80" min="0"
                                required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="data">Data da Medição</label>
                        <input type="date" id="data" name="data" required>
                    </div>

                    <div class="input-group">
                        <label for="hora">Hora da Medição</label>
                        <input type="time" id="hora" name="hora" required>
                    </div>

                    <div class="input-group">
                        <label for="observacoes">Observações</label>
                        <textarea id="observacoes" name="observacoes" rows="3"
                            placeholder="Ex: medi após caminhada..."></textarea>
                    </div>

                    <button type="submit" id="salvarPressao">Salvar Registro</button>
                </form>
                <script src="./script_Registro/scripRegistroPressao.js"></script>
            </div>
        </div>
    </section>



    <section class="help">
        <div class="help-container">
            <h2>Como interpretar seus registros de pressão</h2>
            <p>Veja abaixo como entender os valores de pressão arterial registrados:</p>

            <div class="help-cards">
                <div class="help-card green">
                    <h3>Normal</h3>
                    <p>Pressão sistólica até <strong>120 mmHg</strong><br>
                        Pressão diastólica até <strong>80 mmHg</strong></p>
                    <p>✅ Está dentro do esperado, continue monitorando regularmente.</p>
                </div>

                <div class="help-card orange">
                    <h3>Atenção</h3>
                    <p>Pressão sistólica entre <strong>121-139 mmHg</strong><br>
                        Pressão diastólica entre <strong>81-89 mmHg</strong></p>
                    <p>⚠️ Fique atento! Pode indicar pré-hipertensão ou necessidade de ajustes no estilo de vida.</p>
                </div>

                <div class="help-card red">
                    <h3>Perigo</h3>
                    <p>Pressão sistólica ≥ <strong>140 mmHg</strong><br>
                        Pressão diastólica ≥ <strong>90 mmHg</strong></p>
                    <p>⛔ Procure orientação médica imediatamente!</p>
                </div>
            </div>
        </div>
    </section>




    <!-- Rodapé -->
    <footer class="footer">
        <div class="footerContainer">
            <!-- Logo e nome -->
            <div class="footerBrand">
                <img src="Img/Footer.png" alt="Bem Estar 360" class="footerLogo">
            </div>

            <div class="footerLinks">
                <ul>
                    <li><a href="./index.php" data-lang="footerHome">Home</a></li>
                    <li><a href="./monitoramento.php" data-lang="footerMonitoring">Monitoramento</a></li>
                    <li><a href="./calendario.php" data-lang="">Agenda</a></li>
                    <li><a href="./servicos.php" data-lang="footerServices">Serviços</a></li>
                    <li><a href="./quemSomos.php" data-lang="about">Quem somos</a></li>
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
    <script>
        document.getElementById("logout-btn").addEventListener("click", function () {
            const form = document.createElement("form");

            form.method = "POST";
            form.action = "php/usuario/logout.php";

            document.body.appendChild(form);

            form.submit();
        });

        document.addEventListener("DOMContentLoaded", () => {

            const selected =
                document.getElementById("familySelected");

            const list =
                document.getElementById("familyList");

            if (!selected || !list) {
                console.error("Dropdown não encontrado");
                return;
            }

            selected.addEventListener("click", () => {
                list.classList.toggle("show");
            });

            document
                .querySelectorAll(".family-option")
                .forEach(option => {

                    option.addEventListener("click", () => {

                        document.getElementById(
                            "idUsuarioRegistro"
                        ).value = option.dataset.id;

                        selected.innerHTML =
                            option.innerHTML +
                            '<span class="family-arrow">↓</span>';

                        list.classList.remove("show");

                    });

                });

        });


        
    </script>
</body>

</html>