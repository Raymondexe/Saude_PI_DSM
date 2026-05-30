<?php

require 'php/email/mailer.php';

if (
    enviarEmail(
        "pedrosgam3r@gmail.com",
        "Pedro",
        "Teste Bem-Estar 360",
        "<h1>Email funcionando 🚀</h1>"
    )
) {

    echo "Email enviado!";

} else {

    echo "Erro ao enviar.";

}

