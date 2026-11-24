<?php
require_once __DIR__ . '/../src/init.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /profile/');
    exit;
}

$email = trim($_POST['email'] ?? '');
$userId = $_SESSION['user_id'];

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['profile_error'] = 'Введите корректный email.';
    header('Location: /profile/');
    exit;
}

$stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->execute([$email, $userId]);
if ($stmt->fetch()) {
    $_SESSION['profile_error'] = 'Этот email уже используется другим пользователем.';
    header('Location: /profile/');
    exit;
}

$stmt = $db->prepare("UPDATE users SET email = ? WHERE id = ?");
$stmt->execute([$email, $userId]);

$_SESSION['profile_success'] = 'Email успешно сохранен!';
header('Location: /profile/');
exit;