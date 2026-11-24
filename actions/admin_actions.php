<?php
/**
 * Обработчик действий администратора
 * Файл: actions/admin_actions.php
 */
require_once __DIR__ . '/../src/init.php';

// Проверка на админа
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Доступ запрещен");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // 1. Изменение статуса записи
    if ($action === 'update_booking_status') {
        $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], (int)$_POST['booking_id']]);
    }

    // 2. Удаление записи
    if ($action === 'delete_booking') {
        $stmt = $db->prepare("DELETE FROM bookings WHERE id = ? AND status IN ('completed', 'cancelled')");
        $stmt->execute([(int)$_POST['booking_id']]);
    }

    // 3. Добавление мастера
    if ($action === 'add_master') {
        $name = trim($_POST['name']);
        $specialization = trim($_POST['specialization']);
        $userId = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $avatarPath = null;

        // Загрузка фото
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $fileName = $_FILES['avatar']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = __DIR__ . '/../assets/images/masters/';
                if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0777, true);

                if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                    $avatarPath = '/assets/images/masters/' . $newFileName;
                }
            }
        }

        // Сохраняем мастера
        $stmt = $db->prepare("INSERT INTO masters (name, specialization, avatar, user_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $specialization, $avatarPath, $userId]);
        $newMasterId = $db->lastInsertId();

        // Сохраняем выбранные услуги
        if (isset($_POST['services']) && is_array($_POST['services'])) {
            $stmtSkill = $db->prepare("INSERT INTO master_skills (master_id, service_id) VALUES (?, ?)");
            foreach ($_POST['services'] as $serviceId) {
                $stmtSkill->execute([$newMasterId, (int)$serviceId]);
            }
        }
    }

    // 4. Редактирование мастера
    if ($action === 'edit_master') {
        $masterId = (int)$_POST['master_id'];
        $name = trim($_POST['name']);
        $specialization = trim($_POST['specialization']);
        $userId = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;

        // Обновляем основные данные
        $stmt = $db->prepare("UPDATE masters SET name = ?, specialization = ?, user_id = ? WHERE id = ?");
        $stmt->execute([$name, $specialization, $userId, $masterId]);

        // Обновляем услуги (удаляем старые, вставляем новые)
        $db->prepare("DELETE FROM master_skills WHERE master_id = ?")->execute([$masterId]);

        if (isset($_POST['services']) && is_array($_POST['services'])) {
            $stmtSkill = $db->prepare("INSERT INTO master_skills (master_id, service_id) VALUES (?, ?)");
            foreach ($_POST['services'] as $serviceId) {
                $stmtSkill->execute([$masterId, (int)$serviceId]);
            }
        }
    }

    // 5. Удаление мастера
    if ($action === 'delete_master') {
        $stmt = $db->prepare("DELETE FROM masters WHERE id = ?");
        $stmt->execute([(int)$_POST['master_id']]);
    }

    // 6. Добавление услуги
    if ($action === 'add_service') {
        $stmt = $db->prepare("INSERT INTO services (category, name, price, duration, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['category'], $_POST['name'], $_POST['price'], $_POST['duration'], $_POST['type']]);
    }

    // 7. Редактирование услуги
    if ($action === 'edit_service') {
        $id = (int)$_POST['service_id'];
        $stmt = $db->prepare("UPDATE services SET category = ?, name = ?, price = ?, duration = ?, type = ? WHERE id = ?");
        $stmt->execute([$_POST['category'], $_POST['name'], $_POST['price'], $_POST['duration'], $_POST['type'], $id]);
    }

    // 8. Отключение/Включение услуги
    if ($action === 'toggle_service') {
        $id = (int)$_POST['service_id'];
        $db->query("UPDATE services SET is_active = NOT is_active WHERE id = $id");
    }

    // 9. Удаление услуги
    if ($action === 'delete_service') {
        $stmt = $db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([(int)$_POST['service_id']]);
    }

    // 10. Добавление сертификата (с автогенерацией кода)
    if ($action === 'add_certificate') {
        $title = trim($_POST['title']);
        $price = (float)$_POST['price'];

        $code = 'GIFT-' . strtoupper(substr(md5(uniqid()), 0, 8));

        $stmt = $db->prepare("INSERT INTO certificates (title, price, code, balance) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $price, $code, $price]);
    }

    // 11. Редактирование сертификата
    if ($action === 'edit_certificate') {
        $id = (int)$_POST['cert_id'];
        $title = trim($_POST['title']);
        $price = (float)$_POST['price'];

        $stmt = $db->prepare("UPDATE certificates SET title = ?, price = ? WHERE id = ?");
        $stmt->execute([$title, $price, $id]);
    }

    // 12. Удаление сертификата
    if ($action === 'delete_certificate') {
        $id = (int)$_POST['cert_id'];
        $db->prepare("DELETE FROM certificates WHERE id = ?")->execute([$id]);
    }
}

// Возвращаем обратно на ту вкладку, откуда пришел запрос
$tab = $_POST['return_tab'] ?? 'bookings';
header("Location: /admin/?tab=" . $tab);
exit;