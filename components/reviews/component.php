<?php
/**
 * Компонент: Отзывы (слайдер)
 * Файл: components/reviews/component.php
 */
function renderReviews($db) {
    // Получаем последние 20 отзывов
    $stmt = $db->query("
        SELECT r.*, u.name as user_name 
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
        LIMIT 20
    ");
    $reviews = $stmt->fetchAll();

    ob_start();
    ?>
    <div class="reviews-block" id="reviews">

        <div class="reviews-slider-header">
            <div>
                <h2 class="section-title">Отзывы наших клиентов</h2>
                <p class="section-subtitle">Что о нас говорят</p>
            </div>

            <?php if (count($reviews) > 3): ?>
                <div class="slider-controls">
                    <button class="slider-btn" id="reviewsPrev" aria-label="Назад">←</button>
                    <button class="slider-btn" id="reviewsNext" aria-label="Вперед">→</button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Контейнер слайдера -->
        <div class="reviews-slider-wrapper">
            <div class="reviews-track" id="reviewsTrack">
                <?php if (empty($reviews)): ?>
                    <p style="color: var(--text-muted); width: 100%; text-align: center; padding: 2rem;">Отзывов пока нет. Будьте первым!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $r): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-author">
                                    <div class="review-avatar"><?= mb_substr($r['user_name'], 0, 1) ?></div>
                                    <span class="review-name"><?= htmlspecialchars($r['user_name']) ?></span>
                                </div>
                                <div class="review-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star-fill" style="color: <?= $i <= $r['rating'] ? 'var(--accent-color)' : '#333' ?>;"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="review-text"><?= nl2br(htmlspecialchars($r['text'])) ?></p>
                            <span class="review-date"><?= date('d.m.Y', strtotime($r['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Кнопка вызова модального окна -->
        <div class="reviews-action">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn-open-review" id="openReviewModal">Оставить отзыв</button>
            <?php else: ?>
                <p class="guest-hint">Хотите оставить отзыв? <a href="/login/">Войдите в аккаунт</a></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- МОДАЛЬНОЕ ОКНО (остается без изменений) -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="review-modal" id="reviewModal">
            <div class="review-modal__overlay" id="reviewModalOverlay"></div>
            <div class="review-modal__content">
                <button class="review-modal__close" id="closeReviewModal"><i class="bi bi-x-lg"></i></button>

                <h3 class="review-modal__title">Оставить отзыв</h3>
                <p class="review-modal__subtitle">Нам важно ваше мнение!</p>

                <form action="/actions/add_review.php" method="POST" class="review-form">
                    <div class="rating-wrapper">
                        <span class="rating-label">Оцените наш сервис</span>
                        <div class="rating-input">
                            <input type="radio" name="rating" value="5" id="star5" checked><label for="star5"><i class="bi bi-star-fill"></i></label>
                            <input type="radio" name="rating" value="4" id="star4"><label for="star4"><i class="bi bi-star-fill"></i></label>
                            <input type="radio" name="rating" value="3" id="star3"><label for="star3"><i class="bi bi-star-fill"></i></label>
                            <input type="radio" name="rating" value="2" id="star2"><label for="star2"><i class="bi bi-star-fill"></i></label>
                            <input type="radio" name="rating" value="1" id="star1"><label for="star1"><i class="bi bi-star-fill"></i></label>
                        </div>
                    </div>

                    <textarea name="text" placeholder="Поделитесь своими впечатлениями..." required class="form-control review-textarea" rows="4"></textarea>
                    <button type="submit" class="btn-submit">Отправить отзыв</button>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Логика модалки
                const modal = document.getElementById('reviewModal');
                const openBtn = document.getElementById('openReviewModal');
                const closeBtn = document.getElementById('closeReviewModal');
                const overlay = document.getElementById('reviewModalOverlay');

                if (modal && openBtn) {
                    openBtn.addEventListener('click', () => { modal.classList.add('active'); document.body.style.overflow = 'hidden'; });
                    if (closeBtn) closeBtn.addEventListener('click', () => { modal.classList.remove('active'); document.body.style.overflow = ''; });
                    if (overlay) overlay.addEventListener('click', () => { modal.classList.remove('active'); document.body.style.overflow = ''; });
                }
            });
        </script>
    <?php endif; ?>

    <!-- СКРИПТ ДЛЯ СЛАЙДЕРА -->
    <?php if (!empty($reviews)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const track = document.getElementById('reviewsTrack');
                const prevBtn = document.getElementById('reviewsPrev');
                const nextBtn = document.getElementById('reviewsNext');

                if (!track || !prevBtn || !nextBtn) return;

                // Функция для вычисления ширины прокрутки (ширина одной карточки + отступ gap)
                function getScrollAmount() {
                    const card = track.querySelector('.review-card');
                    if (!card) return 0;
                    const gap = parseInt(window.getComputedStyle(track).gap) || 0;
                    return card.offsetWidth + gap;
                }

                nextBtn.addEventListener('click', () => {
                    track.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
                });

                prevBtn.addEventListener('click', () => {
                    track.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
                });
            });
        </script>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}