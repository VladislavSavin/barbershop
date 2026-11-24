<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/auth_helpers.php';

// Защита: пускаем только мастеров
protectPage('master');

// Получаем ID мастера
$stmtMaster = $db->prepare("SELECT id, specialization FROM masters WHERE user_id = ?");
$stmtMaster->execute([$_SESSION['user_id']]);
$master = $stmtMaster->fetch();

if (!$master) {
    die("Ошибка: Ваш аккаунт не привязан к профилю мастера. Обратитесь к администратору.");
}
$masterId = $master['id'];

// Обработка смены статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $bId = (int)$_POST['booking_id'];
    $status = $_POST['status'];

    if (in_array($status, ['confirmed', 'completed', 'cancelled'])) {
        $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ? AND master_id = ?");
        $stmt->execute([$status, $bId, $masterId]);
    }
    header('Location: /master/');
    exit;
}

// ==========================================
// 1. ЗАПИСИ, ГДЕ ОН МАСТЕР
// ==========================================
$stmtBookings = $db->prepare("
    SELECT b.*, s.name as service_name, 
           COALESCE(u.name, b.client_name) as final_client_name,
           COALESCE(u.phone, b.client_phone) as final_client_phone
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    LEFT JOIN users u ON b.client_id = u.id
    WHERE b.master_id = ?
    ORDER BY b.booking_date ASC, b.start_time ASC
");
$stmtBookings->execute([$masterId]);
$allBookings = $stmtBookings->fetchAll();

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$grouped = ['today' => [], 'tomorrow' => [], 'future' => [], 'history' => []];

foreach ($allBookings as $b) {
    $bDate = $b['booking_date'];
    $isPastTime = strtotime($bDate . ' ' . $b['start_time']) < time();

    if ($b['status'] === 'cancelled' || $isPastTime || $b['status'] === 'completed') {
        $grouped['history'][] = $b;
    } elseif ($bDate === $today) {
        $grouped['today'][] = $b;
    } elseif ($bDate === $tomorrow) {
        $grouped['tomorrow'][] = $b;
    } else {
        $grouped['future'][] = $b;
    }
}

// ==========================================
// 2. ЗАПИСИ, ГДЕ ОН КЛИЕНТ
// ==========================================
$stmtMyVisits = $db->prepare("
    SELECT b.*, s.name as service_name, m.name as master_name 
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN masters m ON b.master_id = m.id
    WHERE b.client_id = ?
    ORDER BY b.booking_date DESC, b.start_time DESC
");
$stmtMyVisits->execute([$_SESSION['user_id']]);
$myVisits = $stmtMyVisits->fetchAll();

$myVisitsActive = [];
$myVisitsHistory = [];

foreach ($myVisits as $v) {
    $isPast = strtotime($v['booking_date'] . ' ' . $v['start_time']) < time();
    if ($isPast || $v['status'] === 'cancelled' || $v['status'] === 'completed') {
        $myVisitsHistory[] = $v;
    } else {
        $myVisitsActive[] = $v;
    }
}

// ==========================================
// 3. ПОДКЛЮЧАЕМ ФУНКЦИИ ОТРИСОВКИ
// ==========================================
require_once __DIR__ . '/php/render.php';

$pageTitle = 'Кабинет мастера — Barbershop';
require_once __DIR__ . '/../template/header.php';
echo '<link rel="stylesheet" href="/template/pages/master/style.css">';
?>

    <div class="container master-dashboard">
        <div class="master-header">
            <div>
                <h1>Привет, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
                <p><?= htmlspecialchars($master['specialization']) ?></p>
            </div>
            <a href="/actions/logout.php" class="btn-logout">Выйти</a>
        </div>

        <!-- ТАБЫ -->
        <div class="master-tabs">
            <button class="tab-btn active" data-target="tab-schedule">Мое расписание</button>
            <button class="tab-btn" data-target="tab-my-visits">Мои визиты (как клиент)</button>
        </div>

        <!-- ВКЛАДКА 1: РАСПИСАНИЕ -->
        <div class="tab-content active" id="tab-schedule">
            <div class="master-schedule">
                <div class="schedule-group">
                    <h2 class="schedule-title today">Сегодня <span>(<?= count($grouped['today']) ?>)</span></h2>
                    <?php if (empty($grouped['today'])): ?>
                        <p class="empty-text">На сегодня записей нет</p>
                    <?php else: ?>
                        <?php foreach ($grouped['today'] as $b) echo renderBookingCard($b, true); ?>
                    <?php endif; ?>
                </div>

                <div class="schedule-group">
                    <h2 class="schedule-title tomorrow">Завтра <span>(<?= count($grouped['tomorrow']) ?>)</span></h2>
                    <?php if (empty($grouped['tomorrow'])): ?>
                        <p class="empty-text">На завтра записей нет</p>
                    <?php else: ?>
                        <?php foreach ($grouped['tomorrow'] as $b) echo renderBookingCard($b, false); ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($grouped['future'])): ?>
                    <div class="schedule-group">
                        <h2 class="schedule-title future">Будущие записи <span>(<?= count($grouped['future']) ?>)</span></h2>
                        <?php foreach ($grouped['future'] as $b) echo renderBookingCard($b, false); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($grouped['history'])): ?>
                    <div class="schedule-group">
                        <h2 class="schedule-title history">История <span>(<?= count($grouped['history']) ?>)</span></h2>
                        <?php foreach ($grouped['history'] as $b) echo renderBookingCard($b, false, true); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ВКЛАДКА 2: МОИ ВИЗИТЫ -->
        <div class="tab-content" id="tab-my-visits">
            <div class="master-schedule">
                <div class="schedule-group">
                    <h2 class="schedule-title today">Предстоящие визиты <span>(<?= count($myVisitsActive) ?>)</span></h2>
                    <?php if (empty($myVisitsActive)): ?>
                        <p class="empty-text">У вас нет предстоящих визитов</p>
                    <?php else: ?>
                        <?php foreach ($myVisitsActive as $v) echo renderMyVisitCard($v); ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($myVisitsHistory)): ?>
                    <div class="schedule-group">
                        <h2 class="schedule-title history">Прошедшие визиты <span>(<?= count($myVisitsHistory) ?>)</span></h2>
                        <?php foreach ($myVisitsHistory as $v) echo renderMyVisitCard($v, true); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Подключаем внешний скрипт для этой страницы -->
    <script src="/master/js/tabs.js"></script>

<?php require_once __DIR__ . '/../template/footer.php'; ?>