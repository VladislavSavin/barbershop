<?php
/**
 * Инициализация приложения
 * Файл: src/init.php
 *
 * Этот файл подключает базу данных и запускает сессию.
 * Его нужно подключать в самом начале каждой страницы.
 */

// 1. Запускаем сессию (если она еще не запущена)
date_default_timezone_set('Europe/Moscow');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Подключаем класс базы данных
require_once __DIR__ . '/Database.php';

// 3. Получаем подключение и сохраняем в переменную $db
$db = Database::getInstance()->getConnection();

$currentDateTime = date('Y-m-d H:i:s');
$stmtAutoComplete = $db->prepare("
    UPDATE bookings 
    SET status = 'completed' 
    WHERE status IN ('pending', 'confirmed') 
    AND CONCAT(booking_date, ' ', end_time) < ?
");
$stmtAutoComplete->execute([$currentDateTime]);