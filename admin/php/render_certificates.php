<?php
/**
 * Рендер вкладки "Сертификаты"
 * Файл: admin/php/render_certificates.php
 */
function renderAdminCertificates($certificates, $editCert) {
    ob_start();
    ?>
    <h2 class="admin-title"><?= $editCert ? 'Редактирование сертификата' : 'Управление сертификатами' ?></h2>

    <form method="POST" action="/actions/admin_actions.php" class="admin-form-row admin-form-wrap">
        <?php if ($editCert): ?>
            <input type="hidden" name="action" value="edit_certificate">
            <input type="hidden" name="cert_id" value="<?= $editCert['id'] ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="add_certificate">
        <?php endif; ?>
        <input type="hidden" name="return_tab" value="certificates">

        <div class="admin-form-group flex-2">
            <label>Название</label>
            <input type="text" name="title" placeholder="Подарочный сертификат..." required value="<?= $editCert ? htmlspecialchars($editCert['title']) : '' ?>">
        </div>

        <div class="admin-form-group flex-1">
            <label>Номинал (₽)</label>
            <input type="number" name="price" placeholder="5000" required value="<?= $editCert ? $editCert['price'] : '' ?>">
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn-admin btn-save">
                <?= $editCert ? 'Сохранить' : 'Сгенерировать код' ?>
            </button>
            <?php if ($editCert): ?>
                <a href="?tab=certificates" class="btn-admin btn-toggle admin-btn-link">Отмена</a>
            <?php endif; ?>
        </div>
    </form>

    <table class="admin-table">
        <thead>
        <tr><th>Код</th><th>Название</th><th>Номинал</th><th>Статус / Владелец</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php foreach ($certificates as $c): ?>
            <tr>
                <td><code class="admin-code"><?= htmlspecialchars($c['code']) ?></code></td>
                <td><?= htmlspecialchars($c['title']) ?></td>
                <td><strong><?= number_format($c['price'], 0, '', ' ') ?> ₽</strong></td>
                <td>
                    <?php if ($c['status'] === 'active'): ?>
                        <?php if ($c['user_id']): ?>
                            <span class="status-warning">У пользователя (<?= htmlspecialchars($c['user_name']) ?>)</span><br>
                            <small class="text-muted">Баланс: <?= number_format($c['balance'], 0, '', ' ') ?> ₽</small>
                        <?php else: ?>
                            <span class="status-active">Свободен</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="status-inactive">Погашен</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="admin-actions-wrap">
                        <a href="?tab=certificates&edit_id=<?= $c['id'] ?>" class="btn-admin btn-toggle admin-btn-link">Ред.</a>
                        <form method="POST" action="/actions/admin_actions.php" onsubmit="return confirm('Удалить сертификат?');">
                            <input type="hidden" name="action" value="delete_certificate">
                            <input type="hidden" name="cert_id" value="<?= $c['id'] ?>">
                            <input type="hidden" name="return_tab" value="certificates">
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