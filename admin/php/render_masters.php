<?php
/**
 * Рендер вкладки "Мастера"
 * Файл: admin/php/render_masters.php
 */

function renderAdminMasters($masters, $allServices, $allUsers, $editMaster, $editSkills) {
    ob_start();
    ?>
    <h2 class="admin-title"><?= $editMaster ? 'Редактирование мастера' : 'Управление персоналом' ?></h2>

    <form method="POST" action="/actions/admin_actions.php" class="admin-form-row admin-form-wrap" enctype="multipart/form-data" style="flex-direction: column; align-items: stretch;" onsubmit="return disableSubmitBtn(this);">

        <?php if ($editMaster): ?>
            <input type="hidden" name="action" value="edit_master">
            <input type="hidden" name="master_id" value="<?= $editMaster['id'] ?>">
        <?php else: ?>
            <input type="hidden" name="action" value="add_master">
        <?php endif; ?>
        <input type="hidden" name="return_tab" value="masters">

        <div class="master-form-top">
            <div class="admin-form-group flex-1">
                <input type="text" name="name" placeholder="Имя мастера" required value="<?= $editMaster ? htmlspecialchars($editMaster['name']) : '' ?>">
            </div>

            <div class="admin-form-group flex-1">
                <input type="text" name="specialization" placeholder="Специализация" required value="<?= $editMaster ? htmlspecialchars($editMaster['specialization']) : '' ?>">
            </div>

            <div class="admin-form-group flex-1">
                <select name="user_id">
                    <option value="">-- Без аккаунта --</option>
                    <?php foreach ($allUsers as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($editMaster && $editMaster['user_id'] == $u['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['name']) ?> (<?= $u['role'] === 'master' ? 'Мастер' : 'Клиент' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!$editMaster): ?>
                <div class="custom-file-wrapper">
                    <input type="file" name="avatar" id="avatarUpload" accept="image/jpeg, image/png, image/webp">
                    <label for="avatarUpload" class="custom-file-btn">
                        <i class="bi bi-camera"></i> Фото
                    </label>
                    <span class="custom-file-name" id="fileNameDisplay">Не выбран</span>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 2rem; position: relative;">
            <label style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Какие услуги оказывает мастер:</label>

            <div class="custom-dropdown-trigger" id="servicesTrigger">
                <span id="servicesTriggerText">Выберите услуги...</span>
                <span class="toggle-icon">▼</span>
            </div>

            <div class="custom-dropdown-menu" id="servicesMenu">
                <div class="services-grouped-container">
                    <?php
                    $groupedServices = [];
                    foreach ($allServices as $s) {
                        $groupedServices[$s['category']][] = $s;
                    }

                    foreach ($groupedServices as $category => $services):
                        ?>
                        <div class="service-category-block">
                            <h4 class="service-category-title"><?= htmlspecialchars($category) ?></h4>
                            <div class="services-checkbox-grid">
                                <?php foreach ($services as $s): ?>
                                    <label class="custom-checkbox-label">
                                        <input type="checkbox" name="services[]" value="<?= $s['id'] ?>" class="service-checkbox"
                                            <?= in_array($s['id'], $editSkills) ? 'checked' : '' ?>>
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">
                                            <?= htmlspecialchars($s['name']) ?>
                                            <?php if (!$s['is_active']): ?>
                                                <small style="color: #ff4444;">(отключена)</small>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn-admin btn-save" id="submitBtn">
                <?= $editMaster ? 'Сохранить изменения' : 'Добавить мастера' ?>
            </button>

            <?php if ($editMaster): ?>
                <a href="?tab=masters" class="btn-admin btn-toggle admin-btn-link">Отмена</a>
            <?php endif; ?>
        </div>
    </form>

    <table class="admin-table">
        <thead>
        <tr>
            <th>Фото</th>
            <th>Имя</th>
            <th>Специализация</th>
            <th>Действие</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($masters as $m): ?>
            <tr>
                <td>
                    <?php if (!empty($m['avatar'])): ?>
                        <img src="<?= htmlspecialchars($m['avatar']) ?>" alt="Фото" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-color);">
                    <?php else: ?>
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; color: #777;">Нет</div>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><?= htmlspecialchars($m['specialization']) ?></td>
                <td>
                    <div class="admin-actions-wrap">
                        <a href="?tab=masters&edit_id=<?= $m['id'] ?>" class="btn-admin btn-toggle admin-btn-link">Ред.</a>

                        <form method="POST" action="/actions/admin_actions.php" onsubmit="return confirm('Точно уволить мастера?');" style="margin: 0;">
                            <input type="hidden" name="action" value="delete_master">
                            <input type="hidden" name="master_id" value="<?= $m['id'] ?>">
                            <input type="hidden" name="return_tab" value="masters">
                            <button type="submit" class="btn-admin btn-delete">Уволить</button>
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