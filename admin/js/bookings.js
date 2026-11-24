/**
 * Логика вкладки "Записи" в админ-панели
 * Файл: admin/js/bookings.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.admin-tab-btn');
    const cards = document.querySelectorAll('.admin-booking-card');

    if (tabs.length === 0 || cards.length === 0) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Переключаем активный класс у кнопок
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const filter = tab.getAttribute('data-filter');

            // Фильтруем карточки
            cards.forEach(card => {
                const status = card.getAttribute('data-status');
                if (filter === 'all' || status === filter) {
                    card.style.display = 'flex'; // Показываем
                } else {
                    card.style.display = 'none'; // Скрываем
                }
            });
        });
    });
});