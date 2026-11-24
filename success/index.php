<?php
require_once __DIR__ . '/../src/init.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id === 0) {
    header('Location: /');
    exit;
}

// Получаем данные записи для вывода
$stmt = $db->prepare("
    SELECT b.*, s.name as service_name, m.name as master_name 
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN masters m ON b.master_id = m.id
    WHERE b.id = ?
");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    die("Запись не найдена.");
}

$pageTitle = 'Вы успешно записаны! — Barbershop';
require_once __DIR__ . '/../template/header.php';

// Подключаем стили компонента
echo '<link rel="stylesheet" href="/template/pages/success/style.css">';
?>

    <div class="container" style="max-width: 600px;">
        <div class="success-card">
            <div class="success-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>

            <h1 class="success-title">Вы записаны!</h1>
            <p class="success-subtitle">Ждем вас в нашем барбершопе. Пожалуйста, не опаздывайте.</p>

            <div class="receipt">
                <div class="receipt-row">
                    <span>Услуга:</span>
                    <strong><?= htmlspecialchars($booking['service_name']) ?></strong>
                </div>
                <div class="receipt-row">
                    <span>Мастер:</span>
                    <strong><?= htmlspecialchars($booking['master_name']) ?></strong>
                </div>
                <div class="receipt-row">
                    <span>Дата:</span>
                    <strong><?= date('d.m.Y', strtotime($booking['booking_date'])) ?></strong>
                </div>
                <div class="receipt-row">
                    <span>Время:</span>
                    <strong><?= substr($booking['start_time'], 0, 5) ?></strong>
                </div>
                <div class="receipt-row total">
                    <span>К оплате:</span>
                    <strong><?= number_format($booking['total_price'], 0, '', ' ') ?> ₽</strong>
                </div>
            </div>

            <a href="/" class="btn-home">Вернуться на главную</a>
        </div>
    </div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>