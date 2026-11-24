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