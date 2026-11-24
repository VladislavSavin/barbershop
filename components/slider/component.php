<?php
function renderSlider($slides) {
    if (empty($slides)) return '<p style="color: var(--text-muted); text-align:center;">Пока нет работ.</p>';

    ob_start();
    ?>
    <div class="slider-block">
        <div class="slider-header">
            <div>
                <h2 class="section-title">Наши работы</h2>
                <p class="section-subtitle">Выберите работу, чтобы рассмотреть</p>
            </div>
        </div>

        <!-- Наш новый контейнер-гармошка -->
        <div class="accordion-gallery" id="accordionGallery">
            <?php foreach ($slides as $index => $slide): ?>
                <!-- Первая карточка по умолчанию активна (класс active) -->
                <div class="accordion-card <?= $index === 0 ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($slide['image_path']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>">

                    <div class="accordion-overlay">
                        <span class="slide-category"><?= htmlspecialchars($slide['category']) ?></span>
                        <h3 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Подключаем новый скрипт -->
    <script src="/components/slider/script.js"></script>
    <?php
    return ob_get_clean();
}