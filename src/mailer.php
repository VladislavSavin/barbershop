<?php
/**
 * Отправка email через PHPMailer
 * Файл: src/mailer.php
 */
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $htmlBody) {
    $config = require __DIR__ . '/../config/mail.php';

    $mail = new PHPMailer(true);

    try {
        // Настройки SMTP
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port       = $config['port'];
        $mail->CharSet    = 'UTF-8';

        // Отправитель и получатель
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);

        // Контент
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Если произошла ошибка, просто возвращаем false.
        // В будущем тут можно добавить запись в системный лог сервера (error_log).
        return false;
    }
}