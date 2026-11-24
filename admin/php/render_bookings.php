<?php
/**
 * Рендер вкладки "Записи"
 * Файл: admin/php/render_bookings.php
 */

function renderAdminBookings($bookings) {
    ob_start();
    ?>
    <h2 class="admin-title">Управление записями</h2>

    <!-- ТАБЫ ДЛЯ ФИЛЬТРАЦИИ -->
    <div class="admin-tabs">
        <button class="admin-tab-btn active" data-filter="all">Все</button>
        <button class="admin-tab-btn" data-filter="pending">Ожидают</button>
        <button class="admin-tab-btn" data-filter="confirmed">Подтверждены</button>
        <button class="admin-tab-btn" data-filter="completed">Завершены</button>
        <button class="admin-tab-btn" data-filter="cancelled">Отменены</button>
    </div>

    <div class="admin-bookings-list">
        <?php if (empty($bookings)): ?>
            <p class="empty-text">Записей пока нет.</p>
        <?php else: ?>
            <?php foreach ($bookings as $b): ?>
                <div class="admin-booking-card <?= in_array($b['status'], ['completed', 'cancelled']) ? 'is-archived' : '' ?>" data-status="<?= $b['status'] ?>">

                    <!-- ЛЕВАЯ ЧАСТЬ: Дата и Время -->
                    <div class="ab-time">
                        <span class="ab-date"><?= date('d.m.Y', strtotime($b['booking_date'])) ?></span>
                        <span class="ab-clock"><?= substr($b['start_time'], 0, 5) ?> — <?= substr($b['end_time'], 0, 5) ?></span>
                    </div>

                    <!-- ЦЕНТР: Клиент и Услуга -->
                    <div class="ab-info">
                        <div class="ab-client">
                            <strong><?= htmlspecialchars($b['final_client_name']) ?></strong>
                            <a href="tel:<?= htmlspecialchars($b['final_client_phone']) ?>" class="ab-phone"><?= htmlspecialchars($b['final_client_phone']) ?></a>
                        </div>
                        <div class="ab-service">
                            <span class="ab-service-name"><?= htmlspecialchars($b['service_name']) ?></span>
                            <span class="ab-price"><?= number_format($b['total_price'], 0, '', ' ') ?> ₽</span>
                        </div>

                        <!-- ОТОБРАЖЕНИЕ ОПЛАТЫ СЕРТИФИКАТОМ -->
                        <?php if ($b['cert_paid'] > 0): ?>
                            <div class="ab-cert-info">
                                <?php if ($b['cert_paid'] >= $b['total_price']): ?>
                                    <span class="cert-full"><i class="bi bi-gift"></i> Полностью оплачено сертификатом</span>
                                <?php else: ?>
                                    <span class="cert-partial"><i class="bi bi-gift"></i> Частично оплачено (<?= number_format($b['cert_paid'], 0, '', ' ') ?> ₽)</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ПРАВАЯ ЧАСТЬ: Мастер и Статус -->
                    <div class="ab-master-status">
                        <div class="ab-master">
                            <?php if ($b['master_avatar']): ?>
                                <img src="<?= htmlspecialchars($b['master_avatar']) ?>" alt="Мастер" class="ab-master-avatar">
                            <?php else: ?>
                                <div class="ab-master-placeholder"><?= mb_substr($b['master_name'], 0, 1) ?></div>
                            <?php endif; ?>
                            <div class="ab-master-info">
                                <span class="ab-master-badge">Мастер</span>
                                <span class="ab-master-name"><?= htmlspecialchars($b['master_name']) ?></span>
                            </div>
                        </div>

                        <div class="ab-actions">
                            <form method="POST" action="/actions/admin_actions.php">
                                <input type="hidden" name="action" value="update_booking_status">
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                <input type="hidden" name="return_tab" value="bookings">

                                <select name="status" onchange="this.form.submit()" class="ab-status-select status-<?= $b['status'] ?>">
                                    <option value="pending" <?= $b['status'] == 'pending' ? 'selected' : '' ?>>Ожидает</option>
                                    <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Подтверждена</option>
                                    <option value="completed" <?= $b['status'] == 'completed' ? 'selected' : '' ?>>Завершена</option>
                                    <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Отменена</option>
                                </select>
                            </form>

                            <?php if (in_array($b['status'], ['completed', 'cancelled'])): ?>
                                <form method="POST" action="/actions/admin_actions.php" onsubmit="return confirm('Удалить запись навсегда?');">
                                    <input type="hidden" name="action" value="delete_booking">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <input type="hidden" name="return_tab" value="bookings">
                                    <button type="submit" class="ab-btn-delete" title="Удалить"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}