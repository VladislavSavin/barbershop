<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/auth_helpers.php';

// Защита: пускаем только клиентов
protectPage('client');

$userId = $_SESSION['user_id'];

// Обработка отмены записи (с возвратом средств)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
    $cancelId = (int)$_POST['cancel_id'];

    // 1. Меняем статус записи
    $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND client_id = ?");
    $stmt->execute([$cancelId, $userId]);

    // 2. Возвращаем деньги с сертификатов, если они использовались
    $stmtCerts = $db->prepare("SELECT certificate_id, amount_used FROM booking_certificates WHERE booking_id = ?");
    $stmtCerts->execute([$cancelId]);
    $usedCerts = $stmtCerts->fetchAll();

    foreach ($usedCerts as $uc) {
        // Возвращаем баланс сертификата
        $db->prepare("UPDATE certificates SET balance = balance + ?, status = 'active' WHERE id = ?")
            ->execute([$uc['amount_used'], $uc['certificate_id']]);
    }

    // Удаляем связи, чтобы нельзя было вернуть дважды
    $db->prepare("DELETE FROM booking_certificates WHERE booking_id = ?")->execute([$cancelId]);

    header('Location: /profile/');
    exit;
}

// Получаем сообщения об успехе/ошибке
$successMessage = $_SESSION['profile_success'] ?? null;
unset($_SESSION['profile_success']);

$errorMessage = $_SESSION['profile_error'] ?? null;
unset($_SESSION['profile_error']);

