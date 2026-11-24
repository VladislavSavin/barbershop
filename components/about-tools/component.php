<?php
function renderAboutTools($tools) {
    ob_start();
    ?>
    <div class="container">
        <div class="tools-block">
            <div class="tools-header">
                <h2 class="section-title">Качество в деталях</h2>
                <p class="section-subtitle">Чем мы работаем и что используем</p>
            </div>

            <div class="tools-grid">
                <?php foreach ($tools as $tool): ?>
                    <div class="tool-item">
                        <div class="tool-icon"><i class="bi <?= htmlspecialchars($tool['icon']) ?>"></i></div>
                        <div class="tool-text">
                            <h3><?= htmlspecialchars($tool['title']) ?></h3>
                            <p><?= htmlspecialchars($tool['text']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}