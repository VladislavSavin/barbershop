<?php
/**
 * Контроллер вкладки "Записи"
 * Файл: admin/views/bookings.php
 */

$stmt = $db->query("
    SELECT b.*, s.name as service_name, 
           m.name as master_name, m.avatar as master_avatar,
           COALESCE(u.name, b.client_name) as final_client_name,
           COALESCE(u.phone, b.client_phone) as final_client_phone,
           COALESCE(SUM(bc.amount_used), 0) as cert_paid
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN masters m ON b.master_id = m.id
    LEFT JOIN users u ON b.client_id = u.id
    LEFT JOIN booking_certificates bc ON b.id = bc.booking_id
    GROUP BY b.id
    ORDER BY b.booking_date DESC, b.start_time DESC
");
$bookings = $stmt->fetchAll();

// Подключаем функцию отрисовки и выводим её
require_once __DIR__ . '/../php/render_bookings.php';
echo renderAdminBookings($bookings);

// ВОЗВРАЩАЕМ СКРИПТ:
echo '<script src="/admin/js/bookings.js"></script>';
?>