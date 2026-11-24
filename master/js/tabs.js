/**
 * Логика переключения вкладок в кабинете мастера
 * Файл: master/js/tabs.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.master-tabs .tab-btn');
    const contents = document.querySelectorAll('.tab-content');

    // Защита: если элементов нет, выходим
    if (tabs.length === 0 || contents.length === 0) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Снимаем активный класс со всех табов и контента
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            // Добавляем активный класс текущему табу
            tab.classList.add('active');

            // Показываем нужный контент
            const targetId = tab.getAttribute('data-target');
            const targetContent = document.getElementById(targetId);

            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
});