<?php
require_once __DIR__ . '/../src/init.php';

// Пускаем только авторизованных
if (!isset($_SESSION['user_id'])) {
    header('Location: /login/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    $text = trim($_POST['text']);
    $userId = $_SESSION['user_id'];

    // Валидация
    if ($rating < 1 || $rating > 5) $rating = 5;

    if (!empty($text)) {
        $stmt = $db->prepare("INSERT INTO reviews (user_id, rating, text) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $rating, $text]);
    }
}

// Возвращаем обратно к отзывам
header('Location: /#reviews');
exit;