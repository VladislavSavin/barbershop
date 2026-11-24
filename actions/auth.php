<?php
/**
 * Обработчик авторизации и регистрации
 * Файл: actions/auth.php
 */
require_once __DIR__ . '/../src/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$action = $_POST['action'] ?? '';
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$name = trim($_POST['name'] ?? '');

// ВАЛИДАЦИЯ ТЕЛЕФОНА
require_once __DIR__ . '/../src/validators.php';
$normalizedPhone = normalizePhone($phone);

if (!$normalizedPhone) {
    $_SESSION['error'] = 'Введите корректный номер телефона (например, +79001234567).';
    // Возвращаем на нужную страницу
    header('Location: /' . ($action === 'register' ? 'register' : 'login') . '/');
    exit;
}
$phone = $normalizedPhone; // Используем нормализованный во всех запросах

if (empty($phone) || empty($password)) {
    $_SESSION['error'] = 'Заполните телефон и пароль.';
    header('Location: /login/'); // или /register/ в зависимости от action
    exit;
}

// --- РЕГИСТРАЦИЯ ---
if ($action === 'register') {
    if (empty($name)) {
        $_SESSION['error'] = 'Введите ваше имя.';
        header('Location: /register/');
        exit;
    }

    if (mb_strlen($password) < 8) {
        $_SESSION['error'] = 'Пароль должен содержать минимум 8 символов.';
        header('Location: /register/');
        exit;
    }

    $email = trim($_POST['email'] ?? '');

    // Email ОПЦИОНАЛЬНЫЙ
    if (!empty($email)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Введите корректный email или оставьте поле пустым.';
            header('Location: /register/');
            exit;
        }
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Пользователь с таким email уже существует.';
            header('Location: /register/');
            exit;
        }
    } else {
        $email = null; // важно для UNIQUE индекса
    }

    // Проверка телефона
    $stmt = $db->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Пользователь с таким номером уже существует.';
        header('Location: /register/');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, phone, email, password_hash, role) VALUES (?, ?, ?, ?, 'client')");
    $stmt->execute([$name, $phone, $email, $hash]);

    $userId = $db->lastInsertId();

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = 'client';

    header('Location: /profile/');
    exit;
}

// --- ВХОД ---
if ($action === 'login') {
    $stmt = $db->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Записываем данные в сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // Перенаправляем в зависимости от роли
        if ($user['role'] === 'admin') {
            header('Location: /admin/');
        } elseif ($user['role'] === 'master') {
            header('Location: /master/'); // Новый кабинет для мастеров
        } else {
            header('Location: /profile/');
        }
        exit;
    } else {
        $_SESSION['error'] = 'Неверный телефон или пароль.';
        header('Location: /login/');
        exit;
    }
}