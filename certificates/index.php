<?php
require_once __DIR__ . '/../src/init.php';

$message = '';
$messageType = '';

// Обработка формы активации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_code'])) {
    if (!isset($_SESSION['user_id'])) {
        $message = "Для активации необходимо войти в аккаунт.";
        $messageType = "error";
    } else {
        $code = strtoupper(trim($_POST['code']));
        $userId = $_SESSION['user_id'];

        // Ищем сертификат
        $stmt = $db->prepare("SELECT * FROM certificates WHERE code = ?");
        $stmt->execute([$code]);
        $cert = $stmt->fetch();

        if (!$cert) {
            $message = "Сертификат с таким кодом не найден.";
            $messageType = "error";
        } elseif ($cert['status'] === 'used') {
            $message = "Этот сертификат уже погашен.";
            $messageType = "error";
        } elseif ($cert['user_id'] !== null && $cert['user_id'] != $userId) {
            $message = "Этот сертификат уже привязан к другому пользователю.";
            $messageType = "error";
        } elseif ($cert['user_id'] == $userId) {
            $message = "Вы уже активировали этот сертификат.";
            $messageType = "error";
        } else {
            // Активируем (привязываем к пользователю)
            $stmtUpdate = $db->prepare("UPDATE certificates SET user_id = ? WHERE id = ?");
            $stmtUpdate->execute([$userId, $cert['id']]);

            $message = "Сертификат успешно активирован! Номинал: " . number_format($cert['price'], 0, '', ' ') . " ₽. Он появился в вашем личном кабинете.";
            $messageType = "success";
        }
    }
}

$pageTitle = 'Подарочные сертификаты — Barbershop';
require_once __DIR__ . '/../template/header.php';
echo '<link rel="stylesheet" href="/template/components/certificates/style.css">';
?>

    <div class="container" style="margin-top: 5rem; margin-bottom: 5rem; max-width: 600px;">
        <div class="cert-activation-card">
            <div class="cert-icon"><i class="bi bi-gift"></i></div>
            <h1 class="cert-title">Активация сертификата</h1>
            <p class="cert-subtitle">Введите уникальный код с вашей подарочной карты, чтобы зачислить его на свой аккаунт.</p>

            <?php if ($message): ?>
                <div class="cert-message <?= $messageType ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" class="cert-form">
                    <input type="text" name="code" placeholder="GIFT-XXXXXXXX" required class="cert-input" autocomplete="off">
                    <button type="submit" name="activate_code" class="cert-submit">Активировать</button>
                </form>
            <?php else: ?>
                <div class="cert-login-block">
                    <p>Чтобы активировать сертификат, вам нужно войти в систему.</p>
                    <a href="/login/" class="cert-submit" style="display:inline-block; text-decoration:none;">Войти в аккаунт</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>