<?php
/**
 * Обработчик формы записи
 * Файл: actions/process_booking.php
 */
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/validators.php';

// 1. Проверяем, что данные пришли методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// 2. Получаем и очищаем данные
$serviceId = (int)$_POST['service_id'];
$masterId  = (int)$_POST['master_id'];
$date      = $_POST['date'] ?? '';
$time      = $_POST['time'] ?? '';
$area      = isset($_POST['area']) ? (float)$_POST['area'] : 1;

// Обработка телефона и имени (для авторизованных берется из БД, для гостей из формы)
$name  = isset($_POST['client_name']) ? trim($_POST['client_name']) : '';
$rawPhone = isset($_POST['client_phone']) ? trim($_POST['client_phone']) : '';

// 3. Базовая валидация
if (empty($serviceId) || empty($masterId) || empty($date) || empty($time)) {
    die("Ошибка: Пожалуйста, заполните все обязательные поля. <a href='/services/'>Вернуться</a>");
}

// Если гость — проверяем имя и телефон
if (!isset($_SESSION['user_id'])) {
    if (empty($name) || empty($rawPhone)) {
        die("Ошибка: Пожалуйста, укажите имя и телефон. <a href='/services/'>Вернуться</a>");
    }

    $normalizedPhone = normalizePhone($rawPhone);
    if (!$normalizedPhone) {
        die("Ошибка: Введите корректный номер телефона (например, +79001234567). <a href='/booking/?service_id=$serviceId'>Вернуться</a>");
    }
    $phone = $normalizedPhone;
}

// 4. Получаем информацию об услуге (только активную)
$stmt = $db->prepare("SELECT * FROM services WHERE id = ? AND is_active = 1");
$stmt->execute([$serviceId]);
$service = $stmt->fetch();

if (!$service) {
    die("Ошибка: Эта услуга временно недоступна для записи. <a href='/services/'>Выбрать другую</a>");
}

// 5. Считаем время окончания и итоговую стоимость
$startTimeObj = new DateTime("$date $time");
$endTimeObj = clone $startTimeObj;
$endTimeObj->modify("+{$service['duration']} minutes");

$startTimeSql = $startTimeObj->format('H:i:s');
$endTimeSql   = $endTimeObj->format('H:i:s');

$totalPrice = $service['type'] === 'area' ? ($service['price'] * $area) : $service['price'];

// 6. ОПРЕДЕЛЯЕМ ПОЛЬЗОВАТЕЛЯ (нужно для проверок ниже)
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $clientName = $_SESSION['user_name'];
    $stmtU = $db->prepare("SELECT phone FROM users WHERE id = ?");
    $stmtU->execute([$userId]);
    $clientPhone = $stmtU->fetchColumn();
} else {
    $userId = null;
    $clientName = $name;
    $clientPhone = $phone;
}

// 7. ПРОВЕРКА: МАСТЕР НЕ МОЖЕТ ЗАПИСАТЬСЯ САМ К СЕБЕ
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'master') {
    $stmtSelf = $db->prepare("SELECT id FROM masters WHERE user_id = ?");
    $stmtSelf->execute([$_SESSION['user_id']]);
    $myMasterId = $stmtSelf->fetchColumn();

    if ($myMasterId && (int)$masterId === (int)$myMasterId) {
        die("Ошибка: Вы не можете записаться к самому себе. Пожалуйста, выберите другого мастера. <a href='/booking/?service_id=$serviceId'>Вернуться</a>");
    }
}

