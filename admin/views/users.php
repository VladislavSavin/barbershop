<?php
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/../php/render_users.php';
echo renderAdminUsers($users);
?>