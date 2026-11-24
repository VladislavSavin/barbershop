<?php
/**
 * Функции отрисовки карточек для кабинета мастера
 * Файл: master/php/render.php
 */

/**
 * Карточка записи, где мастер — ИСПОЛНИТЕЛЬ
 *
 * @param array $b Данные записи
 * @param bool $isToday Сегодняшняя ли запись (выделяется зеленой полосой)
 * @param bool $isHistory Архивная запись (приглушенная)
 */
function renderBookingCard($b, $isToday = false, $isHistory = false) {
    $dateObj = new DateTime($b['booking_date']);
    $months = ['', 'янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];
    $dateStr = $dateObj->format('d') . ' ' . $months[(int)$dateObj->format('n')];

    $cardClass = $isHistory ? 'is-history' : ($isToday ? 'is-today' : '');

    ob_start();
    ?>
    <div class="master-booking-card <?= $cardClass ?>">
        <div class="booking-time">
            <span class="time-start"><?= substr($b['start_time'], 0, 5) ?></span>
            <span class="time-end"><?= substr($b['end_time'], 0, 5) ?></span>
            <?php if (!$isToday && !$isHistory): ?>
                <span class="booking-date"><?= $dateStr ?></span>
            <?php endif; ?>
        </div>

        <div class="booking-details">
            <h3><?= htmlspecialchars($b['service_name']) ?></h3>
            <p class="client-name"><?= htmlspecialchars($b['final_client_name']) ?></p>
            <a href="tel:<?= htmlspecialchars($b['final_client_phone']) ?>" class="client-phone">
                <?= htmlspecialchars($b['final_client_phone']) ?>
            </a>
        </div>

        <div class="booking-status">
            <?php if ($b['status'] === 'cancelled'): ?>
                <span class="status-badge cancelled">Отменена</span>
            <?php elseif ($b['status'] === 'completed'): ?>
                <span class="status-badge completed">Завершена</span>
            <?php else: ?>
                <form method="POST" class="status-form">
                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                    <select name="status" onchange="this.form.submit()">
                        <option value="pending" <?= $b['status'] == 'pending' ? 'selected' : '' ?>>Ожидает</option>
                        <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Подтверждена</option>
                        <option value="completed" <?= $b['status'] == 'completed' ? 'selected' : '' ?>>Завершена</option>
                        <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Отменена</option>
                    </select>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Карточка визита, где мастер — КЛИЕНТ (записан к коллеге)
 *
 * @param array $v Данные визита
 * @param bool $isHistory Архивный визит (приглушенный)
 */
function renderMyVisitCard($v, $isHistory = false) {
    $dateObj = new DateTime($v['booking_date']);
    $months = ['', 'янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];
    $dateStr = $dateObj->format('d') . ' ' . $months[(int)$dateObj->format('n')];

    $statusText = [
        'pending' => 'Ожидает',
        'confirmed' => 'Подтверждена',
        'completed' => 'Завершена',
        'cancelled' => 'Отменена'
    ];

    ob_start();
    ?>
    <div class="master-booking-card <?= $isHistory ? 'is-history' : 'is-today' ?>">
        <div class="booking-time">
            <span class="time-start"><?= substr($v['start_time'], 0, 5) ?></span>
            <span class="time-end"><?= substr($v['end_time'], 0, 5) ?></span>
            <span class="booking-date"><?= $dateStr ?></span>
        </div>

        <div class="booking-details">
            <h3><?= htmlspecialchars($v['service_name']) ?></h3>
            <p class="client-name">Мастер: <?= htmlspecialchars($v['master_name']) ?></p>
        </div>

        <div class="booking-status">
            <span class="status-badge <?= $v['status'] ?>">
                <?= $statusText[$v['status']] ?>
            </span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}