// 8. ПРОВЕРКА: КЛИЕНТ НЕ МОЖЕТ БЫТЬ В ДВУХ МЕСТАХ ОДНОВРЕМЕННО
$stmtUserCheck = $db->prepare("
    SELECT b.*, s.name as service_name, m.name as master_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN masters m ON b.master_id = m.id
    WHERE (b.client_id = ? OR b.client_phone = ?) 
    AND b.booking_date = ? 
    AND b.status != 'cancelled'
    AND (b.start_time < ? AND b.end_time > ?)
    LIMIT 1
");
$stmtUserCheck->execute([$userId, $clientPhone, $date, $endTimeSql, $startTimeSql]);
$existingBooking = $stmtUserCheck->fetch();

if ($existingBooking) {
    $_SESSION['booking_conflict'] = [
        'master' => $existingBooking['master_name'],
        'service' => $existingBooking['service_name'],
        'date' => date('d.m.Y', strtotime($existingBooking['booking_date'])),
        'time' => substr($existingBooking['start_time'], 0, 5)
    ];
    header("Location: /booking/?service_id=$serviceId");
    exit;
}

// 9. ПРОВЕРКА: ЗАНЯТ ЛИ МАСТЕР В ЭТО ВРЕМЯ
$stmtCheck = $db->prepare("
    SELECT COUNT(*) FROM bookings 
    WHERE master_id = ? 
    AND booking_date = ? 
    AND status != 'cancelled'
    AND (start_time < ? AND end_time > ?)
");
$stmtCheck->execute([$masterId, $date, $endTimeSql, $startTimeSql]);

if ($stmtCheck->fetchColumn() > 0) {
    die("К сожалению, это время только что заняли. <a href='/booking/?service_id=$serviceId'>Выбрать другое время</a>");
}

// 10. ОПЛАТА СЕРТИФИКАТАМИ (Множественная, с возможностью частичной оплаты)
$certificateId = null;
$usedCertificates = [];
$useCertificate = isset($_POST['use_certificate']) && $_POST['use_certificate'] == 1;

if ($useCertificate && !empty($_POST['certificate_ids']) && is_array($_POST['certificate_ids'])) {
    if (!isset($_SESSION['user_id'])) {
        die("Ошибка: Для оплаты сертификатом нужно авторизоваться.");
    }

    $certIds = array_map('intval', $_POST['certificate_ids']);

    // Получаем сертификаты пользователя (сортируем от меньшего к большему, чтобы тратить мелкие первыми)
    $placeholders = implode(',', array_fill(0, count($certIds), '?'));
    $stmtCert = $db->prepare("
        SELECT * FROM certificates 
        WHERE id IN ($placeholders) AND user_id = ? AND status = 'active' AND balance > 0
        ORDER BY balance ASC
    ");
    $params = array_merge($certIds, [$_SESSION['user_id']]);
    $stmtCert->execute($params);
    $certs = $stmtCert->fetchAll();

    // Списываем по очереди
    $remainingToPay = $totalPrice;
    foreach ($certs as $c) {
        if ($remainingToPay <= 0) break;

        $deductAmount = min($c['balance'], $remainingToPay);
        $newBalance = $c['balance'] - $deductAmount;
        $newStatus = $newBalance <= 0 ? 'used' : 'active';

        $stmtUpdate = $db->prepare("
            UPDATE certificates 
            SET balance = ?, status = ?, used_at = ? 
            WHERE id = ?
        ");
        $stmtUpdate->execute([$newBalance, $newStatus, $newStatus === 'used' ? date('Y-m-d H:i:s') : null, $c['id']]);

        $usedCertificates[] = ['id' => $c['id'], 'amount' => $deductAmount];
        $remainingToPay -= $deductAmount;
    }

    // Если остались деньги к оплате (частичная оплата) — клиент доплатит в салоне
}

// 11. СОХРАНЯЕМ ЗАПИСЬ В БД
$stmtInsert = $db->prepare("
    INSERT INTO bookings (client_id, client_name, client_phone, master_id, service_id, certificate_id, booking_date, start_time, end_time, total_price, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");
$stmtInsert->execute([$userId, $clientName, $clientPhone, $masterId, $serviceId, $certificateId, $date, $startTimeSql, $endTimeSql, $totalPrice]);

$bookingId = $db->lastInsertId();

// 12. СОХРАНЯЕМ СВЯЗИ С СЕРТИФИКАТАМИ (если они использовались)
if (!empty($usedCertificates)) {
    $stmtLink = $db->prepare("
        INSERT INTO booking_certificates (booking_id, certificate_id, amount_used) 
        VALUES (?, ?, ?)
    ");
    foreach ($usedCertificates as $uc) {
        $stmtLink->execute([$bookingId, $uc['id'], $uc['amount']]);
    }
}

// 13. Перенаправляем на страницу успеха
header("Location: /success/?id=" . $bookingId);
exit;