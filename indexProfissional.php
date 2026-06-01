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
    <title>Bem-Estar 360 - Home Page </title>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- API (Usabilidade) -->
    <script src="https://seeb-widget.pages.dev/widget.js" defer></script>

    <!-- Favicon -->
    <link rel="shortcut icon" href="icon/icon_BemEstar360.ico">

    <!-- CSS externo -->
    <link rel="stylesheet" href="Css/estilo.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
                <li><a href="./indexProfissional.php" data-lang="home">Home</a></li>
                <li><a href="./calendario.php" data-lang="">Dashboard</a></li>
                <li><a href="./calendario.php" data-lang="">Agenda</a></li>

                <?php if ($logado): ?>
                    <li class="perfil-menu">
                        <a href="/Saude_PI_DSM-main/perfil.php" id="perfil-btn" class="perfil-link">
                            <img src="<?= $foto ?>" alt="Foto de perfil" class="foto-perfil">
                            <span class="nome-perfil">Dr(a) <?= $nome ?></span>
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

    <section class="pro-welcome">
        <div class="pro-hero">
            <div class="pro-hero-text">
                <h1>
                    Bem-vindo(a), <span>Dr(a).
                        <?= $nome ?? 'Profissional' ?>
                    </span>
                </h1>

                <p>
                    Seu painel clínico central está pronto.
                    Gerencie consultas, acompanhe pacientes e visualize indicadores de forma rápida e precisa.
                </p>

                <div class="pro-actions">

                    <button class="btn-consulta" id="abrirConsulta">
                        <i class="fa-solid fa-stethoscope"></i>
                        Iniciar Consulta
                    </button>

                    <a href="consultas.php" class="btn-primary">
                        📅 Consultas do dia
                    </a>

                    <a href="dashboard.php" class="btn-secondary">
                        📊 Acessar Dashboard
                    </a>
                </div>


            </div>

            <div class="pro-hero-card">
                <h3>Resumo rápido</h3>
                <div class="mini-stats">
                    <div>
                        <span>Hoje</span>
                        <strong>0 Consultas</strong>
                    </div>

                    <div>
                        <span>Semana</span>
                        <strong>0 Pacientes</strong>
                    </div>

                    <div>
                        <span>Status</span>
                        <strong>Online</strong>
                    </div>
                </div>

            </div>

        </div>

    </section>

    <div class="modal-consulta" id="modalBuscaPaciente">

        <div class="modal-content-consulta">

            <span class="fechar-modal" id="fecharBusca">&times;</span>

            <h2>Iniciar Consulta</h2>

            <p>
                Digite o código único do paciente.
            </p>

            <input type="text" id="codigoPaciente" placeholder="Ex: BSTR-EDC293">

            <button type="button" id="buscarPacienteBtn">
                Buscar Paciente
            </button>
            <div id="resultadoBusca"></div>

        </div>

    </div>

    <div class="modal-prontuario" id="modalProntuario">
        <div class="prontuario-content">

            <div class="fechar-prontuario-area">

                <button id="fecharProntuarioBtn" class="btn-fechar-prontuario">
                    ✖
                </button>

            </div>

            <div class="topo-prontuario">
                <img id="fotoPaciente" src="Img/defaultUser.png" class="foto-paciente">
                <div>
                    <h2 id="nomePaciente"></h2>
                    <p>
                        Código:
                        <span id="codigoPacienteTexto"></span>
                    </p>

                    <p>
                        Tipo sanguíneo:
                        <span id="tipoSanguineoTexto"></span>
                    </p>
                </div>
            </div>

            <div class="alertas-medicos">
                <div class="alerta-card">
                    <h3>Alergias</h3>
                    <p id="alergiasPaciente"></p>
                </div>

                <div class="alerta-card">
                    <h3>Doenças Crônicas</h3>
                    <p id="doencasPaciente"></p>
                </div>
            </div>

            <div class="acoes-consulta">

                <button class="btn-secundario" id="btnHistorico">
                    Histórico Completo
                </button>

                <button class="btn-secundario" id="btnGraficos">
                    Ver Gráficos
                </button>

                <button class="btn-secundario" id="btnMedicamento">
                    Adicionar medicamento
                </button>

                <button class="btn-secundario" onclick="verMedicamentos(window.idPacienteAtual)">
                    Ver Medicamentos
                </button>

            </div>

            <div id="areaDinamicaConsulta"></div>



            <br><br>
            <textarea id="observacoesConsulta" placeholder="Observações médicas..."></textarea>

            <!-- <input type="file" id="receitaConsulta"> -->

            <button class="btn-finalizar">
                Finalizar Consulta
            </button>

        </div>

    </div>

    <div id="templateMedicamento" style="display:none;">

        <div class="box-dinamica medicamento-box">

            <h2 class="titulo-medicamento">
                Adicionar Medicamento para o paciente:
                <span id="nomePacienteMedicamento"></span>
            </h2>

            <div class="grid-medicamento">

                <div class="campo">
                    <label>Nome do medicamento</label>
                    <input type="text" id="nomeMedicamento" placeholder="Ex: Dipirona" class="input-consulta">
                </div>

                <div class="campo">
                    <label>Dosagem</label>
                    <select id="dosagemMedicamento" class="input-consulta">
                        <option value="">Selecione</option>
                        <option value="5mg">5mg</option>
                        <option value="10mg">10mg</option>
                        <option value="20mg">20mg</option>
                        <option value="50mg">50mg</option>
                        <option value="100mg">100mg</option>
                        <option value="250mg">250mg</option>
                        <option value="500mg">500mg</option>
                        <option value="1g">1g</option>
                    </select>
                </div>

                <div class="campo">
                    <label>Via de administração</label>
                    <select id="viaMedicamento" class="input-consulta">

                        <option value="">Selecione</option>
                        <option value="Oral">Oral</option>
                        <option value="Intravenosa">Intravenosa</option>
                        <option value="Intramuscular">Intramuscular</option>
                        <option value="Subcutânea">Subcutânea</option>
                        <option value="Tópica">Tópica</option>
                        <option value="Inalatória">Inalatória</option>

                    </select>

                </div>

                <div class="campo">
                    <label>Finalidade</label>
                    <input type="text" id="finalidadeMedicamento" placeholder="Ex: Dor e febre" class="input-consulta">
                </div>

                <div class="campo">
                    <label>Horário</label>
                    <input type="time" id="horarioMedicamento" class="input-consulta">
                </div>

                <div class="campo">
                    <label>Frequência</label>
                    <select id="frequenciaMedicamento" class="input-consulta">
                        <option value="">Selecione</option>
                        <option value="1x ao dia">
                            1x ao dia
                        </option>

                        <option value="2x ao dia">
                            2x ao dia
                        </option>

                        <option value="3x ao dia">
                            3x ao dia
                        </option>

                        <option value="4x ao dia">
                            4x ao dia
                        </option>

                        <option value="6 em 6 horas">
                            6 em 6 horas
                        </option>

                        <option value="8 em 8 horas">
                            8 em 8 horas
                        </option>

                        <option value="12 em 12 horas">
                            12 em 12 horas
                        </option>

                    </select>

                </div>

            </div>

            <div class="campo-full">

                <label>Observações</label>

                <textarea id="observacaoMedicamento" placeholder="Observações médicas..."
                    class="input-consulta textarea-med"></textarea>

            </div>

            <div class="checkbox-area">

                <label class="checkbox-custom">

                    <input type="checkbox" id="usoContinuoMedicamento">

                    <span>
                        Uso contínuo
                    </span>

                </label>

            </div>

            <button class="btn-salvar-medicamento" onclick="salvarMedicamento()">
                Salvar Medicamento
            </button>

        </div>

    </div>



    <script>

        let abaAtual = null;

        const modalBusca =
            document.getElementById("modalBuscaPaciente");

        const modalProntuario =
            document.getElementById("modalProntuario");

        const btnAbrirConsulta =
            document.getElementById("abrirConsulta");

        const btnFecharBusca =
            document.getElementById("fecharBusca");

        const btnBuscarPaciente =
            document.getElementById("buscarPacienteBtn");





        // ABRIR MODAL BUSCA

        btnAbrirConsulta.addEventListener("click", () => {

            modalBusca.style.display = "flex";

        });



        // FECHAR MODAL BUSCA

        btnFecharBusca.addEventListener("click", () => {

            modalBusca.style.display = "none";

        });



        // BUSCAR PACIENTE

        btnBuscarPaciente.addEventListener(
            "click",
            buscarPaciente
        );



        async function buscarPaciente() {

            const codigo =
                document.getElementById("codigoPaciente")
                    .value
                    .trim();

            const resultadoBusca =
                document.getElementById("resultadoBusca");



            if (codigo === "") {

                resultadoBusca.innerHTML = `
                <p style="color:red;">
                    Digite o código do paciente.
                </p>
            `;
                return;
            }

            resultadoBusca.innerHTML = `
            <p style="color:#ccc;">
                Buscando paciente...
            </p>
        `;

            try {

                const response = await fetch(
                    "php/profissional/buscarPaciente.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/x-www-form-urlencoded"
                        },

                        body:
                            "codigo=" +
                            encodeURIComponent(codigo)
                    }
                );



                const data = await response.json();

                console.log(data);



                if (data.status) {

                    resultadoBusca.innerHTML = `
                    <p style="color:#4caf50;">
                        Paciente encontrado.
                    </p>
                `;

                    abrirProntuario(data.usuario);

                }

                else {

                    resultadoBusca.innerHTML = `
                    <p style="color:red;">
                        ${data.mensagem}
                    </p>
                `;

                }

            }

            catch (error) {

                console.error(error);

                resultadoBusca.innerHTML = `
                <p style="color:red;">
                    Erro ao buscar paciente.
                </p>
            `;

            }

        }



        // ABRIR PRONTUÁRIO

        function abrirProntuario(usuario) {


            console.log(usuario);
            window.idPacienteAtual = usuario.idUsuario;
            modalProntuario.style.display = "flex";

            // FOTO

            let fotoPaciente = "Img/defaultUser.png";

            if (
                usuario.foto &&
                usuario.foto !== ""
            ) {

                if (
                    usuario.foto.includes("Img/")
                ) {

                    fotoPaciente =
                        usuario.foto;

                }

                else {

                    fotoPaciente =
                        "uploads/" +
                        usuario.foto;

                }

            }



            // PREENCHER DADOS

            document
                .getElementById("fotoPaciente")
                .src = fotoPaciente;

            document
                .getElementById("nomePaciente")
                .innerText =
                usuario.nomeUsuario;

            document
                .getElementById("codigoPacienteTexto")
                .innerText =
                usuario.codigoVinculo;

            document
                .getElementById("tipoSanguineoTexto")
                .innerText =
                usuario.tipoSanguineo ||
                "Não informado";

            document
                .getElementById("alergiasPaciente")
                .innerText =
                usuario.alergias ||
                "Nenhuma";

            document
                .getElementById("doencasPaciente")
                .innerText =
                usuario.doencasCronicas ||
                "Nenhuma";



            // BOTÕES

            document
                .getElementById("btnHistorico")
                .onclick = () => abrirHistorico(usuario.idUsuario);

            document
                .getElementById("btnGraficos")
                .onclick = abrirGraficos;

            document
                .getElementById("btnMedicamento")
                .onclick = abrirMedicamento;

        }

        /* =========================
   FECHAR PRONTUÁRIO
========================= */

        document
            .getElementById("fecharProntuarioBtn")
            .addEventListener("click", fecharProntuario);


        function fecharProntuario() {

            const confirmar = confirm(
                "Tem certeza que deseja fechar o prontuário?\n\nA consulta atual será cancelada."
            );

            if (!confirmar) return;

            // FECHAR MODAL

            modalProntuario.style.display = "none";

            // LIMPAR ÁREA DINÂMICA

            document.getElementById(
                "areaDinamicaConsulta"
            ).innerHTML = "";

            // LIMPAR OBSERVAÇÕES

            document.getElementById(
                "observacoesConsulta"
            ).value = "";

        }


        // HISTÓRICO

        async function abrirHistorico(idUsuario) {

            const area =
                document.getElementById("areaDinamicaConsulta");

            if (abaAtual === "historico") {
                area.innerHTML = "";
                abaAtual = null;
                return;
            }

            abaAtual = "historico";

            try {

                const response = await fetch(
                    "php/profissional/buscarHistorico.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/x-www-form-urlencoded"
                        },

                        body:
                            "idUsuario=" +
                            encodeURIComponent(idUsuario)
                    }
                );

                const data = await response.json();

                console.log(data);

                if (!data.status || data.historico.length === 0) {

                    area.innerHTML = `
                <p style="color:#ff4d4d;">
                    Nenhum histórico encontrado.
                </p>
            `;

                    return;
                }

                let html = `

        <div class="box-dinamica">

            <h2>
    Histórico Completo do paciente: ${document.getElementById("nomePaciente").innerText}
</h2>

            <table class="tabela-historico">

                <thead>

                    <tr>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Observação</th>
                    </tr>

                </thead>

                <tbody>
        `;

                data.historico.forEach(item => {

                    html += `
                <tr>

                    <td>
                        ${item.tipo}
                    </td>

                    <td>
                        ${item.valor}
                    </td>

                    <td>
                        ${item.data}
                    </td>

                    <td>
                        ${item.observacao || '-'}
                    </td>

                </tr>
            `;
                });

                html += `
                </tbody>
            </table>

        </div>
        `;

                area.innerHTML = html;

            } catch (error) {

                console.error(error);

                area.innerHTML = `
            <p style="color:red;">
                Erro ao carregar histórico.
            </p>
        `;
            }
        }


        // GRÁFICOS

        let graficoAtual = null;

        async function abrirGraficos() {

            const area =
                document.getElementById("areaDinamicaConsulta");

            if (abaAtual === "graficos") {

                area.innerHTML = "";

                abaAtual = null;

                return;
            }

            abaAtual = "graficos";

            area.innerHTML = `

        <div class="box-dinamica">

            <div class="historico-topo">
                <h2>
                    📊 Gráficos do Paciente
                </h2>
            </div>

            <div class="controle-grafico">

                <label>
                    Indicador:
                </label>

                <select id="tipoGrafico">

                    <option value="batimentos">
                        Batimentos
                    </option>

                    <option value="glicemia">
                        Glicemia
                    </option>

                    <option value="pressao">
                        Pressão
                    </option>

                    <option value="temperatura">
                        Temperatura
                    </option>

                </select>

            </div>

            <canvas id="graficoPaciente"></canvas>

        </div>

    `;

            try {

                const response = await fetch(
                    "php/profissional/buscarGraficos.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/x-www-form-urlencoded"
                        },

                        body:
                            "idUsuario=" +
                            encodeURIComponent(window.idPacienteAtual)
                    }
                );

                const data = await response.json();

                console.log(data);

                if (!data.status) {

                    area.innerHTML = `
                <p style="color:red;">
                    Nenhum gráfico encontrado.
                </p>
            `;

                    return;
                }

                const dadosGrafico = data.dados;

                const select =
                    document.getElementById("tipoGrafico");

                function criarGrafico(tipo) {

                    const registros =
                        dadosGrafico[tipo];

                    if (!registros || registros.length === 0) {
                        return;
                    }

                    const ctx =
                        document
                            .getElementById("graficoPaciente")
                            .getContext("2d");

                    if (graficoAtual) {
                        graficoAtual.destroy();
                    }

                    const labels =
                        registros.map(r => r.data);

                    let datasets = [];

                    switch (tipo) {

                        case "batimentos":

                            datasets.push({
                                label: "Batimentos",
                                data: registros.map(r => r.valor),
                                borderWidth: 2,
                                tension: 0.3
                            });

                            break;

                        case "temperatura":

                            datasets.push({
                                label: "Temperatura",
                                data: registros.map(r => r.valor),
                                borderWidth: 2,
                                tension: 0.3
                            });

                            break;

                        case "glicemia":

                            datasets.push({
                                label: "Glicemia",
                                data: registros.map(r => r.valor),
                                borderWidth: 2,
                                tension: 0.3
                            });

                            break;

                        case "pressao":

                            datasets.push({
                                label: "Sistólica",
                                data: registros.map(r => r.sistolica),
                                borderWidth: 2,
                                tension: 0.3
                            });

                            datasets.push({
                                label: "Diastólica",
                                data: registros.map(r => r.diastolica),
                                borderWidth: 2,
                                tension: 0.3
                            });

                            break;
                    }

                    graficoAtual = new Chart(ctx, {

                        type: "line",

                        data: {
                            labels: labels,
                            datasets: datasets
                        },

                        options: {

                            responsive: true,

                            plugins: {

                                legend: {
                                    labels: {
                                        color: "#000"
                                    }
                                }

                            },

                            scales: {

                                x: {
                                    ticks: {
                                        color: "#000"
                                    }
                                },

                                y: {
                                    ticks: {
                                        color: "#000"
                                    }
                                }

                            }

                        }

                    });

                }

                criarGrafico("batimentos");

                select.addEventListener("change", () => {

                    criarGrafico(select.value);

                });

            }

            catch (error) {

                console.error(error);

                area.innerHTML = `
            <p style="color:red;">
                Erro ao carregar gráficos.
            </p>
        `;
            }
        }



        // MEDICAMENTO

        function abrirMedicamento() {

            const area =
                document.getElementById("areaDinamicaConsulta");

            if (abaAtual === "medicamento") {

                area.innerHTML = "";

                abaAtual = null;

                return;
            }

            abaAtual = "medicamento";

            const template =
                document.getElementById("templateMedicamento");

            area.innerHTML = template.innerHTML;

            document
                .getElementById("nomePacienteMedicamento")
                .innerText =
                document.getElementById("nomePaciente").innerText;

        }


        async function salvarMedicamento() {

            const dados = {

                idUsuario: window.idPacienteAtual,

                nomeMedicamento:
                    document.getElementById("nomeMedicamento").value,

                dosagem:
                    document.getElementById("dosagemMedicamento").value,

                viaAdministracao:
                    document.getElementById("viaMedicamento").value,

                finalidade:
                    document.getElementById("finalidadeMedicamento").value,

                horario:
                    document.getElementById("horarioMedicamento").value,

                frequencia:
                    document.getElementById("frequenciaMedicamento").value,

                observacao:
                    document.getElementById("observacaoMedicamento").value,

                usoContinuo:
                    document.getElementById("usoContinuoMedicamento").checked ? 1 : 0

            };

            try {

                const response = await fetch(
                    "php/profissional/salvarMedicamento.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type": "application/json"
                        },

                        body: JSON.stringify(dados)
                    }
                );

                const resultado = await response.json();

                if (resultado.status) {

                    alert("Medicamento salvo com sucesso!");

                } else {

                    alert(resultado.mensagem);
                }

            } catch (erro) {

                console.error(erro);

                alert("Erro ao salvar medicamento.");
            }
        }


        async function verMedicamentos() {

            const area =
                document.getElementById("areaDinamicaConsulta");

            if (abaAtual === "verMedicamentos") {

                area.innerHTML = "";

                abaAtual = null;

                return;
            }

            abaAtual = "verMedicamentos";

            area.innerHTML = `
        <p style="color:white;">
            Carregando medicamentos...
        </p>
    `;

            try {

                const response = await fetch(
                    "php/profissional/buscarMedicamentos.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/x-www-form-urlencoded"
                        },

                        body:
                            "idUsuario=" +
                            encodeURIComponent(window.idPacienteAtual)
                    }
                );

                const data = await response.json();

                if (!data.status) {

                    area.innerHTML = `
                <p style="color:red;">
                    Nenhum medicamento encontrado.
                </p>
            `;

                    return;
                }

                let html = `

        <div class="lista-medicamentos">

            <h2 class="titulo-medicamentos">
                Medicamentos Prescritos
            </h2>

        `;


                data.medicamentos.forEach(med => {

                    html += `

            <div class="card-medicamento">

                <div class="topo-card-med">

                    <h3>
                        ${med.nomeMedicamento}
                    </h3>

                    <span class="badge-med">
                        ${med.dosagem}
                    </span>

                </div>

                <div class="info-med">

                    <p>
                        <strong>Via:</strong>
                        ${med.viaAdministracao}
                    </p>

                    <p>
                        <strong>Frequência:</strong>
                        ${med.frequencia}
                    </p>

                    <p>
                        <strong>Finalidade:</strong>
                        ${med.finalidade}
                    </p>

                    <p>
                        <strong>Horário:</strong>
                        ${med.horario}
                    </p>

                    <p>
                        <strong>Profissional:</strong>
                        ${med.nomeUsuario}
                    </p>

                    <p>
                        <strong>CRM:</strong>
                        ${med.crm}
                    </p>

                    <p>
                        <strong>Especialidade:</strong>
                        ${med.especialidade}
                    </p>

                    <p>
                        <strong>Data:</strong>
                        ${med.criadoEm}
                    </p>

                </div>

                <div class="acoes-med">

                    <button
                        class="btn-editar-med"
                        onclick="editarMedicamento(${med.idMedicamento})"
                    >
                    ✏️ Editar
                    </button>

                    <button
                        class="btn-excluir-med"
                        onclick="excluirMedicamento(${med.idMedicamento})"
                    >
                        🗑️ Excluir
                    </button>

                </div>

            </div>
            `;
                });

                html += `</div>`;

                area.innerHTML = html;

            } catch (erro) {

                console.error(erro);

                area.innerHTML = `
            <p style="color:red;">
                Erro ao carregar medicamentos.
            </p>
        `;

            }

        }


        async function excluirMedicamento(idMedicamento) {

            const confirmar = confirm(
                "Deseja realmente excluir este medicamento?"
            );

            if (!confirmar) return;

            try {

                const response = await fetch(

                    "php/profissional/excluirMedicamento.php",

                    {

                        method: "POST",

                        headers: {
                            "Content-Type": "application/json"
                        },

                        body: JSON.stringify({
                            idMedicamento
                        })

                    }

                );

                const data = await response.json();

                if (data.status) {

                    alert("Medicamento excluído.");

                    verMedicamentos();

                } else {

                    alert(data.mensagem);

                }

            } catch (error) {

                console.error(error);

                alert("Erro ao excluir medicamento.");

            }

        }

        async function editarMedicamento(idMedicamento) {

            try {

                const response = await fetch(
                    "php/profissional/buscarMedicamento.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type": "application/json"
                        },

                        body: JSON.stringify({
                            idMedicamento
                        })
                    }
                );

                const data = await response.json();

                if (!data.status) {

                    alert(data.mensagem);
                    return;

                }

                const med = data.medicamento;

                abrirMedicamento();

                setTimeout(() => {

                    document.getElementById("nomeMedicamento").value =
                        med.nomeMedicamento;

                    document.getElementById("dosagemMedicamento").value =
                        med.dosagem;

                    document.getElementById("viaMedicamento").value =
                        med.viaAdministracao;

                    document.getElementById("finalidadeMedicamento").value =
                        med.finalidade;

                    document.getElementById("horarioMedicamento").value =
                        med.horario;

                    document.getElementById("frequenciaMedicamento").value =
                        med.frequencia;

                    document.getElementById("observacaoMedicamento").value =
                        med.observacao;

                    document.getElementById("usoContinuoMedicamento").checked =
                        med.usoContinuo == 1;

                    document
                        .querySelector(".btn-salvar-medicamento")
                        .setAttribute(
                            "onclick",
                            `atualizarMedicamento(${idMedicamento})`
                        );

                }, 100);

            } catch (error) {

                console.error(error);

                alert("Erro ao carregar medicamento.");

            }

        }


        async function atualizarMedicamento(idMedicamento) {

            const dados = {

                idMedicamento,

                nomeMedicamento:
                    document.getElementById("nomeMedicamento").value,

                dosagem:
                    document.getElementById("dosagemMedicamento").value,

                viaAdministracao:
                    document.getElementById("viaMedicamento").value,

                finalidade:
                    document.getElementById("finalidadeMedicamento").value,

                horario:
                    document.getElementById("horarioMedicamento").value,

                frequencia:
                    document.getElementById("frequenciaMedicamento").value,

                observacao:
                    document.getElementById("observacaoMedicamento").value,

                usoContinuo:
                    document.getElementById("usoContinuoMedicamento").checked ? 1 : 0

            };

            try {

                const response = await fetch(
                    "php/profissional/atualizarMedicamento.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type": "application/json"
                        },

                        body: JSON.stringify(dados)
                    }
                );

                const data = await response.json();

                if (data.status) {

                    alert("Medicamento atualizado.");

                    verMedicamentos();

                } else {

                    alert(data.mensagem);

                }

            } catch (error) {

                console.error(error);

                alert("Erro ao atualizar.");

            }

        }

        let medicamentoParaExcluir = null;

        function excluirMedicamento(idMedicamento) {

            medicamentoParaExcluir = idMedicamento;

            document.getElementById(
                "modalExcluirMedicamento"
            ).style.display = "flex";
        }

        function fecharModalExcluir() {

            document.getElementById(
                "modalExcluirMedicamento"
            ).style.display = "none";

            medicamentoParaExcluir = null;
        }

        async function confirmarExcluirMedicamento() {

            if (!medicamentoParaExcluir) return;

            try {

                const response = await fetch(

                    "php/profissional/excluirMedicamento.php",

                    {

                        method: "POST",

                        headers: {
                            "Content-Type": "application/json"
                        },

                        body: JSON.stringify({
                            idMedicamento: medicamentoParaExcluir
                        })

                    }

                );

                const data = await response.json();

                if (data.status) {

                    fecharModalExcluir();

                    verMedicamentos();

                } else {

                    console.log(data.mensagem);

                }

            } catch (error) {

                console.error(error);

            }

        }








        function mostrarMensagem(texto, erro = false) {

            const toast =
                document.getElementById("toastMensagem");

            toast.innerText = texto;

            toast.className =
                erro
                    ? "toast erro"
                    : "toast sucesso";

            toast.style.display = "block";

            setTimeout(() => {

                toast.style.opacity = "1";

                toast.style.transform =
                    "translateY(0)";

            }, 10);

            setTimeout(() => {

                toast.style.opacity = "0";

                toast.style.transform =
                    "translateY(-10px)";

                setTimeout(() => {

                    toast.style.display = "none";

                }, 300);

            }, 3000);

        }

    </script>

    <!-- <button onclick="mostrarMensagem('Medicamento salvo com sucesso!')">
        TESTAR
    </button>

    <button onclick="mostrarMensagem('Erro ao excluir medicamento.', true)">
        TESTAR ERRO
    </button> -->

    <!-- MODAL EXCLUIR -->
    <div class="modal-excluir" id="modalExcluirMedicamento">

        <div class="modal-excluir-box">

            <h2>Excluir medicamento</h2>

            <p>
                Tem certeza que deseja excluir este medicamento?
            </p>

            <div class="acoes-excluir">

                <button class="btn-cancelar-excluir" onclick="fecharModalExcluir()">
                    Cancelar
                </button>

                <button class="btn-confirmar-excluir" onclick="confirmarExcluirMedicamento()">
                    Excluir
                </button>

            </div>

        </div>

    </div>
    <div id="toastMensagem"></div>




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