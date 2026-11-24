<?php
/**
 * Компонент: Преимущества
 * Файл: components/features/component.php
 */
function renderFeatures() {
    // Массив с преимуществами
    $features = [
        [
            'icon' => 'bi-scissors',
            'title' => 'Опытные барберы',
            'desc' => 'Наши мастера — настоящие профессионалы с многолетним стажем, которые знают всё о мужском стиле.'
        ],
        [
            'icon' => 'bi-shield-check',
            'title' => 'Абсолютная стерильность',
            'desc' => 'Мы используем только одноразовые лезвия и тщательно дезинфицируем каждый инструмент после клиента.'
        ],
        [
            'icon' => 'bi-star',
            'title' => 'Премиальная косметика',
            'desc' => 'Работаем только с проверенными брендами, чтобы ваша кожа и волосы чувствовали себя идеально.'
        ],
        [
            'icon' => 'bi-calendar2-check',
            'title' => 'Удобная запись',
            'desc' => 'Запишитесь онлайн за пару минут. Мы ценим ваше время, поэтому принимаем строго по записи.'
        ]
    ];

    ob_start();
    ?>
    <div class="features-block">
        <div class="features-header">
            <h2 class="section-title">Почему выбирают нас</h2>
            <p class="section-subtitle">Мы делаем больше, чем просто стрижку</p>
        </div>

        <div class="features-grid">
            <?php foreach ($features as $feature): ?>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi <?= $feature['icon'] ?>"></i>
                    </div>
                    <h3 class="feature-title"><?= htmlspecialchars($feature['title']) ?></h3>
                    <p class="feature-desc"><?= htmlspecialchars($feature['desc']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}