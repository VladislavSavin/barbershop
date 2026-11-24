<?php
/**
 * Рендер вкладки "Услуги"
 * Файл: admin/php/render_services.php
 */
function renderAdminServices($services, $editService) {
    ob_start();
    ?>
    <h2 class="admin-title"><?= $editService ? 'Редактирование услуги' : 'Управление услугами' ?></h2>

    <form method="POST" action="/actions/admin_actions.php" class="admin-form-row admin-form-wrap">
        <?php if ($editService): ?>
            <input type="hidden" name="action" value="edit_service">
            <input type="hidden" name="service_id" value="<?= $editService['id'] ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="add_service">
        <?php endif; ?>
        <input type="hidden" name="return_tab" value="services">

        <div class="admin-form-group flex-1">
            <label>Категория</label>
            <input type="text" name="category" placeholder="Например: Мужские стрижки" required value="<?= $editService ? htmlspecialchars($editService['category']) : '' ?>">
        </div>

        <div class="admin-form-group flex-2">
            <label>Название услуги</label>
            <input type="text" name="name" placeholder="Классическая стрижка" required value="<?= $editService ? htmlspecialchars($editService['name']) : '' ?>">
        </div>

        <div class="admin-form-group flex-1">
            <label>Цена (₽)</label>
            <input type="number" name="price" placeholder="1500" required value="<?= $editService ? $editService['price'] : '' ?>">
        </div>

        <div class="admin-form-group flex-1">
            <label>Длительность (мин)</label>
            <input type="number" name="duration" placeholder="45" required value="<?= $editService ? $editService['duration'] : '' ?>">
        </div>

        <div class="admin-form-group flex-1">
            <label>Тип цены</label>
            <select name="type">
                <option value="fixed" <?= $editService && $editService['type'] === 'fixed' ? 'selected' : '' ?>>Фиксированная</option>
                <option value="area" <?= $editService && $editService['type'] === 'area' ? 'selected' : '' ?>>За см² (тату)</option>
            </select>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn-admin btn-save">
                <?= $editService ? 'Сохранить' : 'Добавить' ?>
            </button>
            <?php if ($editService): ?>
                <a href="?tab=services" class="btn-admin btn-toggle admin-btn-link">Отмена</a>
            <?php endif; ?>
        </div>
    </form>

    <table class="admin-table">
        <thead>
        <tr><th>Услуга</th><th>Длительность</th><th>Цена</th><th>Статус</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php foreach ($services as $s): ?>
            <tr class="<?= !$s['is_active'] ? 'is-inactive' : '' ?>">
                <td>
                    <strong><?= htmlspecialchars($s['name']) ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($s['category']) ?></small>
                </td>
                <td>⏱ <?= $s['duration'] ?> мин</td>
                <td><?= number_format($s['price'], 0, '', ' ') ?> ₽ <?= $s['type'] == 'area' ? '/см²' : '' ?></td>
                <td>
                    <?= $s['is_active'] ? '<span class="status-active">Активна</span>' : '<span class="status-inactive">Отключена</span>' ?>
                </td>
                <td>
                    <div class="admin-actions-wrap">
                        <a href="?tab=services&edit_id=<?= $s['id'] ?>" class="btn-admin btn-toggle admin-btn-link">Ред.</a>

                        <form method="POST" action="/actions/admin_actions.php">
                            <input type="hidden" name="action" value="toggle_service">
                            <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                            <input type="hidden" name="return_tab" value="services">
                            <button type="submit" class="btn-admin btn-toggle"><?= $s['is_active'] ? 'Отключить' : 'Включить' ?></button>
                        </form>

                        <form method="POST" action="/actions/admin_actions.php" onsubmit="return confirm('Удалить услугу навсегда?');">
                            <input type="hidden" name="action" value="delete_service">
                            <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                            <input type="hidden" name="return_tab" value="services">
                            <button type="submit" class="btn-admin btn-delete">Удалить</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}