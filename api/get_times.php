<?php
require_once __DIR__ . '/../src/init.php';
date_default_timezone_set('Europe/Moscow');
header('Content-Type: application/json; charset=utf-8');

$masterId = isset($_GET['master_id']) ? (int)$_GET['master_id'] : 0;
$date = $_GET['date'] ?? '';
$serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

if (!$masterId || !$date || !$serviceId) {
    echo json_encode(['error' => 'Недостаточно данных']);
    exit;
}

// 1. Длительность услуги
$stmt = $db->prepare("SELECT duration FROM services WHERE id = ?");
$stmt->execute([$serviceId]);
$service = $stmt->fetch();
$duration = $service ? (int)$service['duration'] : 0;

if ($duration === 0) {
    echo json_encode(['error' => 'Услуга не найдена']);
    exit;
}

// 2. Получаем все записи ВЫБРАННОГО МАСТЕРА на эту дату
$stmtBookings = $db->prepare("
    SELECT id, start_time, end_time, status 
    FROM bookings 
    WHERE master_id = ? AND booking_date = ?
");
$stmtBookings->execute([$masterId, $date]);
$masterBookings = $stmtBookings->fetchAll();

// 2.1 Получаем записи САМОГО КЛИЕНТА на эту дату (если он авторизован)
$userBookings = [];
if (isset($_SESSION['user_id'])) {
    $stmtUserBookings = $db->prepare("
        SELECT start_time, end_time 
        FROM bookings 
        WHERE client_id = ? AND booking_date = ? AND status != 'cancelled'
    ");
    $stmtUserBookings->execute([$_SESSION['user_id'], $date]);
    $userBookings = $stmtUserBookings->fetchAll();
}

$timezone = new DateTimeZone('Europe/Moscow');

$startTime = new DateTime("$date 10:00:00", $timezone);
$endTime = new DateTime("$date 20:00:00", $timezone);

$now = new DateTime('now', $timezone);
$todayDate = $now->format('Y-m-d');
$isToday = ($date === $todayDate);

$availableSlots = [];
$blockedSlots = [];
$currentSlot = clone $startTime;

while ($currentSlot < $endTime) {
    $slotStart = clone $currentSlot;
    $slotEnd = clone $currentSlot;
    $slotEnd->modify("+{$duration} minutes");

    if ($slotEnd > $endTime) {
        $currentSlot->modify("+30 minutes");
        continue;
    }

    if ($isToday) {
        $diffInMinutes = ($slotStart->getTimestamp() - $now->getTimestamp()) / 60;
        if ($diffInMinutes < 20) {
            $currentSlot->modify("+30 minutes");
            continue;
        }
    }

    $isAvailable = true;
    $blockedReason = null;

    // ПРОВЕРКА 1: Занят ли мастер?
    foreach ($masterBookings as $booking) {
        if ($booking['status'] === 'cancelled') continue;

        $bookStart = new DateTime("$date " . $booking['start_time'], $timezone);
        $bookEnd = new DateTime("$date " . $booking['end_time'], $timezone);

        if ($slotStart < $bookEnd && $slotEnd > $bookStart) {
            $isAvailable = false;
            $blockedReason = 'master';
            break;
        }
    }

    // ПРОВЕРКА 2: Занят ли сам пользователь у другого мастера?
    if ($isAvailable) {
        foreach ($userBookings as $ub) {
            $ubStart = new DateTime("$date " . $ub['start_time'], $timezone);
            $ubEnd = new DateTime("$date " . $ub['end_time'], $timezone);

            if ($slotStart < $ubEnd && $slotEnd > $ubStart) {
                $isAvailable = false;
                $blockedReason = 'user';
                break;
            }
        }
    }

    if ($isAvailable) {
        $availableSlots[] = $slotStart->format('H:i');
    } else {
        $blockedSlots[] = [
            'slot' => $slotStart->format('H:i'),
            'reason' => $blockedReason
        ];
    }

    $currentSlot->modify("+30 minutes");
}

// ОТЛАДОЧНЫЙ ВЫВОД
if (isset($_GET['debug'])) {
    echo json_encode([
        'requested_date' => $date,
        'is_today' => $isToday,
        'master_bookings' => $masterBookings,
        'user_bookings' => $userBookings,
        'available_slots' => $availableSlots,
        'blocked_slots' => $blockedSlots,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode($availableSlots);