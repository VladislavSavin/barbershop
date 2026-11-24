<?php
/**
 * Рендер вкладки "Пользователи"
 * Файл: admin/php/render_users.php
 */
function renderAdminUsers($users) {
    ob_start();
    ?>
    <h2 class="admin-title">Зарегистрированные пользователи</h2>
    <table class="admin-table">
        <thead>
        <tr><th>ID</th><th>Имя</th><th>Телефон</th><th>Email</th><th>Роль</th><th>Дата рег.</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td>#<?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['phone']) ?></td>
                <td><?= htmlspecialchars($u['email'] ?? '—') ?></td>
                <td>
                    <?php if ($u['role'] === 'admin'): ?>
                        <span class="role-admin">Админ</span>
                    <?php elseif ($u['role'] === 'master'): ?>
                        <span class="role-master">Мастер</span>
                    <?php else: ?>
                        Клиент
                    <?php endif; ?>
                </td>
                <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}