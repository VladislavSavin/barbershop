/**
 * Логика отображения формы email в профиле
 * Файл: profile/js/email.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.querySelector('.btn-edit-email');
    const emailForm = document.getElementById('emailForm');
    const cancelBtn = document.getElementById('cancelEmailBtn');

    // Защита: если кнопки или формы нет, выходим
    if (!editBtn || !emailForm) return;

    // Показываем форму при клике на кнопку "Изменить/Добавить"
    editBtn.addEventListener('click', () => {
        emailForm.style.display = 'flex';
        editBtn.style.display = 'none';
    });

    // Скрываем форму, если пользователь передумал
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            emailForm.style.display = 'none';
            editBtn.style.display = 'inline-block';
        });
    }
});