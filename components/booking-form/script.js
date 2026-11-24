/**
 * Логика страницы записи (Выбор мастера, даты, времени и оплата сертификатами)
 * Файл: components/booking-form/script.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const masterCards = document.querySelectorAll('.master-card');
    const dateCards = document.querySelectorAll('.date-card');
    const timeGrid = document.getElementById('timeGrid');

    // Скрытые поля формы
    const masterIdInput = document.getElementById('masterIdInput');
    const dateInput = document.getElementById('dateInput');
    const timeInput = document.getElementById('timeInput');

    // Форма и ID услуги
    const bookingForm = document.getElementById('bookingForm');
    const serviceIdInput = document.querySelector('input[name="service_id"]');

    // Защита: если мы не на странице записи, прекращаем работу
    if (!masterCards.length || !dateCards.length || !serviceIdInput || !bookingForm) return;
    const serviceId = serviceIdInput.value;

    // ==========================================
    // 1. ВЫБОР МАСТЕРА
    // ==========================================
    masterCards.forEach(card => {
        card.addEventListener('click', () => {
            masterCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            masterIdInput.value = card.getAttribute('data-id');
            timeInput.value = '';
            loadTimes();
        });
    });

    // ==========================================
    // 2. ВЫБОР ДАТЫ
    // ==========================================
    dateCards.forEach(card => {
        card.addEventListener('click', () => {
            dateCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            dateInput.value = card.getAttribute('data-date');
            timeInput.value = '';
            loadTimes();
        });
    });

    // ==========================================
    // 3. ЗАГРУЗКА ВРЕМЕНИ
    // ==========================================
    function loadTimes() {
        const masterId = masterIdInput.value;
        const date = dateInput.value;

        if (masterId && date) {
            timeGrid.innerHTML = '<p class="loading-text">Поиск свободных окон...</p>';

            fetch(`/api/get_times.php?master_id=${masterId}&date=${date}&service_id=${serviceId}`, {
                cache: 'no-store'
            })
                .then(response => response.json())
                .then(data => {
                    timeGrid.innerHTML = '';

                    if (data.error) {
                        timeGrid.innerHTML = `<p class="error-text">Ошибка: ${data.error}</p>`;
                        return;
                    }

                    if (data.length === 0) {
                        timeGrid.innerHTML = '<p class="empty-times">Нет свободного времени на эту дату</p>';
                        return;
                    }

                    data.forEach(time => {
                        const btn = document.createElement('div');
                        btn.className = 'time-btn';
                        btn.textContent = time;
                        btn.dataset.time = time;

                        btn.addEventListener('click', () => {
                            document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');
                            timeInput.value = time;
                        });

                        timeGrid.appendChild(btn);
                    });
                })
                .catch(error => {
                    console.error('Ошибка при загрузке времени:', error);
                    timeGrid.innerHTML = '<p class="error-text">Ошибка загрузки времени. Проверьте соединение.</p>';
                });
        }
    }

    // ==========================================
    // 4. ОПЛАТА СЕРТИФИКАТАМИ
    // ==========================================
    const useCertCheck = document.getElementById('useCertCheck');
    const certListWrapper = document.getElementById('certListWrapper');
    const certSummary = document.getElementById('certSummary');
    const certCheckboxes = document.querySelectorAll('.cert-item-checkbox');
    const certSelectedSum = document.getElementById('certSelectedSum');
    const certEnoughStatus = document.getElementById('certEnoughStatus');

    // Стоимость услуги из data-атрибута формы
    const servicePrice = parseFloat(bookingForm.getAttribute('data-service-price')) || 0;

    if (useCertCheck && certListWrapper && certSummary) {
        // Функция обновления суммы
        function updateCertSummary() {
            let sum = 0;
            certCheckboxes.forEach(cb => {
                if (cb.checked) {
                    sum += parseFloat(cb.getAttribute('data-balance')) || 0;
                }
            });

            certSelectedSum.textContent = sum.toLocaleString('ru-RU') + ' ₽';

            if (sum >= servicePrice) {
                certEnoughStatus.textContent = 'Да (полная оплата)';
                certEnoughStatus.classList.remove('cert-status-no');
                certEnoughStatus.classList.add('cert-status-yes');
            } else if (sum > 0) {
                const diff = servicePrice - sum;
                certEnoughStatus.textContent = 'Частично (доплата ' + diff.toLocaleString('ru-RU') + ' ₽)';
                certEnoughStatus.classList.remove('cert-status-no', 'cert-status-yes');
                certEnoughStatus.style.color = '#ffc107'; // Желтый цвет
            } else {
                certEnoughStatus.textContent = 'Нет';
                certEnoughStatus.classList.remove('cert-status-yes');
                certEnoughStatus.classList.add('cert-status-no');
            }
        }

        // Включение/выключение блока
        useCertCheck.addEventListener('change', () => {
            if (useCertCheck.checked) {
                certListWrapper.style.display = 'flex';
                certSummary.style.display = 'block';
            } else {
                certListWrapper.style.display = 'none';
                certSummary.style.display = 'none';
                // Снимаем выделение со всех сертификатов
                certCheckboxes.forEach(cb => cb.checked = false);
            }
            updateCertSummary();
        });

        // Изменение выбора сертификатов
        certCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateCertSummary);
        });

        // Инициализация
        updateCertSummary();
    }
});