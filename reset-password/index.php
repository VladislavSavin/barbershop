<?php
require_once __DIR__ . '/../src/init.php';

$token = $_GET['token'] ?? '';
$message = '';
$messageType = '';
$validToken = false;
$resetRecord = null;

if (!empty($token)) {
    $stmt = $db->prepare("
        SELECT pr.*, u.name 
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW()
    ");
    $stmt->execute([$token]);
    $resetRecord = $stmt->fetch();

    if ($resetRecord) {
        $validToken = true;
    } else {
        $message = 'Ссылка недействительна или устарела.';
        $messageType = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (mb_strlen($password) < 8) {
        $message = 'Пароль должен содержать минимум 8 символов.';
        $messageType = 'error';
    } elseif ($password !== $confirm) {
        $message = 'Пароли не совпадают.';
        $messageType = 'error';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hash, $resetRecord['user_id']]);

        $db->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")->execute([$resetRecord['id']]);

        $message = 'Пароль успешно изменен! Теперь вы можете войти.';
        $messageType = 'success';
        $validToken = false;
    }
}

$pageTitle = 'Сброс пароля — Barbershop';
require_once __DIR__ . '/../template/header.php';
echo '<link rel="stylesheet" href="/template/pages/auth/style.css">';
?>

    <div class="container" style="margin-top: 4rem; margin-bottom: 4rem; max-width: 400px;">
        <div class="auth-card">
            <h1 class="auth-title">Новый пароль</h1>

            <?php if ($message): ?>
                <p class="<?= $messageType === 'success' ? 'auth-success' : 'auth-error' ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <?php if ($validToken): ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Новый пароль:</label>
                        <input type="password" name="password" required minlength="8" placeholder="Минимум 8 символов" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Повторите пароль:</label>
                        <input type="password" name="confirm_password" required minlength="8" class="form-control">
                    </div>
                    <button type="submit" class="btn-submit">Сохранить пароль</button>
                </form>
            <?php else: ?>
                <p class="auth-link" style="margin-top: 1rem;">
                    <a href="/forgot-password/">Запросить новую ссылку</a> или <a href="/login/">войти</a>
                </p>
            <?php endif; ?>
        </div>
    </div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>