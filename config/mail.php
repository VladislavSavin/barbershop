<?php
/**
 * Настройки почты
 * Файл: config/mail.php
 */

return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'your_email@gmail.com',
    'password' => 'YOUR_APP_PASSWORD', // Без пробелов
    'encryption' => 'tls',
    'from_email' => 'your_email@gmail.com',
    'from_name' => 'Barbershop & Tattoo',
];