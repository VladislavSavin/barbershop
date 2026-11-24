<?php
function renderAboutInterior($images) {
    ob_start();
    ?>
    <div class="container">
        <div class="interior-block">
            <div class="interior-header">
                <h2 class="section-title">Наш интерьер</h2>
            </div>
            <div class="interior-grid">
                <?php foreach ($images as $img): ?>
                    <div class="interior-img <?= $img['class'] ?>" data-src="<?= htmlspecialchars($img['src']) ?>">
                        <img src="<?= htmlspecialchars($img['src']) ?>" alt="<?= htmlspecialchars($img['alt']) ?>" onerror="this.src='https://via.placeholder.com/800x600/262626/c87a4e?text=Photo'">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Лайтбокс -->
    <div class="lightbox" id="lightbox">
        <button class="lightbox__close" id="lightboxClose"><i class="bi bi-x-lg"></i></button>
        <img src="" alt="Фото" id="lightboxImg">
    </div>

    <script src="/components/about-interior/script.js"></script>
    <?php
    return ob_get_clean();
}