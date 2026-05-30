<?php

function templateCriacaoConta($nome)
{
    return "
    <div style='font-family:Arial;padding:30px;background:#f5f7fb'>
        
        <div style='max-width:600px;margin:auto;background:#fff;
        border-radius:16px;padding:40px'>

            <h1 style='color:#1565c0'>
                Bem-vindo ao Bem-Estar 360 💙
            </h1>

            <p>
                Olá, <strong>$nome</strong>.
            </p>

            <p>
                Sua conta foi criada com sucesso.
            </p>

            <p>
                Agora você já pode acompanhar:
            </p>

            <ul>
                <li>Pressão arterial</li>
                <li>Batimentos cardíacos</li>
                <li>Temperatura</li>
                <li>Glicemia</li>
            </ul>

            <br>

            <p style='color:#777'>
                Bem-Estar 360 © 2025
            </p>

        </div>

    </div>
    ";
}