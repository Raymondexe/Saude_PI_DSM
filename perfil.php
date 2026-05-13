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

$tipoSanguineo = $usuario['tipoSanguineo'] ?? '';

$alergias = isset($usuario['alergias']) ? htmlspecialchars($usuario['alergias']) : '';
$doencasCronicas = isset($usuario['doencasCronicas']) ? htmlspecialchars($usuario['doencasCronicas']) : '';

$contatoEmergencia = htmlspecialchars($usuario['contatoEmergencia'] ?? '');
$telefoneEmergencia = htmlspecialchars($usuario['telefoneEmergencia'] ?? '');





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


if (isset($_GET['sucesso'])): ?>
    <script>
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
<?php endif;


if (isset($_GET['erro'])): ?>
    <script>
        const erro = "<?= $_GET['erro'] ?>";

        if (erro === "senhasDiferentes") {
            alert("As senhas não coincidem.");
        }

        if (erro === "preenchaAmbasSenhas") {
            alert("Preencha ambos os campos de senha.");
        }
    </script>
<?php endif;




$stmtConvites = $conn->prepare("
    SELECT 
        c.idConvite,
        c.validadeConvite,
        c.statusConvite,
        f.nomeFamilia,
        u.nomeUsuario
    FROM tblConvite c
    INNER JOIN tblUsuario u 
        ON u.idUsuario = c.Responsavel_idResponsavel
    LEFT JOIN tblFamiliaUsuario fu
        ON fu.Usuario_idUsuario = c.Usuario_idUsuario
    LEFT JOIN tblFamilia f
        ON f.idFamilia = fu.Familia_idFamilia
    WHERE c.Usuario_idUsuario = ?
    AND c.statusConvite = 'pendente'
");

$stmtConvites->bind_param("i", $id);
$stmtConvites->execute();

$resultConvites = $stmtConvites->get_result();
$totalConvites = $resultConvites->num_rows;

$stmtFamilias = $conn->prepare("
    SELECT DISTINCT
        f.idFamilia,
        f.nomeFamilia
    FROM tblFamilia f
    INNER JOIN tblFamiliaUsuario fu
        ON fu.Familia_idFamilia = f.idFamilia
    WHERE fu.Usuario_idUsuario = ?
");

$stmtFamilias->bind_param("i", $id);
$stmtFamilias->execute();

$resultFamilias = $stmtFamilias->get_result();
$temFamilias = $resultFamilias->num_rows > 0;
$totalFamilias = $resultFamilias->num_rows;
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
                    </div>
                </li>
            </ul>
        </nav>


    </header>

    <script src="script.js"></script>
    <script src="scriptTraducao.js"></script>
    <script src="scriptShowLogin.js"></script>

    <script>

        let tagParaExcluir = null;

        function adicionarTag(tipo) {
            const input = document.getElementById(
                tipo === "alergia" ? "inputAlergia" : "inputDoenca"
            );

            const hidden = document.getElementById(
                tipo === "alergia" ? "hiddenAlergias" : "hiddenDoencas"
            );

            const valor = input.value.trim();

            if (!valor) return;

            let lista = hidden.value
                ? hidden.value.split(",").map(item => item.trim()).filter(Boolean)
                : [];

            if (lista.includes(valor)) {
                alert("Item já existe.");
                return;
            }

            lista.push(valor);
            hidden.value = lista.join(",");

            input.value = "";

            renderizarTags(tipo);
        }

        function renderizarTags(tipo) {
            const hidden = document.getElementById(
                tipo === "alergia" ? "hiddenAlergias" : "hiddenDoencas"
            );

            const listaContainer = document.getElementById(
                tipo === "alergia" ? "listaAlergias" : "listaDoencas"
            );

            listaContainer.innerHTML = "";

            let lista = hidden.value
                ? hidden.value.split(",").map(item => item.trim()).filter(Boolean)
                : [];

            lista.forEach(item => {
                const tag = document.createElement("div");
                tag.className = "tag-item";

                tag.innerHTML = `
    <span class="tag-text">${item}</span>
    <button type="button" 
            class="tag-remove-btn"
            onclick="abrirModal('${tipo}', '${item}')">
        ✕
    </button>
`;

                listaContainer.appendChild(tag);
            });
        }

        function abrirModal(tipo, valor) {
            tagParaExcluir = { tipo, valor };
            document.getElementById("modalExcluir").style.display = "flex";
        }

        function fecharModal() {
            document.getElementById("modalExcluir").style.display = "none";
            tagParaExcluir = null;
        }

        function confirmarExclusao() {
            if (!tagParaExcluir) return;

            fetch("php/usuario/removerTag.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `tipo=${encodeURIComponent(tagParaExcluir.tipo)}&valor=${encodeURIComponent(tagParaExcluir.valor)}`
            })
                .then(response => response.text())
                .then(data => {
                    console.log("Resposta removerTag:", data);

                    if (data.trim() === "ok") {

                        const hidden = document.getElementById(
                            tagParaExcluir.tipo === "alergia"
                                ? "hiddenAlergias"
                                : "hiddenDoencas"
                        );

                        let lista = hidden.value
                            ? hidden.value.split(",").map(item => item.trim()).filter(Boolean)
                            : [];

                        lista = lista.filter(item => item !== tagParaExcluir.valor);

                        hidden.value = lista.join(",");

                        renderizarTags(tagParaExcluir.tipo);
                        fecharModal();

                    } else {
                        alert(data); // mostra erro real vindo do PHP
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Erro de conexão ao excluir.");
                });
        }

        window.addEventListener("load", function () {
            renderizarTags("alergia");
            renderizarTags("doenca");
        });


        window.addEventListener("DOMContentLoaded", function () {

            document.querySelectorAll('.menu a').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href');
                    const target = document.querySelector(targetId);

                    if (!target) return;

                    // abre o accordion alvo
                    if (target.tagName.toLowerCase() === 'details') {
                        target.open = true;
                    }

                    // abre accordions pais
                    let parent = target.parentElement;

                    while (parent) {
                        if (
                            parent.tagName &&
                            parent.tagName.toLowerCase() === 'details'
                        ) {
                            parent.open = true;
                        }
                        parent = parent.parentElement;
                    }

                    setTimeout(() => {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 150);
                });
            });

        });

        // Code Copia código
        function copiarCodigo() {
            const codigo = document.getElementById('codigo').innerText;

            navigator.clipboard.writeText(codigo);

            alert('Código copiado!');
        }


        // Validação código (Relacionamento)
        window.addEventListener("DOMContentLoaded", function () {

            const inputCodigo = document.getElementById("codigoDependente");
            const erro = document.getElementById("codigoErro");

            if (inputCodigo) {
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
            }

            renderizarTags("alergia");
            renderizarTags("doenca");
        });



        const campoTelefone = document.getElementById("telefone");
        const campoCpf = document.getElementById("cpf");

        /* TELEFONE */
        document.getElementById("telefone").addEventListener("input", function (e) {
            let v = e.target.value.replace(/\D/g, "");

            if (v.length > 11) v = v.slice(0, 11);

            if (v.length > 10) {
                v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, "($1) $2-$3");
            } else if (v.length > 6) {
                v = v.replace(/^(\d{2})(\d{4})(\d+)/, "($1) $2-$3");
            } else if (v.length > 2) {
                v = v.replace(/^(\d{2})(\d+)/, "($1) $2");
            }

            e.target.value = v;
        });




        // Verifica Senha
        const formPerfil = document.querySelector('form[action="php/usuario/updatePerfil.php"]');
        const novaSenha = document.getElementById("novaSenha");
        const confirmarSenha = document.getElementById("confirmarSenha");
        const erroSenha = document.getElementById("erroSenha");

        formPerfil.addEventListener("submit", function (e) {
            erroSenha.textContent = "";

            const senha = novaSenha.value.trim();
            const confirmacao = confirmarSenha.value.trim();

            // se começou preencher senha, precisa confirmar
            if (senha !== "" || confirmacao !== "") {

                if (senha === "" || confirmacao === "") {
                    e.preventDefault();
                    erroSenha.textContent = "Preencha os dois campos para alterar a senha.";
                    return;
                }

                if (senha !== confirmacao) {
                    e.preventDefault();
                    erroSenha.textContent = "As senhas não coincidem.";
                    return;
                }
            }
        });


        function showToast(msg) {
            const toast = document.getElementById("toast");

            toast.textContent = msg;
            toast.style.display = "block";

            setTimeout(() => {
                toast.style.display = "none";
            }, 2500);
        }
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



                                <!-- ARRUMAR MASCARA DO TELEFONE E CPF -->

                                <div class="field">
                                    <label>Telefone</label>
                                    <input type="text" id="telefone" name="telefone" value="<?= $telefone ?>"
                                        maxlength="15">
                                </div>

                                <div class="field">
                                    <label>CPF</label>
                                    <input type="text" id="cpf" name="cpf" value="<?= $cpf ?>" maxlength="14">
                                </div>
                            </div>
                        </details>

                        <details id="dados-medicos" class="accordion-item">
                            <summary>Dados Médicos</summary>

                            <div class="info-box">
                                <div class="info-icon">i</div>

                                <div class="info-text">
                                    <strong>Como adicionar informações médicas</strong>
                                    <p>
                                        Digite uma alergia ou doença, clique em <b>Adicionar</b> para incluir
                                        na lista e, ao finalizar todas as alterações, clique em
                                        <b>Salvar Alterações</b>. É possível excluir qualquer alteração antes e depois
                                        de clicar em Salvar alterações.
                                    </p>
                                </div>
                            </div>

                            <div class="grid">

                                <!-- Tipo sanguíneo -->
                                <div class="field">
                                    <label>Tipo sanguíneo</label>
                                    <select name="tipoSanguineo">
                                        <option value="">Selecione</option>
                                        <?php
                                        $tipos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                        foreach ($tipos as $tipo) {
                                            $selected = ($tipoSanguineo == $tipo) ? 'selected' : '';
                                            echo "<option value='$tipo' $selected>$tipo</option>";
                                        }
                                        ?>
                                    </select>
                                </div>


                                <!-- ALERGIAS -->
                                <div class="field full-width">
                                    <label>Alergias</label>

                                    <div class="input-group-tag">
                                        <input type="text" id="inputAlergia" placeholder="Digite alergia">
                                        <button type="button" onclick="adicionarTag('alergia')">
                                            Adicionar
                                        </button>
                                    </div>

                                    <div id="listaAlergias" class="tags-list"></div>

                                    <input type="hidden" name="alergias" id="hiddenAlergias"
                                        value="<?= isset($alergias) ? $alergias : '' ?>">
                                </div>

                                <!-- DOENÇAS -->
                                <div class="field full-width">
                                    <label>Doenças Crônicas</label>

                                    <div class="input-group-tag">
                                        <input type="text" id="inputDoenca" placeholder="Digite doença">
                                        <button type="button" onclick="adicionarTag('doenca')">
                                            Adicionar
                                        </button>
                                    </div>

                                    <div id="listaDoencas" class="tags-list"></div>

                                    <input type="hidden" name="doencasCronicas" id="hiddenDoencas"
                                        value="<?= isset($doencasCronicas) ? $doencasCronicas : '' ?>">
                                </div>
                            </div>
                        </details>


                        <div id="modalExcluir" class="modal-excluir">
                            <div class="modal-content-excluir">
                                <h3>Confirmar exclusão</h3>
                                <p>Deseja realmente excluir esta informação?</p>

                                <div class="modal-buttons">
                                    <button type="button" onclick="confirmarExclusao()">Confirmar</button>
                                    <button type="button" onclick="fecharModal()">Cancelar</button>
                                </div>
                            </div>
                        </div>









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




                        <div class="security-sections">

                            <!-- CONTA -->


                            <details id="seguranca" class="accordion-item">
                                <summary>Segurança e Conta</summary>

                                <div class="security-wrapper">

                                    <!-- CONTA -->
                                    <details class="sub-accordion" open>
                                        <summary>Conta</summary>

                                        <div class="sub-content">

                                            <div class="info-box">
                                                <div class="info-icon">i</div>

                                                <div class="info-text">
                                                    <strong>Alteração de senha</strong>
                                                    <p>
                                                        Para redefinir sua senha, preencha os campos abaixo
                                                        e confirme a nova senha antes de salvar.
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="password-container">
                                                <div class="field password-field">
                                                    <label>Nova senha</label>
                                                    <input type="password" id="novaSenha" name="novaSenha">
                                                </div>

                                                <div class="field password-field">
                                                    <label>Confirmar nova senha</label>
                                                    <input type="password" id="confirmarSenha" name="confirmarSenha">
                                                </div>
                                            </div>

                                            <p id="erroSenha" class="erro-senha"></p>

                                            <button type="submit" class="change-password-btn">
                                                Salvar alteração de senha
                                            </button>

                                            <small class="password-hint">
                                                A senha só será alterada caso os dois campos estejam preenchidos
                                                corretamente.
                                            </small>

                                        </div>
                                    </details>

                                    <!-- SEGURANÇA -->
                                    <details class="sub-accordion danger-accordion">
                                        <summary>Segurança</summary>

                                        <div class="sub-content">

                                            <div class="alert-box">
                                                <div class="alert-icon">!</div>

                                                <div class="alert-text">
                                                    <strong>Zona de risco</strong>
                                                    <p>
                                                        Esta área contém ações críticas e irreversíveis.
                                                        Dados removidos não poderão ser recuperados.
                                                    </p>
                                                </div>
                                            </div>

                                            <button type="button" class="danger-btn" onclick="abrirModalDelete()">
                                                Excluir Conta
                                            </button>

                                        </div>
                                    </details>
                                </div>
                            </details>

                            <button class="save-btn" type="submit">
                                Salvar Alterações
                            </button>





                        </div>


            </details>


            </details>


            <!-- BLOCO RELACIONAMENTO -->
            <details id="relacionamento" class="main-accordion">
                <summary>Relacionamento</summary>


                <?php if (!$temFamilias): ?>

                    <div id="estadoInicial" class="empty-family-box">
                        <h3>Crie seu relacionamento familiar</h3>
                        <p>Monte sua família e convide membros.</p>

                        <button type="button" onclick="abrirModalNovaFamilia()">
                            + Criar família
                        </button>
                    </div>

                <?php else: ?>

                    <div class="card">

                        <details id="familias" class="accordion-item" open>
                            <summary>Minhas Famílias</summary>

                            <?php while ($familia = $resultFamilias->fetch_assoc()): ?>

                                <?php
                                $idFamilia = $familia['idFamilia'];

                                $stmtMembros = $conn->prepare("
                SELECT
                    u.nomeUsuario,
                    u.foto,
                    fu.papel,
                    fu.statusMembro
                FROM tblFamiliaUsuario fu
                INNER JOIN tblUsuario u
                    ON u.idUsuario = fu.Usuario_idUsuario
                WHERE fu.Familia_idFamilia = ?
            ");

                                $stmtMembros->bind_param("i", $idFamilia);
                                $stmtMembros->execute();
                                $membros = $stmtMembros->get_result();
                                ?>

                                <div class="familia-card">

                                    <details class="familia-accordion">
                                        <summary class="familia-header">

                                            <h3><?= htmlspecialchars($familia['nomeFamilia']) ?></h3>

                                            <div class="familia-actions" onclick="event.stopPropagation()">
                                                <button type="button" onclick="abrirModalConfigFamilia(<?= $idFamilia ?>,
                                                    '<?= htmlspecialchars($familia['nomeFamilia'], ENT_QUOTES) ?>'
                                                         )">
                                                    ⚙️
                                                </button>
                                            </div>

                                        </summary>

                                        <div class="familia-content">

                                            <!-- RESPONSÁVEIS -->
                                            <div class="membros-section">
                                                <h4>Responsáveis</h4>

                                                <div class="membros-grid">
                                                    <?php
                                                    mysqli_data_seek($membros, 0);
                                                    while ($membro = $membros->fetch_assoc()):
                                                        if ($membro['papel'] !== 'responsavel')
                                                            continue;

                                                        $fotoMembro = !empty($membro['foto']) && file_exists("uploads/" . $membro['foto'])
                                                            ? "uploads/" . $membro['foto']
                                                            : "Img/defaultUser.png";
                                                        ?>


                                                        <div
                                                            class="membro-card <?= $membro['statusMembro'] === 'pendente' ? 'pending' : '' ?>">
                                                            <img src="<?= $fotoMembro ?>">
                                                            <span><?= htmlspecialchars($membro['nomeUsuario']) ?></span>
                                                            <small><?= ucfirst($membro['papel']) ?></small>
                                                        </div>

                                                        <div class="add-dependente"
                                                            onclick="abrirModalAdicionarResponsavel(<?= $idFamilia ?>)">
                                                            + Adicionar responsável
                                                        </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            </div>

                                            <!-- DEPENDENTES -->
                                            <div class="membros-section">
                                                <h4>Dependentes</h4>

                                                <div class="membros-grid">
                                                    <?php
                                                    mysqli_data_seek($membros, 0);
                                                    while ($membro = $membros->fetch_assoc()):
                                                        if ($membro['papel'] !== 'dependente')
                                                            continue;

                                                        $fotoMembro = !empty($membro['foto']) && file_exists("uploads/" . $membro['foto'])
                                                            ? "uploads/" . $membro['foto']
                                                            : "Img/defaultUser.png";
                                                        ?>
                                                        <div
                                                            class="membro-card <?= $membro['statusMembro'] === 'pendente' ? 'pending' : '' ?>">
                                                            <img src="<?= $fotoMembro ?>">
                                                            <span><?= htmlspecialchars($membro['nomeUsuario']) ?></span>
                                                            <small><?= ucfirst($membro['papel']) ?></small>
                                                        </div>
                                                    <?php endwhile; ?>

                                                    <div class="add-dependente"
                                                        onclick="abrirModalAdicionarDependente(<?= $idFamilia ?>)">
                                                        + Adicionar dependente
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </details>
                                </div>

                            <?php endwhile; ?>

                            <?php if ($totalFamilias < 2): ?>
                                <div class="novo-relacionamento-card" onclick="abrirModalNovaFamilia()">
                                    +
                                </div>
                            <?php endif; ?>

                        </details>
                    </div>

                <?php endif; ?>


                <div id="modalNovaFamilia" class="modal-excluir">
                    <div class="modal-content-excluir">

                        <h3>Criar nova família</h3>

                        <p>
                            Escolha um nome para identificar esse relacionamento familiar.
                        </p>

                        <input type="text" id="nomeNovaFamilia" placeholder="Ex: Família Oliveira" maxlength="50">

                        <button type="button" onclick="criarFamilia()">
                            Criar família
                        </button>

                        <button type="button" onclick="fecharModalNovaFamilia()">
                            Cancelar
                        </button>

                    </div>
                </div>

                <div id="modalNovoRelacionamento" class="modal-excluir">
                    <div class="modal-content-excluir">

                        <h3>Novo relacionamento</h3>

                        <input type="text" id="nomeFamilia" placeholder="Nome da família">

                        <input type="text" id="codigoDependenteModal" placeholder="BSTR-XXXXXX">

                        <button onclick="enviarConviteRelacionamento()">
                            Enviar convite
                        </button>

                        <button type="button" onclick="fecharModalNovoRelacionamento()">
                            Cancelar
                        </button>
                    </div>
                </div>

                <div id="modalConfigFamilia" class="modal-excluir">
                    <div class="modal-config-familia">

                        <div class="modal-config-header">
                            <h3>Configurações da Família</h3>
                            <button type="button" onclick="fecharModalConfigFamilia()">✕</button>
                        </div>

                        <input type="hidden" id="idFamiliaConfig">

                        <div class="config-section">
                            <label>Nome da família</label>

                            <div class="edit-nome-box">
                                <input type="text" id="novoNomeFamilia" maxlength="50">
                                <button type="button" onclick="salvarNovoNomeFamilia()">
                                    Salvar
                                </button>
                            </div>
                        </div>

                        <div class="config-actions">
                            <button type="button" onclick="gerenciarMembros()">
                                👥 Gerenciar membros
                            </button>

                            <button class="btnPerigo" onclick="abrirModalExcluir()">
                                🗑 Excluir família
                            </button>


                            <!-- ARRUMAR MODAL DE CONFIRMARÇÃO DE EXCLUSÃO DE FAMILIA -->

                            <div id="modalConfirmarExclusao" class="modal-excluir">
                                <div class="modal-content-excluir">
                                    <h3>Excluir família</h3>
                                    <p>Tem certeza que deseja excluir esta família?</p>

                                    <div class="modal-buttons">
                                        <button onclick="excluirFamilia()">Sim, excluir</button>
                                        <button onclick="fecharModalExcluir()">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                <!-- <div id="toast" class="toast"></div>-->
            </details>

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

    <!-- <div class="card">
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
    </div> -->





    <br><br><br><br><br><br><br>





















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

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            /* ===== ALTERAÇÃO DE SENHA ===== */
            const formPerfil = document.querySelector('form[action="php/usuario/updatePerfil.php"]');
            const novaSenha = document.getElementById("novaSenha");
            const confirmarSenha = document.getElementById("confirmarSenha");
            const erroSenha = document.getElementById("erroSenha");

            formPerfil.addEventListener("submit", function (e) {
                erroSenha.textContent = "";

                const senha = novaSenha.value.trim();
                const confirmacao = confirmarSenha.value.trim();

                if (senha !== "" || confirmacao !== "") {

                    if (senha === "" || confirmacao === "") {
                        e.preventDefault();
                        erroSenha.textContent = "Preencha ambos os campos.";
                        return;
                    }

                    if (senha !== confirmacao) {
                        e.preventDefault();
                        erroSenha.textContent = "As senhas não coincidem.";
                        return;
                    }
                }
            });


            /* ===== MODAL DELETE ===== */
            const modalDelete = document.getElementById("modalDeleteConta");
            const inputDelete = document.getElementById("confirmacaoDelete");
            const btnDelete = document.getElementById("btnConfirmDelete");

            const textoConfirmacao = "EXCLUIR MINHA CONTA";

            window.abrirModalDelete = function () {
                modalDelete.style.display = "flex";
                inputDelete.value = "";
                btnDelete.disabled = true;
            }

            window.fecharModalDelete = function () {
                modalDelete.style.display = "none";
                inputDelete.value = "";
                btnDelete.disabled = true;
            }

            inputDelete.addEventListener("paste", function (e) {
                e.preventDefault();
            });

            inputDelete.addEventListener("input", function () {
                btnDelete.disabled = inputDelete.value.trim() !== textoConfirmacao;
            });

            window.excluirConta = function () {
                fetch("php/usuario/excluirConta.php", {
                    method: "POST"
                })
                    .then(response => response.text())
                    .then(data => {
                        if (data.trim() === "ok") {
                            alert("Conta excluída com sucesso.");
                            window.location.href = "login.html";
                        } else {
                            alert(data);
                        }
                    })
                    .catch(() => {
                        alert("Erro ao excluir conta.");
                    });
            }

        });




        // MODAL NOTIFICAÇÃO
        document.addEventListener("DOMContentLoaded", function () {

            const modalNotificacoes = document.getElementById("modalNotificacoes");
            const btnNotificacao = document.getElementById("btnNotificacao");

            if (!modalNotificacoes || !btnNotificacao) {
                console.log("Elemento não encontrado");
                return;
            }

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
        });



        function abrirModalNovoRelacionamento() {
            document.getElementById("modalNovoRelacionamento").style.display = "flex";
        }

        function fecharModalNovoRelacionamento() {
            document.getElementById("modalNovoRelacionamento").style.display = "none";
        }

        function enviarConviteRelacionamento() {

            showToast("Enviando convite...");

            const nomeFamilia =
                document.getElementById("nomeFamilia").value;

            const codigoDependente =
                document.getElementById("codigoDependenteModal").value;

            fetch("php/relacionamento/enviarConvite.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body:
                    "nomeFamilia=" + encodeURIComponent(nomeFamilia) +
                    "&codigoDependente=" + encodeURIComponent(codigoDependente)
            })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "ok") {
                        showToast("Convite enviado!");

                        setTimeout(() => {
                            location.reload();
                        }, 1200);
                    } else {
                        showToast(data);
                    }
                });
        }


        function abrirModalNovaFamilia() {
            document.getElementById("modalNovaFamilia").style.display = "flex";
            document.getElementById("nomeNovaFamilia").value = "";
        }

        function fecharModalNovaFamilia() {
            document.getElementById("modalNovaFamilia").style.display = "none";
        }

        function criarFamilia() {
            const nomeFamilia = document.getElementById("nomeNovaFamilia").value.trim();

            if (!nomeFamilia) {
                alert("Digite o nome da família.");
                return;
            }

            fetch("/Saude_PI_DSM-main/php/usuario/relacionamento/criarFamilia.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "nomeFamilia=" + encodeURIComponent(nomeFamilia)
            })
                .then(response => response.text())
                .then(data => {
                    console.log(data);

                    if (data.trim() === "ok") {
                        fecharModalNovaFamilia();
                        location.reload();
                    } else {
                        alert(data);
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert("Erro ao criar família.");
                });
        }


        function abrirModalConfigFamilia(idFamilia, nomeFamilia) {
            document.getElementById("modalConfigFamilia").style.display = "flex";
            document.getElementById("idFamiliaConfig").value = idFamilia;
            document.getElementById("novoNomeFamilia").value = nomeFamilia;
        }

        function fecharModalConfigFamilia() {
            document.getElementById("modalConfigFamilia").style.display = "none";
        }

        function salvarNovoNomeFamilia() {
            const idFamilia = document.getElementById("idFamiliaConfig").value;
            const novoNome = document.getElementById("novoNomeFamilia").value.trim();

            if (!novoNome) {
                alert("Digite um nome válido.");
                return;
            }

            fetch("/Saude_PI_DSM-main/php/usuario/relacionamento/editarFamilia.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body:
                    "idFamilia=" + encodeURIComponent(idFamilia) +
                    "&novoNome=" + encodeURIComponent(novoNome)
            })
                .then(res => res.text())
                .then(data => {
                    if (data.trim() === "ok") {
                        location.reload();
                    } else {
                        alert(data);
                    }
                });
        }

        function excluirFamilia() {
            const idFamilia = document.getElementById("idFamiliaConfig").value;

            fetch("/Saude_PI_DSM-main/php/usuario/relacionamento/excluirFamilia.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "idFamilia=" + encodeURIComponent(idFamilia)
            })
                .then(res => res.text())
                .then(data => {
                    if (data.trim() === "ok") {
                        location.reload();
                    } else {
                        mostrarToast(data || "Erro ao excluir família");
                    }
                });
        }


        function abrirModalExcluir() {
            document.getElementById("modalConfirmarExclusao").style.display = "flex";
        }

        function fecharModalExcluir() {
            document.getElementById("modalConfirmarExclusao").style.display = "none";
        }

    </script>


    <div id="modalDeleteConta" class="modal-excluir">
        <div class="modal-content-excluir">

            <h3>Excluir conta</h3>

            <p>Esta ação é irreversível. Para confirmar, digite:</p>

            <strong id="textoConfirmacao">EXCLUIR MINHA CONTA</strong>

            <input type="text" id="confirmacaoDelete" placeholder="Digite exatamente o texto acima" autocomplete="off"
                spellcheck="false" autocorrect="off" autocapitalize="off">

            <button id="btnConfirmDelete" disabled onclick="excluirConta()">
                Excluir Conta Permanentemente
            </button>

            <button type="button" onclick="fecharModalDelete()">
                Cancelar
            </button>

        </div>
    </div>


    <div id="modalNotificacoes" class="modal-notificacoes">

        <div class="modal-box-notificacoes">

            <div class="modal-header">
                <h2>Notificações</h2>
                <button onclick="fecharNotificacoes()">✕</button>
            </div>

            <!-- CONVITES -->
            <details class="notificacao-accordion" open>
                <summary>
                    Convites
                    <span class="badge contador-itens">
                        <?= $totalConvites ?>
                    </span>
                </summary>

                <div class="notificacao-lista">

                    <?php if ($totalConvites > 0): ?>
                        <?php while ($convite = $resultConvites->fetch_assoc()): ?>

                            <div class="convite-card">

                                <div class="convite-info">
                                    <strong>
                                        <?= htmlspecialchars($convite['nomeUsuario']) ?>
                                    </strong>
                                    <p>Família
                                        <?= htmlspecialchars($convite['nomeUsuario']) ?>
                                    </p>
                                    <small>
                                        Convite válido até:
                                        <?= date('d/m/Y', strtotime($convite['validadeConvite'])) ?>
                                    </small>
                                </div>

                                <div class="convite-acoes">

                                    <form action="php/relacionamento/aceitarConvite.php" method="POST">
                                        <input type="hidden" name="idConvite" value="<?= $convite['idConvite'] ?>">
                                        <button type="submit" class="btn-aceitar">
                                            Aceitar
                                        </button>
                                    </form>

                                    <form action="php/relacionamento/recusarConvite.php" method="POST">
                                        <input type="hidden" name="idConvite" value="<?= $convite['idConvite'] ?>">
                                        <button type="submit" class="btn-recusar">
                                            Recusar
                                        </button>
                                    </form>

                                </div>
                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>
                        <div class="empty-box">
                            Nenhum convite pendente.
                        </div>
                    <?php endif; ?>

                </div>
            </details>

            <!-- SAÚDE -->
            <details class="notificacao-accordion">
                <summary>
                    Saúde
                    <span class="badge">0</span>
                </summary>

                <div class="empty-box">
                    Nenhuma notificação de saúde.
                </div>
            </details>

            <!-- OUTROS -->
            <details class="notificacao-accordion">
                <summary>
                    Outros
                    <span class="badge">0</span>
                </summary>

                <div class="empty-box">
                    Nenhuma outra notificação.
                </div>
            </details>

        </div>
    </div>
</body>

</html>