/**
 * Логика вкладки "Мастера" в админ-панели
 * Файл: admin/js/masters.js
 */
document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 1. ВЫПАДАЮЩИЙ СПИСОК УСЛУГ
    // ==========================================
    const trigger = document.getElementById('servicesTrigger');
    const menu = document.getElementById('servicesMenu');
    const checkboxes = document.querySelectorAll('.service-checkbox');
    const triggerText = document.getElementById('servicesTriggerText');

    if (trigger && menu) {
        // Открытие/закрытие
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.classList.toggle('show');
            trigger.classList.toggle('active');
        });

        // Закрытие при клике вне меню
        document.addEventListener('click', function(e) {
            if (!menu.contains(e.target) && !trigger.contains(e.target)) {
                menu.classList.remove('show');
                trigger.classList.remove('active');
            }
        });

        // Обновление текста на кнопке
        function updateTriggerText() {
            const checkedCount = document.querySelectorAll('.service-checkbox:checked').length;
            triggerText.textContent = checkedCount === 0 ? 'Выберите услуги...' : `Выбрано услуг: ${checkedCount}`;
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateTriggerText));
        updateTriggerText();
    }

    // ==========================================
    // 2. ОТОБРАЖЕНИЕ ИМЕНИ ФАЙЛА (ФОТО)
    // ==========================================
    const fileInput = document.getElementById('avatarUpload');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                fileNameDisplay.textContent = e.target.files[0].name;
                fileNameDisplay.style.color = 'var(--text-main)';
            } else {
                fileNameDisplay.textContent = 'Не выбран';
                fileNameDisplay.style.color = 'var(--text-muted)';
            }
        });
    }
});