<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/mailer.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Введите корректный email.';
        $messageType = 'error';
    } else {
        $stmt = $db->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        $message = 'Если такой email зарегистрирован, мы отправили ссылку для восстановления.';
        $messageType = 'success';

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $db->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$user['id']]);

            $stmt = $db->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expiresAt]);

            $resetLink = "http://barbershop/reset-password/?token=$token";
            $html = "
                <h2>Восстановление пароля</h2>
                <p>Здравствуйте, {$user['name']}!</p>
                <p>Для сброса пароля перейдите по ссылке:</p>
                <p><a href='$resetLink' style='display:inline-block; padding:10px 20px; background:#c87a4e; color:#000; text-decoration:none; border-radius:5px;'>Сбросить пароль</a></p>
                <p>Ссылка действует 1 час.</p>
                <p>Если вы не запрашивали восстановление — проигнорируйте это письмо.</p>
            ";

            sendMail($email, 'Восстановление пароля — Barbershop', $html);
        }
    }
}

$pageTitle = 'Восстановление пароля — Barbershop';
require_once __DIR__ . '/../template/header.php';
echo '<link rel="stylesheet" href="/template/pages/auth/style.css">';
?>

    <div class="container" style="margin-top: 4rem; margin-bottom: 4rem; max-width: 400px;">
        <div class="auth-card">
            <h1 class="auth-title">Забыли пароль?</h1>
            <p style="color: var(--text-muted); text-align: center; margin-bottom: 1.5rem;">
                Введите email, указанный при регистрации.
            </p>

            <?php if ($message): ?>
                <p class="<?= $messageType === 'success' ? 'auth-success' : 'auth-error' ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required placeholder="example@mail.ru" class="form-control">
                </div>
                <button type="submit" class="btn-submit">Отправить ссылку</button>
            </form>

            <p class="auth-link">Вспомнили пароль? <a href="/login/">Войти</a></p>
        </div>
    </div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>