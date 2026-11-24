<?php
$certificates = $db->query("
    SELECT c.*, u.name as user_name 
    FROM certificates c 
    LEFT JOIN users u ON c.user_id = u.id 
    ORDER BY c.id DESC
")->fetchAll();

$editId = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
$editCert = null;

if ($editId > 0) {
    $stmt = $db->prepare("SELECT * FROM certificates WHERE id = ?");
    $stmt->execute([$editId]);
    $editCert = $stmt->fetch();
}

require_once __DIR__ . '/../php/render_certificates.php';
echo renderAdminCertificates($certificates, $editCert);
?>