// Получаем данные пользователя
$stmtUser = $db->prepare("SELECT name, phone, email, created_at FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch();

// Получаем записи пользователя
$stmt = $db->prepare("
    SELECT b.*, s.name as service_name, s.id as service_id, m.name as master_name 
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN masters m ON b.master_id = m.id
    WHERE b.client_id = ?
    ORDER BY b.booking_date DESC, b.start_time DESC
");
$stmt->execute([$userId]);
$allBookings = $stmt->fetchAll();

// Разделяем на активные и историю + статистика
$activeBookings = [];
$historyBookings = [];
$totalSpent = 0;
$totalVisits = 0;

foreach ($allBookings as $b) {
    $isPast = strtotime($b['booking_date'] . ' ' . $b['start_time']) < time();
    $isCancelled = $b['status'] === 'cancelled';
    $isCompleted = $b['status'] === 'completed';

    if ($isPast || $isCancelled || $isCompleted) {
        $historyBookings[] = $b;
        if ($isCompleted) {
            $totalSpent += $b['total_price'];
            $totalVisits++;
        }
    } else {
        $activeBookings[] = $b;
    }
}

// Получаем активные сертификаты пользователя
$stmtCerts = $db->prepare("
    SELECT * FROM certificates 
    WHERE user_id = ? AND status = 'active' AND balance > 0 
    ORDER BY price ASC
");
$stmtCerts->execute([$userId]);
$myCertificates = $stmtCerts->fetchAll();

$pageTitle = 'Личный кабинет — Barbershop';
require_once __DIR__ . '/../template/header.php';
echo '<link rel="stylesheet" href="/template/pages/profile/style.css">';
?>

    <div class="container profile-container">
        <!-- Уведомления -->
        <?php if ($successMessage): ?>
            <div class="profile-alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="profile-alert alert-error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <div class="profile-layout">
            <!-- ЛЕВЫЙ САЙДБАР -->
            <aside class="user-sidebar">
                <div class="user-avatar-large"><?= mb_substr($user['name'], 0, 1) ?></div>
                <h2 class="user-name-large"><?= htmlspecialchars($user['name']) ?></h2>
                <p class="user-phone"><?= htmlspecialchars($user['phone']) ?></p>

                <!-- Блок email -->
                <div class="user-email-block">
                    <?php if (!empty($user['email'])): ?>
                        <p class="user-email">📧 <?= htmlspecialchars($user['email']) ?></p>
                        <button class="btn-edit-email" id="editEmailBtn">Изменить</button>
                    <?php else: ?>
                        <p class="user-email-empty">📧 Email не указан</p>
                        <button class="btn-edit-email" id="editEmailBtn">Добавить email</button>
                    <?php endif; ?>

                    <form method="POST" action="/actions/update_email.php" class="email-form" id="emailForm">
                        <input type="email" name="email" required placeholder="example@mail.ru" class="email-input">
                        <div class="email-form-actions">
                            <button type="submit" class="btn-save-email">Сохранить</button>
                            <button type="button" class="btn-cancel-email" id="cancelEmailBtn">Отмена</button>
                        </div>
                    </form>
                </div>

                <div class="user-stats">
                    <div class="stat-box">
                        <span class="stat-val"><?= $totalVisits ?></span>
                        <span class="stat-label">Визитов</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-val"><?= number_format($totalSpent, 0, '', ' ') ?></span>
                        <span class="stat-label">Потрачено (₽)</span>
                    </div>
                </div>

                <a href="/actions/logout.php" class="btn-logout-profile">Выйти из аккаунта</a>
            </aside>

            <!-- ПРАВАЯ ЧАСТЬ -->
            <main class="profile-main">
                <div class="profile-tabs">
                    <button class="tab-btn active" data-target="tab-active">Активные записи (<?= count($activeBookings) ?>)</button>
                    <button class="tab-btn" data-target="tab-history">История</button>
                </div>

                <!-- Таб 1: Активные -->
                <div class="tab-content active" id="tab-active">
                    <?php if (empty($activeBookings)): ?>
                        <div class="empty-state">
                            <p>У вас нет активных записей.</p>
                            <a href="/services/" class="btn-repeat empty-btn">Записаться</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($activeBookings as $b): ?>
                            <div class="booking-card-profile">
                                <div class="bc-info">
                                    <h3><?= htmlspecialchars($b['service_name']) ?></h3>
                                    <p class="master-name">Мастер: <?= htmlspecialchars($b['master_name']) ?></p>
                                    <p class="datetime"><?= date('d.m.Y', strtotime($b['booking_date'])) ?> в <?= substr($b['start_time'], 0, 5) ?></p>
                                </div>
                                <div class="bc-actions">
                                    <div class="bc-price"><?= number_format($b['total_price'], 0, '', ' ') ?> ₽</div>
                                    <span class="status-badge-p status-<?= $b['status'] ?>">
                                    <?= $b['status'] === 'pending' ? 'Ожидает' : 'Подтверждена' ?>
                                </span>
                                    <form method="POST" onsubmit="return confirm('Точно отменить запись?');">
                                        <input type="hidden" name="cancel_id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn-cancel">Отменить</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Таб 2: История -->
                <div class="tab-content" id="tab-history">
                    <?php if (empty($historyBookings)): ?>
                        <div class="empty-state">
                            <p>История визитов пуста.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($historyBookings as $b): ?>
                            <div class="booking-card-profile is-past">
                                <div class="bc-info">
                                    <h3><?= htmlspecialchars($b['service_name']) ?></h3>
                                    <p class="master-name">Мастер: <?= htmlspecialchars($b['master_name']) ?></p>
                                    <p class="datetime datetime-muted"><?= date('d.m.Y', strtotime($b['booking_date'])) ?> в <?= substr($b['start_time'], 0, 5) ?></p>
                                </div>
                                <div class="bc-actions">
                                    <div class="bc-price bc-price-muted"><?= number_format($b['total_price'], 0, '', ' ') ?> ₽</div>
                                    <span class="status-badge-p status-<?= $b['status'] ?>">
                                    <?= $b['status'] === 'completed' ? 'Завершена' : 'Отменена' ?>
                                </span>
                                    <a href="/booking/?service_id=<?= $b['service_id'] ?>" class="btn-repeat">Повторить</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Блок сертификатов -->
                <?php if (!empty($myCertificates)): ?>
                    <div class="profile-certs-block">
                        <h3 class="profile-certs-title">🎁 Мои сертификаты</h3>
                        <div class="profile-certs-grid">
                            <?php foreach ($myCertificates as $c): ?>
                                <div class="profile-cert-card">
                                    <div class="pc-top">
                                        <span class="pc-nominal"><?= number_format($c['price'], 0, '', ' ') ?> ₽</span>
                                        <span class="pc-badge">Активен</span>
                                    </div>
                                    <div class="pc-code"><?= htmlspecialchars($c['code']) ?></div>
                                    <div class="pc-balance">Доступно: <strong><?= number_format($c['balance'], 0, '', ' ') ?> ₽</strong></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Подключаем внешние скрипты для этой страницы -->
    <script src="/profile/js/tabs.js"></script>
    <script src="/profile/js/email.js"></script>

<?php require_once __DIR__ . '/../template/footer.php'; ?>