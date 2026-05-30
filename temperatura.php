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
    <title>Bem-Estar 360 - Indicador (Temperatura)</title>
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

    <script src="./JS/script.js"></script>

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
            <img class="ImgLogo" src="Img/bemEstar.webp" alt="Logo Bem Estar 360">
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
    <!-- Pressão Arterial -->


    <section class="registroIndicador">
        <div class="container">
            <h1>Registre a sua temperatura corporal</h1>
            <div class="leftImg">
                <img src="./Img/medindoTemperatura.webp" alt="Temperatura">
            </div>

            <div class="rightForms">
                <form id="temperaturaForm" method="POST" action="./php/usuario/registros/salvarTemperatura.php">
                    <div class="input-group">
                        <label for="valorTemperatura">Temperatura (°C)</label>
                        <input type="number" id="valorTemperatura" name="valorTemperatura" placeholder="Ex: 36.5"
                            step="0.1" min="30" max="45" required>
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
                            placeholder="Ex: medi após atividade física..."></textarea>
                    </div>

                    <button type="submit" id="salvarTemperatura">Salvar Registro</button>
                </form>

                <script src="./script_Registro/scriptRegistroTemperatura.js"></script>

            </div>
        </div>
    </section>



    <section class="help">
        <div class="help-container">
            <h2>Como interpretar seus registros de temperatura</h2>
            <p>Veja abaixo como entender os valores de temperatura corporal registrados:</p>

            <div class="help-cards">
                <div class="help-card green">
                    <h3>Normal</h3>
                    <p>Temperatura entre <strong>36,0 – 37,2 °C</strong></p>
                    <p>✅ Está dentro do esperado, continue monitorando regularmente.</p>
                </div>

                <div class="help-card orange">
                    <h3>Febre leve / Atenção</h3>
                    <p>Temperatura entre <strong>37,3 – 38,0 °C</strong></p>
                    <p>⚠️ Fique atento! Pode indicar início de febre. Hidrate-se e repita a medição.</p>
                </div>

                <div class="help-card red">
                    <h3>Febre alta / Perigo</h3>
                    <p>Temperatura ≥ <strong>38,1 °C</strong></p>
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