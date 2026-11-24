<?php
/**
 * Компонент: Сетка популярных услуг
 * Файл: components/list-services/component.php
 *
 * @param PDO $db Подключение к базе
 * @param int|null $limit Количество услуг (null - вывести все)
 */
function renderServicesList($db, $limit = 6) {
    // Если лимит не задан, выводим все активные услуги
    if ($limit === null) {
        $stmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY category, name");
    } else {
        // Иначе выводим только указанное количество активных услуг
        $stmt = $db->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY category, name LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
    }

    $services = $stmt->fetchAll();

    // Группируем по категориям
    $groupedServices = [];
    foreach ($services as $service) {
        $groupedServices[$service['category']][] = $service;
    }

    ob_start();
    ?>
    <div class="services-block">
        <div class="services-header">
            <div>
                <h2 class="section-title">Популярные услуги</h2>
                <p class="section-subtitle">Нажмите на карточку, чтобы записаться</p>
            </div>
            <a href="/services/" class="view-all-link">Все услуги →</a>
        </div>

        <div class="services-horizontal-grid">
            <?php foreach ($services as $item): ?>
                <div class="service-row-card">
                    <div class="service-row-card__info">
                        <span class="service-row-card__category"><?= htmlspecialchars($item['category']) ?></span>
                        <h3 class="service-row-card__name"><?= htmlspecialchars($item['name']) ?></h3>
                        <span class="service-row-card__time">⏱ <?= $item['duration'] ?> мин</span>
                    </div>

                    <div class="service-row-card__action">
                        <span class="service-row-card__price">
                            <?= number_format($item['price'], 0, '', ' ') ?> ₽
                            <?= $item['type'] === 'area' ? '<small>/см²</small>' : '' ?>
                        </span>
                        <a href="/booking/?service_id=<?= $item['id'] ?>" class="service-row-card__btn">
                            Записаться
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}