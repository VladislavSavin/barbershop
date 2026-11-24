<?php
/**
 * Помощники для авторизации
 * Файл: src/auth_helpers.php
 */

/**
 * Защищает страницу и пускает только пользователей с определенной ролью.
 * Если роль не совпадает, перенаправляет в "родной" кабинет.
 *
 * @param string $requiredRole Роль, которой разрешен доступ (admin, master, client)
 */
function protectPage($requiredRole) {
    // 1. Если не авторизован — на страницу входа
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login/');
        exit;
    }

    $currentRole = $_SESSION['user_role'];

    // 2. Если роль совпадает — пускаем (ничего не делаем)
    if ($currentRole === $requiredRole) {
        return;
    }

    // 3. Если роль не совпадает — отправляем туда, где ему место
    if ($currentRole === 'admin') {
        header('Location: /admin/');
    } elseif ($currentRole === 'master') {
        header('Location: /master/');
    } else {
        header('Location: /profile/');
    }
    exit;
}