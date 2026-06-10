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
ÚLTIMO BATIMENTO
*/
$sqlBatimentos = "
SELECT bpm
FROM tblbatimentos
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtBatimentos = $conn->prepare($sqlBatimentos);
$stmtBatimentos->bind_param("i", $id);
$stmtBatimentos->execute();

$resultBatimentos = $stmtBatimentos->get_result();
$batimentos = $resultBatimentos->fetch_assoc();

/*
ÚLTIMA GLICEMIA
*/
$sqlGlicemia = "
SELECT valor_glicemia
FROM tblglicemia
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtGlicemia = $conn->prepare($sqlGlicemia);
$stmtGlicemia->bind_param("i", $id);
$stmtGlicemia->execute();

$resultGlicemia = $stmtGlicemia->get_result();
$glicemia = $resultGlicemia->fetch_assoc();

/*
ÚLTIMA TEMPERATURA
*/
$sqlTemperatura = "
SELECT temperatura
FROM tbltemperatura
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtTemperatura = $conn->prepare($sqlTemperatura);
$stmtTemperatura->bind_param("i", $id);
$stmtTemperatura->execute();

$resultTemperatura = $stmtTemperatura->get_result();
$temperatura = $resultTemperatura->fetch_assoc();

/*
ÚLTIMA PRESSÃO
*/
$sqlPressao = "
SELECT sistolica, diastolica
FROM tblpressao
WHERE Usuario_idUsuario = ?
ORDER BY data_hora DESC
LIMIT 1
";

$stmtPressao = $conn->prepare($sqlPressao);
$stmtPressao->bind_param("i", $id);
$stmtPressao->execute();

$resultPressao = $stmtPressao->get_result();
$pressao = $resultPressao->fetch_assoc();




/*
REGISTROS DO USUÁRIO
*/

$registros = [];

/*
BATIMENTOS
*/
$sqlBatimentos = "
SELECT 
    DATE(data_hora) as data,
    bpm
FROM tblbatimentos
WHERE Usuario_idUsuario = ?
";

$stmtBatimentos = $conn->prepare($sqlBatimentos);
$stmtBatimentos->bind_param("i", $id);
$stmtBatimentos->execute();

$resultBatimentos = $stmtBatimentos->get_result();

while ($row = $resultBatimentos->fetch_assoc()) {

    $data = $row['data'];

    if (!isset($registros[$data])) {
        $registros[$data] = [];
    }

    $registros[$data]['batimentos'] = $row['bpm'] . " bpm";
}

/*
TEMPERATURA
*/
$sqlTemperatura = "
SELECT 
    DATE(data_hora) as data,
    temperatura
FROM tbltemperatura
WHERE Usuario_idUsuario = ?
";

$stmtTemperatura = $conn->prepare($sqlTemperatura);
$stmtTemperatura->bind_param("i", $id);
$stmtTemperatura->execute();

$resultTemperatura = $stmtTemperatura->get_result();

while ($row = $resultTemperatura->fetch_assoc()) {

    $data = $row['data'];

    if (!isset($registros[$data])) {
        $registros[$data] = [];
    }

    $registros[$data]['temperatura'] = $row['temperatura'] . " °C";
}

/*
GLICEMIA
*/
$sqlGlicemia = "
SELECT 
    DATE(data_hora) as data,
    valor_glicemia,
    tipo_medicao
FROM tblglicemia
WHERE Usuario_idUsuario = ?
";

$stmtGlicemia = $conn->prepare($sqlGlicemia);
$stmtGlicemia->bind_param("i", $id);
$stmtGlicemia->execute();

$resultGlicemia = $stmtGlicemia->get_result();

while ($row = $resultGlicemia->fetch_assoc()) {

    $data = $row['data'];

    if (!isset($registros[$data])) {
        $registros[$data] = [];
    }

    $registros[$data]['glicemia'] =
        $row['valor_glicemia'] .
        " mg/dL (" .
        $row['tipo_medicao'] .
        ")";
}

/*
PRESSÃO
*/
$sqlPressao = "
SELECT 
    DATE(data_hora) as data,
    sistolica,
    diastolica
FROM tblpressao
WHERE Usuario_idUsuario = ?
";

$stmtPressao = $conn->prepare($sqlPressao);
$stmtPressao->bind_param("i", $id);
$stmtPressao->execute();

$resultPressao = $stmtPressao->get_result();

while ($row = $resultPressao->fetch_assoc()) {

    $data = $row['data'];

    if (!isset($registros[$data])) {
        $registros[$data] = [];
    }

    $registros[$data]['pressao'] =
        $row['sistolica'] .
        "/" .
        $row['diastolica'];
}



/*
DADOS DOS GRÁFICOS
*/

$dadosGrafico = [

    "batimentos" => [],
    "temperatura" => [],
    "glicemia" => [],
    "pressao" => []

];

/*
BATIMENTOS
*/
$sql = "
SELECT 
    DATE(data_hora) as data,
    bpm
FROM tblbatimentos
WHERE Usuario_idUsuario = ?
ORDER BY data_hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $dadosGrafico["batimentos"][] = [
        "data" => $row["data"],
        "valor" => $row["bpm"]
    ];

}

/*
TEMPERATURA
*/
$sql = "
SELECT 
    DATE(data_hora) as data,
    temperatura
FROM tbltemperatura
WHERE Usuario_idUsuario = ?
ORDER BY data_hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $dadosGrafico["temperatura"][] = [
        "data" => $row["data"],
        "valor" => $row["temperatura"]
    ];

}

/*
GLICEMIA
*/
$sql = "
SELECT 
    DATE(data_hora) as data,
    valor_glicemia
