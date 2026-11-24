<?php
function renderAboutIntro($data) {
    ob_start();
    ?>
    <div class="container">
        <div class="about-intro">
            <h1><?= htmlspecialchars($data['title']) ?></h1>
            <p><?= htmlspecialchars($data['text']) ?></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}