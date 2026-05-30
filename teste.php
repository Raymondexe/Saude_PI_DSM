<?php

require "php/email/mailer.php";

$enviado = enviarEmail(
    "SEUEMAIL@gmail.com",
    "Pedro",
    "Teste Bem-Estar 360",
    "<h1>Email funcionando 🚀</h1>"
);

if ($enviado) {
    echo "Email enviado com sucesso!";
} else {
    echo "Erro ao enviar.";
}