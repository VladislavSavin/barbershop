<?php
require_once __DIR__ . '/../src/init.php';

// 1. Получаем ID услуги из URL
$serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

if ($serviceId === 0) {
    header('Location: /services/');
    exit;
}

// 2. Получаем информацию об услуге (только если она АКТИВНА)
$stmt = $db->prepare("SELECT * FROM services WHERE id = ? AND is_active = 1");
$stmt->execute([$serviceId]);
$service = $stmt->fetch();

if (!$service) {
    die("Услуга не найдена или временно недоступна. <a href='/services/'>Вернуться к услугам</a>");
}

// 3. Получаем мастеров, которые УМЕЮТ делать эту услугу
$stmtMasters = $db->prepare("
    SELECT m.* 
    FROM masters m
    JOIN master_skills ms ON m.id = ms.master_id
    WHERE ms.service_id = ? AND m.is_active = 1
");
$stmtMasters->execute([$serviceId]);
$availableMasters = $stmtMasters->fetchAll();

// 3.1 ИСКЛЮЧАЕМ САМОГО СЕБЯ ИЗ СПИСКА (Если зашел мастер)
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'master') {
    $stmtSelf = $db->prepare("SELECT id FROM masters WHERE user_id = ?");
    $stmtSelf->execute([$_SESSION['user_id']]);
    $myMasterId = $stmtSelf->fetchColumn();

    if ($myMasterId) {
        $availableMasters = array_filter($availableMasters, function($m) use ($myMasterId) {
            return $m['id'] != $myMasterId;
        });
        $availableMasters = array_values($availableMasters);
    }
}

// 4. Проверяем, авторизован ли пользователь, и получаем его данные + сертификаты
$currentUser = null;
$myCertificates = [];

if (isset($_SESSION['user_id'])) {
    $stmtUser = $db->prepare("SELECT name, phone FROM users WHERE id = ?");
    $stmtUser->execute([$_SESSION['user_id']]);
    $currentUser = $stmtUser->fetch();

    // Получаем активные сертификаты
    $stmtCert = $db->prepare("
        SELECT * FROM certificates 
        WHERE user_id = ? AND status = 'active' AND balance > 0
        ORDER BY balance ASC
    ");
    $stmtCert->execute([$_SESSION['user_id']]);
    $myCertificates = $stmtCert->fetchAll();
}

// 5. Настройка SEO и подключение шапки
$pageTitle = 'Запись на услугу — Barbershop';
require_once __DIR__ . '/../template/header.php';

// 6. Подключаем стили и компонент формы
echo '<link rel="stylesheet" href="/template/components/booking-form/style.css">';
require_once __DIR__ . '/../components/booking-form/component.php';

// 7. Выводим форму, передавая данные пользователя и сертификаты
echo renderBookingForm($service, $availableMasters, $currentUser, $myCertificates);

// 8. Подключаем подвал
require_once __DIR__ . '/../template/footer.php';