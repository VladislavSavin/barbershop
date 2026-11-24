<?php
require_once __DIR__ . '/../src/init.php';
$pageTitle = 'Все услуги — Barbershop & Tattoo';
require_once __DIR__ . '/../template/header.php';
?>

    <div class="container" style="margin-top: 4rem; margin-bottom: 4rem;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 class="section-title">Прайс-лист</h1>
            <p class="section-subtitle">Выберите категорию, чтобы увидеть услуги</p>
        </div>

        <?php
        // Подключаем стили и компонент
        echo '<link rel="stylesheet" href="/template/components/services-catalog/style.css">';
        require_once __DIR__ . '/../components/services-catalog/component.php';
        echo renderServicesCatalog($db);
        ?>
    </div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>