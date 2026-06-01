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
    <title data-lang="title">Bem-Estar 360 - Quem Somos</title>
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
    <link rel="stylesheet" href="./CSS/estiloQuemSomos.css">

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
                    <button id="btnNotificacao" class="notificacao-btn">
                        <img id="iconeNotificacao" src="Img/Corres_Fechada.png" alt="Notificações" class="Notificacao">
                    </button>
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
    <script src="scriptTraducao.js"></script>


    <main class="section" aria-labelledby="quem-somos">
        <header class="header">
            <div>
                <h2 id="quem-somos" data-lang="whoWeAre">Quem Somos</h2>
                <p data-lang="teamDescription">
                    Conheça a equipe responsável pelo Bem-Estar 360 — profissionais dedicados
                    ao desenvolvimento de soluções digitais focadas em saúde, acessibilidade e inovação.
                </p>
            </div>
            <div>
                <small style="color:var(--muted)" data-lang="availableForProjects">Disponível para projetos: Web •
                    Mobile • UI/UX</small>
            </div>
        </header>
        <section class="team-grid" role="list">

            <!-- Integrante 1 -->
            <article class="card" role="listitem" aria-labelledby="nome-1">
                <div class="avatar" aria-hidden="false">
                    <img src="./Img/adriano.jpg" alt="Foto de Adriano" />
                </div>
                <div class="name" id="nome-1">Adriano Rodrigues Araújo</div>
                <div class="role" data-lang="role1">Desenvolvedor Back-End</div>
                <p class="bio" data-lang="specialty">Especialidade em linguagem de programação:</p>
                <div class="tags" aria-hidden="true">
                    <span class="tag">Javascript/Java/Python</span>
                    <span class="tag">PHP/Node.JS</span>
                    <span class="tag">MySql/SQL server</span>
                </div>
                <div class="links">
                    <a href="https://www.linkedin.com/in/adriano-rodrigues-de-araujo-4a5078124" title="LinkedIn Adriano"
                        aria-label="LinkedIn Adriano">

                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="4" fill="none" />
                            <path
                                d="M6.94 8.94h2.5V18.5h-2.5zM8.19 6.5a1.26 1.26 0 110 2.52 1.26 1.26 0 010-2.52zM11.56 8.94h2.4v1.28h.03c.33-.63 1.15-1.29 2.36-1.29 2.52 0 2.99 1.66 2.99 3.81V18.5h-2.5v-4.3c0-1.03-.02-2.36-1.44-2.36-1.44 0-1.66 1.12-1.66 2.28v4.38h-2.5z"
                                fill="currentColor" />
                        </svg>
                    </a>

                    <a href="https://github.com/AdrianoRAraujo365" title="GitHub Adriano" aria-label="GitHub Adriano">
                        <img src="./Img/github.png" alt="GitHub">
                    </a>
                </div>
            </article>

            <!-- Integrante 2 -->
            <article class="card" role="listitem" aria-labelledby="nome-2">
                <div class="avatar">
                    <img src="./Img/pedro.jpg" alt="Foto de Pedro" />
                </div>
                <div class="name" id="nome-2">Pedro Henrique Raimundo</div>
                <div class="role" data-lang="role2">Desenvolvedor Full-Stack</div>
                <p class="bio" data-lang="specialty2">Especialista em Banco de Dados: </p>
                <div class="tags" aria-hidden="true">
                    <span class="tag">PHP</span>
                    <span class="tag">MySQL/SQL</span>
                    <span class="tag">JavaScript</span>
                </div>
                <div class="links">
                    <a href="https://www.linkedin.com/in/pedrohraimundo/" title="LinkedIn Pedro"
                        aria-label="LinkedIn Pedro">
                        <!-- LinkedIn svg -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="4" fill="none" />
                            <path
                                d="M6.94 8.94h2.5V18.5h-2.5zM8.19 6.5a1.26 1.26 0 110 2.52 1.26 1.26 0 010-2.52zM11.56 8.94h2.4v1.28h.03c.33-.63 1.15-1.29 2.36-1.29 2.52 0 2.99 1.66 2.99 3.81V18.5h-2.5v-4.3c0-1.03-.02-2.36-1.44-2.36-1.44 0-1.66 1.12-1.66 2.28v4.38h-2.5z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://github.com/Raymondexe" title="GitHub Adriano" aria-label="GitHub Adriano">
                        <img src="./Img/github.png" alt="GitHub">
                    </a>
                </div>
            </article>

            <!-- Integrante 3 -->
            <article class="card" role="listitem" aria-labelledby="nome-3">
                <div class="avatar">
                    <img src="./Img/esau.jpg" alt="Foto de Esaú" />
                </div>
                <div class="name" id="nome-3">Esaú Tenório</div>
                <div class="role" data-lang="role3">Desenvolvimento de Software em Java</div>
                <p class="bio" data-lang="specialty3">Focado para carreira na área de:</p>
                <div class="tags" aria-hidden="true">
                    <span class="tag">Dados</span>
                    <span class="tag">Cloud</span>
                    <span class="tag">Acessibilidade</span>
                </div>
                <div class="links">
                    <a href="https://www.linkedin.com/in/esau-tenorio/" title="LinkedIn Esau"
                        aria-label="LinkedIn Esau">
                        <!-- LinkedIn svg -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="4" fill="none" />
                            <path
                                d="M6.94 8.94h2.5V18.5h-2.5zM8.19 6.5a1.26 1.26 0 110 2.52 1.26 1.26 0 010-2.52zM11.56 8.94h2.4v1.28h.03c.33-.63 1.15-1.29 2.36-1.29 2.52 0 2.99 1.66 2.99 3.81V18.5h-2.5v-4.3c0-1.03-.02-2.36-1.44-2.36-1.44 0-1.66 1.12-1.66 2.28v4.38h-2.5z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://github.com/sep-tenorio" title="GitHub Adriano" aria-label="GitHub Adriano">
                        <img src="./Img/github.png" alt="GitHub">
                    </a>
                </div>
                </div>
            </article>

            <!-- Integrante 4 -->
            <article class="card" role="listitem" aria-labelledby="nome-4">
                <div class="avatar">
                    <img src="./Img/julia.jpg" alt="Foto de Júlia" />
                </div>
                <div class="name" id="nome-4">Júlia Santana</div>
                <div class="role" data-lang="role4">Front-end Developer</div>
                <p class="bio" data-lang="specialty4">Foca em interfaces acessíveis e performáticas:</p>
                <div class="tags" aria-hidden="true">
                    <span class="tag">HTML</span>
                    <span class="tag">CSS</span>
                    <span class="tag">Flutter</span>
                </div>
                <div class="links">
                    <a href="https://www.linkedin.com/in/j%C3%BAlia-queiroz-de-santana-16902b366/"
                        title="LinkedIn Julia" aria-label="LinkedIn Julia">
                        <!-- LinkedIn svg -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="4" fill="none" />
                            <path
                                d="M6.94 8.94h2.5V18.5h-2.5zM8.19 6.5a1.26 1.26 0 110 2.52 1.26 1.26 0 010-2.52zM11.56 8.94h2.4v1.28h.03c.33-.63 1.15-1.29 2.36-1.29 2.52 0 2.99 1.66 2.99 3.81V18.5h-2.5v-4.3c0-1.03-.02-2.36-1.44-2.36-1.44 0-1.66 1.12-1.66 2.28v4.38h-2.5z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://github.com/juliasantana1508-hub" title="GitHub Julia" aria-label="GitHub Julia">
                        <img src="./Img/github.png" alt="GitHub">
                    </a>
                </div>
                </div>
            </article>

            <!-- Integrante 5 -->
            <article class="card" role="listitem" aria-labelledby="nome-5">
                <div class="avatar">
                    <img src="./Img/karen.jpg" alt="Foto de Karen" />
                </div>
                <div class="name" id="nome-5">Karen Larissa Lima dos Santos</div>
                <div class="role" data-lang="role5">Back-End Developer</div>
                <p class="bio" data-lang="specialty5">Desenvolvedora Back-end</p>
                <div class="tags" aria-hidden="true">
                    <span class="tag">Java</span>
                    <span class="tag">MySQL/SQL</span>
                    <span class="tag">PHP</span>
                </div>
                <div class="links">
                    <a href="https://www.linkedin.com/in/karen-larissa-lima-dos-santos/" aria-label="LinkedIn Karen">
                        <!-- LinkedIn svg -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="4" fill="none" />
                            <path
                                d="M6.94 8.94h2.5V18.5h-2.5zM8.19 6.5a1.26 1.26 0 110 2.52 1.26 1.26 0 010-2.52zM11.56 8.94h2.4v1.28h.03c.33-.63 1.15-1.29 2.36-1.29 2.52 0 2.99 1.66 2.99 3.81V18.5h-2.5v-4.3c0-1.03-.02-2.36-1.44-2.36-1.44 0-1.66 1.12-1.66 2.28v4.38h-2.5z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://github.com/karenlarissa01" title="GitHub Julia" aria-label="GitHub Karen">
                        <img src="./Img/github.png" alt="GitHub">
                    </a>
                </div>
            </article>

            <!-- Integrante 6 -->
            <article class="card" role="listitem" aria-labelledby="nome-6">
                <div class="avatar">
                    <img src="./Img/gabriel.jpg" alt="Foto de Gabriel" />
                </div>
                <div class="name" id="nome-6">Gabriel Gomide de Souza Babo </div>
                <div class="role" data-lang="role6">Desenvolvedor Full-Stack </div>
                <p class="bio" data-lang="specialty6"></p>
                <div class="tags" aria-hidden="true">
                    <span class="tag">HTML/CSS</span>
                    <span class="tag">Java</span>
                    <span class="tag">C#</span>
                </div>
                <div class="links">
                    <a href="https://www.linkedin.com/in/gabriel-gomide-387b21250/" aria-label="LinkedIn Gabriel">
                        <!-- LinkedIn svg -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="24" rx="4" fill="none" />
                            <path
                                d="M6.94 8.94h2.5V18.5h-2.5zM8.19 6.5a1.26 1.26 0 110 2.52 1.26 1.26 0 010-2.52zM11.56 8.94h2.4v1.28h.03c.33-.63 1.15-1.29 2.36-1.29 2.52 0 2.99 1.66 2.99 3.81V18.5h-2.5v-4.3c0-1.03-.02-2.36-1.44-2.36-1.44 0-1.66 1.12-1.66 2.28v4.38h-2.5z"
                                fill="currentColor" />
                        </svg>
                    </a>
                    <a href="https://github.com/Gomide-Gabriel" title="GitHub Gabriel" aria-label="GitHub Gabriel">
                        <img src="./Img/github.png" alt="GitHub">
                    </a>
                </div>
                </div>
            </article>
        </section>
    </main>


    <section class="info-projeto">
        <h3>🚀 Sobre o Bem-Estar 360</h3>
        <p>
            O Bem-Estar 360 nasceu como um projeto acadêmico com o objetivo de unir
            tecnologia, acessibilidade e monitoramento inteligente de saúde em uma única
            plataforma moderna e intuitiva.
        </p>

        <div class="info-grid">
            <div class="info-card">
                <h4>💡 Nossa Missão</h4>
                <p>
                    Desenvolver soluções digitais que auxiliem pessoas e profissionais
                    da saúde no acompanhamento diário de indicadores importantes.
                </p>
            </div>

            <div class="info-card">
                <h4>📊 Tecnologia & Dados</h4>
                <p>
                    Integramos monitoramento em tempo real, gráficos inteligentes,
                    histórico médico e futuramente análises clínicas automatizadas.
                </p>
            </div>

            <div class="info-card">
                <h4>🛡️ Segurança</h4>
                <p>
                    Priorizamos privacidade, organização e proteção de dados sensíveis
                    relacionados à saúde dos usuários.
                </p>
            </div>

            <div class="info-card">
                <h4>🌍 Futuro do Projeto</h4>
                <p>
                    Atualmente o Bem-Estar 360 encontra-se em fase de projeto acadêmico,
                    mas possui potencial para evoluir futuramente para um produto real
                    voltado ao mercado de saúde digital.
                </p>
            </div>
        </div>
    </section>

    <br>

    <div class="direitos">
        © 2025 Bem-Estar 360 — Todos os direitos reservados.<br>
    </div>

    <br>

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
    </script>
    <script>
        document.getElementById("logout-btn").addEventListener("click", function () {
            const form = document.createElement("form");

            form.method = "POST";
            form.action = "php/usuario/logout.php";

            document.body.appendChild(form);

            form.submit();
        });
    </script>
</body>

</html>