<?php
$services = $db->query("SELECT * FROM services ORDER BY category, name")->fetchAll();
$editId = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
$editService = null;

if ($editId > 0) {
    $stmt = $db->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$editId]);
    $editService = $stmt->fetch();
}

require_once __DIR__ . '/../php/render_services.php';
echo renderAdminServices($services, $editService);
?>