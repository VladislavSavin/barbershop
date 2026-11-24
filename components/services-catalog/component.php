<?php
/**
 * Компонент: Каталог услуг (Сайдбар)
 * Файл: components/services-catalog/component.php
 *
 * @param PDO $db Подключение к базе данных
 */
function renderServicesCatalog($db) {
    // 1. Получаем все АКТИВНЫЕ услуги из базы
    $stmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
    $services = $stmt->fetchAll();

    // 2. Группируем их по категориям
    $groupedServices = [];
    foreach ($services as $service) {
        $groupedServices[$service['category']][] = $service;
    }

    // 3. Массив иконок для категорий (Bootstrap Icons)
    $categoryIcons = [
        'Мужские стрижки' => 'bi-scissors',
        'Женские стрижки' => 'bi-flower1',
        'Детская стрижка' => 'bi-balloon',
        'Борода и усы' => 'bi-sliders',
        'Окрашивание' => 'bi-palette',
        'Тату' => 'bi-brush',
        'Пирсинг' => 'bi-record-circle',
        'Укладка' => 'bi-wind'
    ];

    // 4. Массив ID услуг, которые мы хотим отметить как "Хит" (поменяй на свои)
    $popularServices = [1, 4, 6];

    // 5. Используем нативную функцию
    $firstCategory = array_key_first($groupedServices);

    ob_start();
    ?>
    <div class="catalog-layout">

        <!-- ЛЕВАЯ КОЛОНКА: Меню категорий -->
        <aside class="catalog-sidebar">
            <h3 class="sidebar-title">Категории</h3>

            <nav class="category-nav">
                <?php $isFirst = true; foreach ($groupedServices as $category => $items):
                    // Берем иконку из массива или ставим звездочку по умолчанию
                    $iconClass = $categoryIcons[$category] ?? 'bi-star';
                    ?>
                    <button class="category-btn <?= $isFirst ? 'active' : '' ?>"
                            data-target="cat-<?= md5($category) ?>">
                        <i class="bi <?= $iconClass ?>"></i>
                        <span><?= htmlspecialchars($category) ?></span>
                    </button>
                    <?php $isFirst = false; endforeach; ?>
            </nav>
        </aside>

        <!-- ПРАВАЯ КОЛОНКА: Услуги -->
        <div class="catalog-content">
            <?php $isFirst = true; foreach ($groupedServices as $category => $items): ?>
                <div class="services-group <?= $isFirst ? 'active' : '' ?>" id="cat-<?= md5($category) ?>">

                    <h2 class="group-title"><?= htmlspecialchars($category) ?></h2>

                    <div class="services-list">
                        <?php foreach ($items as $item): ?>
                            <div class="service-item">

                                <div class="service-item__info">
                                    <!-- Обертка для названия и бейджа -->
                                    <div class="service-item__name-wrap">
                                        <h3 class="service-item__name"><?= htmlspecialchars($item['name']) ?></h3>

                                        <!-- Проверяем, является ли услуга популярной -->
                                        <?php if (in_array($item['id'], $popularServices)): ?>
                                            <span class="service-badge">🔥 Хит</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="service-item__time">⏱ <?= $item['duration'] ?> мин</span>
                                </div>

                                <div class="service-item__action">
                                    <span class="service-item__price">
                                        <?= number_format($item['price'], 0, '', ' ') ?> ₽
                                        <?= $item['type'] === 'area' ? '<small>/см²</small>' : '' ?>
                                    </span>
                                    <a href="/booking/?service_id=<?= $item['id'] ?>" class="btn-book">
                                        Записаться
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
                <?php $isFirst = false; endforeach; ?>
        </div>
    </div>

    <!-- Подключаем скрипт -->
    <script src="/components/services-catalog/script.js"></script>
    <?php
    return ob_get_clean();
}