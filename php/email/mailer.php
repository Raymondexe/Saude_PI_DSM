<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

function enviarEmail($destinatario, $nome, $assunto, $mensagemHtml)
{
    $mail = new PHPMailer(true);

    try {

        /*
        SMTP
        */
        $mail->isSMTP();

        $mail->Host       = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth   = true;

        $mail->Username   = $_ENV['EMAIL_USER'];
        $mail->Password   = $_ENV['EMAIL_PASS'];

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) $_ENV['EMAIL_PORT'];

        /*
        IMPORTANTE
        */
        $mail->CharSet = 'UTF-8';

        /*
        REMETENTE
        */
        $mail->setFrom(
            $_ENV['EMAIL_FROM'],
            $_ENV['EMAIL_NAME']
        );

        /*
        DESTINATÁRIO
        */
        $mail->addAddress($destinatario, $nome);

        /*
        EMAIL
        */
        $mail->isHTML(true);

        $mail->Subject = $assunto;
        $mail->Body    = $mensagemHtml;

        /*
        ENVIA
        */
        return $mail->send();

    } catch (Exception $e) {

        echo "Erro ao enviar email: {$mail->ErrorInfo}";
        return false;

    }
}