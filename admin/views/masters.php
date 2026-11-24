<?php
/**
 * Контроллер вкладки "Мастера"
 * Файл: admin/views/masters.php
 */

$masters = $db->query("SELECT * FROM masters ORDER BY is_active DESC, name ASC")->fetchAll();
$allServices = $db->query("SELECT * FROM services ORDER BY category, name")->fetchAll();
$allUsers = $db->query("SELECT id, name, role FROM users WHERE role IN ('master', 'client') ORDER BY name ASC")->fetchAll();

$editId = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
$editMaster = null;
$editSkills = [];

if ($editId > 0) {
    $stmt = $db->prepare("SELECT * FROM masters WHERE id = ?");
    $stmt->execute([$editId]);
    $editMaster = $stmt->fetch();

    $stmtSkills = $db->prepare("SELECT service_id FROM master_skills WHERE master_id = ?");
    $stmtSkills->execute([$editId]);
    $editSkills = $stmtSkills->fetchAll(PDO::FETCH_COLUMN);
}

// Подключаем рендер
require_once __DIR__ . '/../php/render_masters.php';
echo renderAdminMasters($masters, $allServices, $allUsers, $editMaster, $editSkills);

// ВОЗВРАЩАЕМ СКРИПТ:
echo '<script src="/admin/js/masters.js"></script>';
?>