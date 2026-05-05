<?php
session_start();

$logado = isset($_SESSION['idLogin']);
$nome = $logado ? $_SESSION['nome'] : '';
$foto = $logado ? ($_SESSION['foto'] ?? 'Img/defaultUser.png') : 'Img/defaultUser.png';
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
                <li><a href="./login.html" data-lang="login">Login</a></li>

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
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <script src="script.js"></script>
    <script src="scriptTraducao.js"></script>
    <script src="scriptShowLogin.js"></script>




    <section class="Perfil">

        <h1>Meu Perfil</h1>

        <!-- FOTO -->
        <div class="fotoPerfil">
            <img src="<?= $foto ?>" id="previewFoto">

            <form action="php/usuario/uploadFoto.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="foto" accept="image/*" required>
                <button type="submit">Atualizar Foto</button>
            </form>
        </div>

        <!-- INFO -->
        <form action="php/usuario/updatePerfil.php" method="POST" class="infoPerfil">

            <div class="input-group">
                <label>Nome</label>
                <input type="text" name="nome" value="<?= $nome ?>" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= $usuario ?>" required>
            </div>

            <div class="input-group">
                <label>Nova Senha</label>
                <input type="password" name="novaSenha">
            </div>

            <button type="submit">Salvar Alterações</button>
        </form>

        <!-- ZONA PERIGOSA -->
        <div class="danger-zone">
            <h2>Excluir Conta</h2>
            <p>Essa ação é irreversível.</p>

            <form action="php/usuario/deleteConta.php" method="POST">
                <input type="text" name="confirmNome" placeholder="Digite seu nome para confirmar" required>
                <input type="password" name="senha" placeholder="Digite sua senha" required>

                <button type="submit" class="btn-danger">
                    Deletar Conta Permanentemente
                </button>
            </form>
        </div>

    </section>



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
</body>

</html>