<?php
/**
 * Компонент: Главный баннер (Hero)
 * Файл: components/banner/component.php
 *
 * @param array $data Параметры баннера (badge, title, subtitle, btn_text, btn_link, bg_image)
 */
function renderHeroBanner($data = []) {
    $defaults = [
        'badge'    => '',
        'title'    => 'Добро пожаловать',
        'subtitle' => '',
        'btn_text' => 'Записаться',
        'btn_link' => '/booking/',
        'bg_image' => '/assets/images/default-banner.jpg' // Заглушка
    ];
    $data = array_merge($defaults, $data);

    ob_start();
    ?>
    <section class="hero-banner" style="background-image: url('<?= htmlspecialchars($data['bg_image']) ?>');">
        <div class="hero-banner__overlay"></div>

        <div class="container hero-banner__container">
            <div class="hero-banner__content">
                <?php if ($data['badge']): ?>
                    <span class="hero-banner__badge"><?= htmlspecialchars($data['badge']) ?></span>
                <?php endif; ?>

                <h1 class="hero-banner__title"><?= $data['title'] ?></h1>

                <?php if ($data['subtitle']): ?>
                    <p class="hero-banner__subtitle"><?= htmlspecialchars($data['subtitle']) ?></p>
                <?php endif; ?>

                <?php if ($data['btn_text']): ?>
                    <a href="<?= htmlspecialchars($data['btn_link']) ?>" class="hero-banner__btn">
                        <?= htmlspecialchars($data['btn_text']) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}