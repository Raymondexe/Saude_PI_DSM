<?php
session_start();

// Limpa sessão
$_SESSION = [];
session_destroy();

// Redireciona corretamente
header("Location: /Saude_PI_DSM-main/index.php");
exit();