<?php
require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/auth_helpers.php';

protectPage('admin');

$tab = $_GET['tab'] ?? 'bookings';
$pageTitle = 'Админ-панель — Barbershop';
require_once __DIR__ . '/../template/header.php';
echo '<link rel="stylesheet" href="/template/pages/admin/style.css">';
?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Панель администратора</h1>
            <a href="/actions/logout.php" class="admin-logout">Выйти</a>
        </div>

        <div class="admin-layout">
            <aside class="admin-sidebar">
                <nav class="admin-nav">
                    <a href="?tab=bookings" class="<?= $tab === 'bookings' ? 'active' : '' ?>">📅 Записи</a>
                    <a href="?tab=masters" class="<?= $tab === 'masters' ? 'active' : '' ?>">✂️ Мастера</a>
                    <a href="?tab=services" class="<?= $tab === 'services' ? 'active' : '' ?>">💰 Услуги</a>
                    <a href="?tab=users" class="<?= $tab === 'users' ? 'active' : '' ?>">👥 Пользователи</a>
                    <a href="?tab=certificates" class="<?= $tab === 'certificates' ? 'active' : '' ?>">🎁 Сертификаты</a>
                </nav>
            </aside>

            <main class="admin-content">
                <?php
                $viewFile = __DIR__ . '/views/' . $tab . '.php';
                if (file_exists($viewFile)) {
                    require $viewFile;
                } else {
                    echo '<p>Раздел не найден.</p>';
                }
                ?>
            </main>
        </div>
    </div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>