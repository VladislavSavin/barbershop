<?php
require_once __DIR__ . '/../src/init.php';

// Если уже вошел — перенаправляем в зависимости от роли
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: /admin/');
    } elseif ($_SESSION['user_role'] === 'master') {
        header('Location: /master/');
    } else {
        header('Location: /profile/');
    }
    exit;
}

if (isset($_SESSION['user_id'])) { header('Location: /profile/'); exit; }

echo '<link rel="stylesheet" href="/template/pages/auth/style.css">';

$pageTitle = 'Вход — Barbershop';
require_once __DIR__ . '/../template/header.php';
?>
    <div class="container" style="margin-top: 4rem; margin-bottom: 4rem; max-width: 400px;">
        <div class="auth-card">
            <h1 class="auth-title">Вход в систему</h1>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="auth-error"><?= htmlspecialchars($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/actions/auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>Телефон:</label>
                    <input type="tel" name="phone" placeholder="+7 (999) 123-45-67" required pattern="^(\+7|8)?[\s\-]?\(?9[0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$" title="Введите российский номер: +79001234567 или 89001234567" class="form-control">
                </div>
                <div class="form-group">
                    <label>Пароль:</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <button type="submit" class="btn-submit">Войти</button>
            </form>
            <p class="auth-link" style="margin-top: 0.5rem;">
                <a href="/forgot-password/" style="color: var(--text-muted); font-size: 0.85rem;">Забыли пароль?</a>
            </p>
            <p class="auth-link">Нет аккаунта? <a href="/register/">Зарегистрироваться</a></p>
        </div>
    </div>
<?php require_once __DIR__ . '/../template/footer.php'; ?>