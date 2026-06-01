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



/* descobrir família do usuário */
$stmtFamilia = $conn->prepare("
    SELECT Familia_idFamilia, papel
    FROM tblFamiliaUsuario
    WHERE Usuario_idUsuario = ?
    LIMIT 1
");

$stmtFamilia->bind_param("i", $id);
$stmtFamilia->execute();
$dadosFamilia = $stmtFamilia->get_result()->fetch_assoc();

$idFamilia = $dadosFamilia['Familia_idFamilia'] ?? null;
$papelUsuario = $dadosFamilia['papel'] ?? null;

$membrosFamilia = [];

if ($idFamilia) {
    $stmtMembros = $conn->prepare("
        SELECT 
            u.idUsuario,
            u.nomeUsuario,
            u.foto,
            fu.papel
        FROM tblFamiliaUsuario fu
        INNER JOIN tblUsuario u 
            ON u.idUsuario = fu.Usuario_idUsuario
        WHERE fu.Familia_idFamilia = ?
        AND fu.statusMembro = 'ativo'
    ");

    $stmtMembros->bind_param("i", $idFamilia);
    $stmtMembros->execute();
    $membrosFamilia = $stmtMembros->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bem-Estar 360 - Calendário</title>

    <!-- API (Usabilidade) -->
    <script src="https://seeb-widget.pages.dev/widget.js" defer></script>

    <!-- Favicon -->
    <link rel="shortcut icon" href="icon/icon_BemEstar360.ico">

    <!-- CSS -->
    <link rel="stylesheet" href="Css/estilo.css">
    <link rel="stylesheet" href="Css/estiloCalendario.css">

    <!-- Google Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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

    <?php
    $stmtDependentes = $conn->prepare("
    SELECT 
        u.idUsuario,
        u.nomeUsuario,
        u.foto,
        fu.papel
    FROM tblFamiliaUsuario fu
    INNER JOIN tblUsuario u
        ON u.idUsuario = fu.Usuario_idUsuario
    WHERE fu.Familia_idFamilia = ?
    AND fu.statusMembro = 'ativo'
");

    $stmtDependentes->bind_param("i", $idFamilia);
    $stmtDependentes->execute();

    $dependentes = $stmtDependentes->get_result()->fetch_all(MYSQLI_ASSOC);
    ?>


    <?php
    $nomeFamilia = "Família";

    foreach ($dependentes as $membro) {
        if ($membro['papel'] === 'responsavel') {
            $primeiroNome = explode(' ', $membro['nomeUsuario'])[0];
            $nomeFamilia = "Família " . $primeiroNome;
            break;
        }
    }
    ?>


    <div class="aviso-calendario">
        <div class="icone-aviso">
            <img src="Img/info.png" alt="Informação">
        </div>

        <div class="texto-aviso">
            <strong>Como funciona a agenda</strong>
            <p>
                Responsáveis podem visualizar e gerenciar eventos da família.
                Dependentes visualizam os membros da família, mas acessam apenas sua própria agenda.
            </p>
        </div>
    </div>


    <div class="app">
        <aside class="sidebar">
            <div class="dependentes">
                <h4><?= $nomeFamilia ?></h4>

                <div class="busca-wrapper">
                    <input type="text" id="buscarMembro" placeholder="Buscar membro...">
                </div>

                <div class="lista-pessoas">
                    <?php foreach ($dependentes as $dep): ?>
                        <div class="person <?= $dep['idUsuario'] == $id ? 'ativo' : '' ?>"
                            onclick="selecionarUsuario(<?= $dep['idUsuario'] ?>, this)"> <img class="img"
                                src="<?= !empty($dep['foto']) && file_exists('uploads/' . $dep['foto']) ? 'uploads/' . $dep['foto'] : 'Img/defaultUser.png' ?>">
                            <p><?= $dep['nomeUsuario'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </aside>

        <!-- CALENDÁRIO -->
        <main class="main">

            <div class="calendar">

                <div class="header">
                    <button id="prev"> <img class="seta" src="Img/seta-esquerda.png" alt="Anterior"> </button>
                    <h2 id="mesAno"></h2> <button id="next"> <img class="seta" src="Img/seta-direita.png" alt="Próximo">
                </div>

                <div class="calendario-duplo">
                    <div class="mes">
                        <div class="dias-semana">
                            <div>Segunda</div>
                            <div>Terça</div>
                            <div>Quarta</div>
                            <div>Quinta</div>
                            <div>Sexta</div>
                            <div>Sábado</div>
                            <div>Domingo</div>
                        </div>
                        <div id="dias"></div>
                    </div>

                </div>

            </div>

        </main>

    </div>

    <!-- MODAL -->

    <div id="modalExame" class="modal-exame">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <span class="modal-sub">Novo evento</span>
                    <h2 id="dataSelecionada"></h2>
                </div>
                <button class="btn-fechar" onclick="fecharModal()">✕</button>
            </div>

            <div class="modal-body">

                <div class="input-group">
                    <label>Local</label>
                    <input type="text" id="local" placeholder="Ex: Hospital São Paulo">
                </div>

                <div class="input-group">
                    <label>Tipo</label>
                    <select id="tipo">
                        <option value="">Selecione</option>
                        <option value="Consulta">Consulta</option>
                        <option value="Exame">Exame</option>
                        <option value="Retorno">Retorno</option>
                    </select>
                </div>

                <div class="input-row">
                    <div class="input-group">
                        <label>Médico</label>
                        <input type="text" id="medico" placeholder="Dr. João">
                    </div>

                    <div class="input-group">
                        <label>Horário</label>
                        <input type="time" id="horario">
                    </div>
                </div>

                <div class="input-group">
                    <label>O que levar</label>

                    <div class="check-list">
                        <label><input type="checkbox" value="Documento"> Documento</label>
                        <label><input type="checkbox" value="Carteirinha SUS"> Carteirinha SUS</label>
                        <label><input type="checkbox" value="Exames anteriores"> Exames anteriores</label>
                        <label><input type="checkbox" value="Receita médica"> Receita médica</label>
                    </div>
                </div>

                <button id="btnSalvar" class="btn-salvar" onclick="salvarExame()">
                    Salvar evento
                </button>
            </div>

        </div>

    </div>

    <div id="modalLista" class="modal-exame">
        <div class="modal-card grande">
            <div class="modal-header">
                <h2 id="tituloLista"></h2>
                <button class="btn-fechar" onclick="fecharLista()">✕</button>
            </div>
            <div id="listaEventos"></div>
            <button id="btnNovo" class="btn-salvar" onclick="abrirNovoEvento()">
                + Novo evento
            </button>

        </div>

    </div>

    <br><br>

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

    <script src="script.js"></script>
    <script src="scriptTraducao.js"></script>


    <script>
        const usuarioLogado = <?= $_SESSION['idUsuario'] ?>;
        const papelUsuario = "<?= $papelUsuario ?>";
    </script>

    <script src="Scripts/ScriptCalendario/scriptCalendario.js"></script>

    <!-- Bootstrap JS -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
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