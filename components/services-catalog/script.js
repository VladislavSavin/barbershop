document.addEventListener('DOMContentLoaded', function() {
    const btns = document.querySelectorAll('.category-btn');
    const groups = document.querySelectorAll('.services-group');

    if (btns.length === 0) return;

    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');

            // 1. Переключаем активную кнопку
            btns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // 2. Переключаем блок с услугами
            groups.forEach(group => group.classList.remove('active'));
            const targetGroup = document.getElementById(targetId);
            if (targetGroup) targetGroup.classList.add('active');
        });
    });
});