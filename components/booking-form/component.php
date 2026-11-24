<?php
/**
 * Компонент: Форма записи на услугу
 * Файл: components/booking-form/component.php
 */
function renderBookingForm($service, $availableMasters, $currentUser = null, $certificates = []) {
    ob_start();
    ?>
    <div class="booking-wrapper">
        <a href="/services/" class="booking-back">← Назад к услугам</a>

        <div class="booking-card">
            <h1 class="booking-title"><?= htmlspecialchars($service['name']) ?></h1>
            <p class="booking-meta">
                Длительность: <strong><?= $service['duration'] ?> мин.</strong> |
                Цена: <strong><?= number_format($service['price'], 0, '', ' ') ?> ₽ <?= $service['type'] === 'area' ? '/ см²' : '' ?></strong>
            </p>

            <?php if (empty($availableMasters)): ?>
                <p class="booking-error">К сожалению, на эту услугу пока нет доступных мастеров.</p>
            <?php else: ?>
                <form action="/actions/process_booking.php" method="POST" class="booking-form" id="bookingForm" data-service-price="<?= (float)$service['price'] ?>">
                    <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                    <input type="hidden" name="master_id" id="masterIdInput" required>
                    <input type="hidden" name="date" id="dateInput" required>
                    <input type="hidden" name="time" id="timeInput" required>

                    <!-- ВЫБОР МАСТЕРА -->
                    <div class="form-group">
                        <label>1. Выберите мастера</label>
                        <div class="masters-grid">
                            <?php foreach ($availableMasters as $master): ?>
                                <div class="master-card" data-id="<?= $master['id'] ?>">
                                    <div class="master-avatar">
                                        <?php if (!empty($master['avatar'])): ?>
                                            <img src="<?= htmlspecialchars($master['avatar']) ?>" alt="<?= htmlspecialchars($master['name']) ?>">
                                        <?php else: ?>
                                            <div class="avatar-placeholder"><?= mb_substr($master['name'], 0, 1) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="master-name"><?= htmlspecialchars($master['name']) ?></span>
                                    <div class="master-check"><i class="bi bi-check-lg"></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- ВЫБОР ДАТЫ -->
                    <div class="form-group">
                        <label>2. Выберите дату</label>
                        <div class="dates-strip">
                            <?php
                            $months = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
                            $weekdays = ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'];
                            for ($i = 0; $i < 14; $i++):
                                $time = strtotime("+$i days");
                                $dateVal = date('Y-m-d', $time);
                                $day = date('d', $time);
                                $wd = $weekdays[date('w', $time)];
                                $m = $months[date('n', $time) - 1];
                                ?>
                                <div class="date-card" data-date="<?= $dateVal ?>">
                                    <span class="date-wd"><?= $wd ?></span>
                                    <span class="date-day"><?= $day ?></span>
                                    <span class="date-m"><?= $m ?></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- ВЫБОР ВРЕМЕНИ -->
                    <div class="form-group">
                        <label>3. Выберите время</label>
                        <div class="time-grid" id="timeGrid">
                            <p class="empty-times">Сначала выберите мастера и дату</p>
                        </div>
                    </div>

                    <!-- БЛОК С КОНТАКТАМИ -->
                    <?php if ($currentUser): ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ваше имя:</label>
                                <input type="text" value="<?= htmlspecialchars($currentUser['name']) ?>" readonly class="form-control readonly-input">
                            </div>
                            <div class="form-group">
                                <label>Телефон:</label>
                                <input type="tel" value="<?= htmlspecialchars($currentUser['phone']) ?>" readonly class="form-control readonly-input">
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ваше имя:</label>
                                <input type="text" name="client_name" required placeholder="Иван" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Телефон:</label>
                                <input type="tel" name="client_phone" required placeholder="+7 (999) 123-45-67" class="form-control">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($service['type'] === 'area'): ?>
                        <div class="form-group area-group">
                            <label>Площадь татуировки (см²):</label>
                            <input type="number" name="area" min="1" value="1" required class="form-control">
                            <small>Итоговая цена будет рассчитана автоматически.</small>
                        </div>
                    <?php endif; ?>

                    <!-- ВЫБОР СЕРТИФИКАТА ДЛЯ ОПЛАТЫ -->
                    <?php if (!empty($certificates) && $service['type'] === 'fixed'): ?>
                        <div class="form-group cert-payment-block">
                            <label class="cert-toggle-label">
                                <input type="checkbox" name="use_certificate" id="useCertCheck" value="1" class="cert-toggle-checkbox">
                                <span>🎁 Оплатить сертификатами</span>
                            </label>

                            <div id="certListWrapper" class="cert-list-wrapper">
                                <?php foreach ($certificates as $c): ?>
                                    <label class="cert-item-label">
                                        <input type="checkbox" name="certificate_ids[]" value="<?= $c['id'] ?>" class="cert-item-checkbox" data-balance="<?= $c['balance'] ?>">
                                        <span class="cert-item-code">Сертификат <?= htmlspecialchars($c['code']) ?></span>
                                        <strong class="cert-item-balance"><?= number_format($c['balance'], 0, '', ' ') ?> ₽</strong>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <div id="certSummary" class="cert-summary">
                                <div class="cert-summary-row">
                                    <span>Стоимость услуги:</span>
                                    <strong><?= number_format($service['price'], 0, '', ' ') ?> ₽</strong>
                                </div>
                                <div class="cert-summary-row">
                                    <span>Выбрано сертификатов:</span>
                                    <strong id="certSelectedSum">0 ₽</strong>
                                </div>
                                <div class="cert-summary-row cert-summary-total">
                                    <span>Хватит на услугу?</span>
                                    <strong id="certEnoughStatus" class="cert-status-no">Нет</strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn-submit">Записаться на прием</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- МОДАЛЬНОЕ ОКНО КОНФЛИКТА ЗАПИСЕЙ -->
    <?php if (isset($_SESSION['booking_conflict'])): ?>
        <div class="conflict-modal active" id="conflictModal">
            <div class="conflict-modal__overlay" onclick="document.getElementById('conflictModal').classList.remove('active')"></div>
            <div class="conflict-modal__content">
                <div class="conflict-modal__icon">⚠️</div>
                <h3 class="conflict-modal__title">Вы уже записаны!</h3>
                <p class="conflict-modal__text">На это время у вас уже есть запись:</p>

                <div class="conflict-details">
                    <div class="conflict-row">
                        <span>Мастер:</span>
                        <strong><?= htmlspecialchars($_SESSION['booking_conflict']['master']) ?></strong>
                    </div>
                    <div class="conflict-row">
                        <span>Услуга:</span>
                        <strong><?= htmlspecialchars($_SESSION['booking_conflict']['service']) ?></strong>
                    </div>
                    <div class="conflict-row">
                        <span>Дата и время:</span>
                        <strong><?= $_SESSION['booking_conflict']['date'] ?> в <?= $_SESSION['booking_conflict']['time'] ?></strong>
                    </div>
                </div>

                <button type="button" class="conflict-btn-close" onclick="document.getElementById('conflictModal').classList.remove('active')">Понятно</button>
            </div>
        </div>
        <?php unset($_SESSION['booking_conflict']); ?>
    <?php endif; ?>

    <!-- МОДАЛЬНОЕ ОКНО ДЛЯ ГОСТЕЙ -->
    <?php if (!$currentUser): ?>
        <div class="guest-modal" id="guestModal">
            <div class="guest-modal__overlay" id="guestModalOverlay"></div>
            <div class="guest-modal__content">
                <button class="guest-modal__close" id="guestModalClose"><i class="bi bi-x-lg"></i></button>

                <div class="guest-modal__icon">👋</div>
                <h3 class="guest-modal__title">Вы не авторизованы</h3>
                <p class="guest-modal__text">
                    Войдите в аккаунт, чтобы мы могли автоматически подставлять ваши данные и скрывать время, на которое вы уже записаны.
                    Или продолжите оформление как гость.
                </p>

                <div class="guest-modal__actions">
                    <a href="/login/" class="btn-guest-login">Войти</a>
                    <a href="/register/" class="btn-guest-register">Регистрация</a>
                    <button type="button" class="btn-guest-continue" id="guestContinueBtn">Продолжить как гость</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="/components/booking-form/script.js"></script>
    <?php
    return ob_get_clean();
}