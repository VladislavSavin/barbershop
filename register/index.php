<?php
require_once __DIR__ . '/../src/init.php';
if (isset($_SESSION['user_id'])) { header('Location: /profile/'); exit; }

$pageTitle = 'Регистрация — Barbershop';
require_once __DIR__ . '/../template/header.php';

echo '<link rel="stylesheet" href="/template/pages/auth/style.css">';
?>
    <div class="container" style="margin-top: 4rem; margin-bottom: 4rem; max-width: 400px;">
        <div class="auth-card">
            <h1 class="auth-title">Регистрация</h1>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="auth-error"><?= htmlspecialchars($_SESSION['error']) ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/actions/auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label>Ваше имя:</label>
                    <input type="text" name="name" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Телефон:</label>
                    <input type="tel" name="phone" placeholder="+7 (999) 123-45-67" required pattern="^(\+7|8)?[\s\-]?\(?9[0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$" title="Введите российский номер: +79001234567 или 89001234567" class="form-control">
                </div>
                <div class="form-group">
                    <label>Пароль:</label>
                    <input type="password" name="password" required minlength="8" placeholder="Минимум 8 символов" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email (необязательно):</label>
                    <input type="email" name="email" placeholder="example@mail.ru" class="form-control">
                    <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.3rem; display: block;">
                        Нужен для восстановления пароля. Можно указать позже в профиле.
                    </small>
                </div>
                <button type="submit" class="btn-submit">Создать аккаунт</button>
            </form>
            <p class="auth-link">Уже есть аккаунт? <a href="/login/">Войти</a></p>
        </div>
    </div>
<?php require_once __DIR__ . '/../template/footer.php'; ?>