FROM tblglicemia
WHERE Usuario_idUsuario = ?
ORDER BY data_hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $dadosGrafico["glicemia"][] = [
        "data" => $row["data"],
        "valor" => $row["valor_glicemia"]
    ];

}

/*
PRESSÃO
*/
$sql = "
SELECT 
    DATE(data_hora) as data,
    sistolica,
    diastolica
FROM tblpressao
WHERE Usuario_idUsuario = ?
ORDER BY data_hora ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $dadosGrafico["pressao"][] = [
        "data" => $row["data"],
        "sistolica" => $row["sistolica"],
        "diastolica" => $row["diastolica"]
    ];

}



$stmtConvites = $conn->prepare("
    SELECT 
        c.idConvite,
        c.validadeConvite,
        c.statusConvite,
        u.nomeUsuario
    FROM tblConvite c
    INNER JOIN tblResponsavel r
        ON r.idResponsavel = c.Responsavel_idResponsavel
    INNER JOIN tblUsuario u
        ON u.idUsuario = r.Login_Usuario_idUsuario
    WHERE c.Usuario_idUsuario = ?
    AND c.statusConvite = 'PENDENTE'
");

$stmtConvites->bind_param("i", $id);
$stmtConvites->execute();
$resultConvites = $stmtConvites->get_result();
$totalConvites = $resultConvites->num_rows;
$resultConvites->data_seek(0);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saúde 360 - Monitoramento</title>

    <!-- API (Usabilidade) -->
    <script src="https://seeb-widget.pages.dev/widget.js" defer></script>

    <!-- Favicon -->
    <link rel="shortcut icon" href="icon/icon_BemEstar360.ico">

    <!-- CSS externo -->
    <link rel="stylesheet" href="Css/estilo.css">
    <link rel="stylesheet" href="./Css/estiloMonitoramento.css">


    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <script src="scriptTraducao.js"></script>

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




    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modalNotificacoes = document.getElementById("modalNotificacoes");
            const btnNotificacao = document.getElementById("btnNotificacao");

            console.log(btnNotificacao);
            console.log(modalNotificacoes);

            if (btnNotificacao && modalNotificacoes) {
                btnNotificacao.addEventListener("click", function () {
                    modalNotificacoes.style.display = "flex";
                });

                window.fecharNotificacoes = function () {
                    modalNotificacoes.style.display = "none";
                };

                window.addEventListener("click", function (e) {
                    if (e.target === modalNotificacoes) {
                        modalNotificacoes.style.display = "none";
                    }
                });
            }
        });
    </script>


    <br><br>

    <!-- Conteúdo principal -->
    <main class="conteudo">

        <section class="monitoramento-container">
            <h2>Últimos registros</h2>
            <div class="cards">
                <div class="card" id="card-batimentos">
                    <h3>❤️ Batimentos</h3>
                    <p id="batimentos">
                        <?= $batimentos['bpm'] ?? '--' ?> bpm
                    </p>
                </div>

                <div class="card" id="card-glicemia">
                    <h3>🩸 Glicemia</h3>
                    <p id="glicemia">
                        <?= $glicemia['valor_glicemia'] ?? '--' ?> mg/dL
                    </p>
                </div>

                <div class="card" id="card-temperatura">
                    <h3>🌡️ Temperatura</h3>
                    <p id="temperatura">
                        <?= $temperatura['temperatura'] ?? '--' ?> °C
                    </p>
                </div>

                <div class="card" id="card-pressao">
                    <h3>🩺 Pressão</h3>
                    <p id="pressao">
                        <?= isset($pressao['sistolica'])
                            ? $pressao['sistolica'] . '/' . $pressao['diastolica']
                            : '--'
                            ?> mmHg
                    </p>
                </div>
            </div>
        </section>

        <br><br>

        <div class="calendar-container">
            <div class="calendar-header">
                <button id="prevMonth">&#8249;</button>
                <span id="monthYear"></span>
                <button id="nextMonth">&#8250;</button>
            </div>
            <div class="calendar-weekdays">
                <div>D</div>
                <div>S</div>
                <div>T</div>
                <div>Q</div>
                <div>Q</div>
                <div>S</div>
                <div>S</div>
            </div>
            <div class="calendar-days" id="calendarDays"></div>
            <div id="dayRecords" class="day-records">Selecione um dia para ver os registros</div>
        </div>


        <script>
            const registros = <?= json_encode($registros) ?>;
        </script>

        <script src="./script_Registro/scriptCalendario.js"></script>



        <br><br>



        <!-- <div class="acoes-monitoramento">
            <a href="monitoramento_pdf.php" class="btn-pdf">
                📄 Baixar Histórico Completo
            </a>
        </div> -->

    </main>

    <section ng-app="monitorApp" ng-controller="monitorCtrl" class="grafico-container">
        <h2>📊 Gráfico de Monitoramento</h2>

        <div class="controle-grafico">
            <label>Selecionar indicador:</label>
            <select ng-model="tipoSelecionado" ng-change="atualizarGrafico()">
                <option value="batimentos">Batimentos</option>
                <option value="glicemia">Glicemia</option>
                <option value="pressao">Pressão</option>
                <option value="temperatura">Temperatura</option>
            </select>
        </div>
        <canvas id="graficoMonitoramento"></canvas>
    </section>

    <script>
        const dadosGrafico = <?= json_encode($dadosGrafico) ?>;
    </script>

    <script src="./script_Registro/graficoRegistro.js"></script>



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

    <!-- JS do menu mobile -->
    <script>
        const toggle = document.querySelector(".menu-toggle");
        const menu = document.querySelector(".Navegacao ul");

        toggle.addEventListener("click", () => {
            menu.classList.toggle("show");
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