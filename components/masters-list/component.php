<?php
function renderMastersList($db) {
    $stmt = $db->query("SELECT * FROM masters WHERE is_active = 1 ORDER BY id ASC");
    $masters = $stmt->fetchAll();
    if (empty($masters)) return '';

    ob_start();
    ?>
    <div class="masters-block">
        <div class="masters-header">
            <h2 class="section-title">Наша команда</h2>
            <p class="section-subtitle">Профессионалы, влюбленные в свое дело</p>
        </div>

        <div class="masters-accordion">
            <?php foreach ($masters as $index => $m): ?>
                <!-- Первая карточка открыта по умолчанию -->
                <div class="master-acc-card <?= $index === 0 ? 'active' : '' ?>" data-id="<?= $m['id'] ?>">
                    <?php if (!empty($m['avatar'])): ?>
                        <img src="<?= htmlspecialchars($m['avatar']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x600/262626/c87a4e?text=<?= mb_substr($m['name'], 0, 1) ?>" alt="<?= htmlspecialchars($m['name']) ?>">
                    <?php endif; ?>

                    <div class="master-acc-content">
                        <h3><?= htmlspecialchars($m['name']) ?></h3>
                        <p><?= htmlspecialchars($m['specialization']) ?></p>
                        <a href="/services/" class="btn-master-book" onclick="event.stopPropagation();">Записаться →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.master-acc-card');
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    cards.forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}