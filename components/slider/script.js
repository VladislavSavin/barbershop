document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.accordion-card');

    if (cards.length === 0) return;

    cards.forEach(card => {
        card.addEventListener('click', function() {
            // Если карточка уже активна, ничего не делаем
            if (this.classList.contains('active')) return;

            // Убираем класс active со всех карточек
            cards.forEach(c => c.classList.remove('active'));

            // Добавляем класс active той, на которую кликнули
            this.classList.add('active');
        });
    });
});