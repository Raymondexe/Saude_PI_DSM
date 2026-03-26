<?php
session_start();

$logado = isset($_SESSION['idLogin']);
$nome = $logado ? $_SESSION['nome'] : '';
$foto = $logado ? ($_SESSION['foto'] ?? 'Img/defaultUser.png') : 'Img/defaultUser.png';
?>

<nav class="Navegacao">
    <ul>
        <li><a href="./index.php" data-lang="home">Home</a></li>
        <li><a href="./monitoramento.php" data-lang="monitoring">Monitoramento</a></li>

        <li><a href="./calendario.php">Agenda</a></li>
        <li><a href="./servicos.php" data-lang="services">Serviços</a></li>
        <li><a href="./quemSomos.php" data-lang="about">Quem somos</a></li>

        <?php if ($logado): ?>
            <!-- Perfil do usuário -->
            <li class="perfil-menu">
                <button id="perfil-btn">
                    <img src="<?= $foto ?>" alt="Foto de perfil" class="foto-perfil">
                    <span class="nome-perfil"><?= $nome ?></span>
                </button>

                <div class="dropdown-perfil">
                    <a href="./perfil.php">Meu Perfil</a>
                    <a href="./php/usuario/logout.php">Sair</a>
                </div>
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