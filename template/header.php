<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbershop & Tattoo</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/template/style.css">
    <link rel="stylesheet" href="/template/burger_menu.css">
</head>
<body>

<header class="site-header">
    <div class="container header-container">

        <!-- ЛЕВЫЙ БЛОК: Логотип -->
        <div class="header-left">
            <a href="/" class="site-logo">Barbershop & Tattoo</a>
        </div>

        <!-- ЦЕНТРАЛЬНЫЙ БЛОК: Навигация (десктоп) -->
        <nav class="header-center">
            <a href="/services/">Услуги</a>
            <a href="/about/">О нас</a>
            <a href="/certificates/">Сертификаты</a>
        </nav>

        <!-- ПРАВЫЙ БЛОК: Кнопки авторизации (десктоп) -->
        <div class="header-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-nav">
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin/" class="nav-btn nav-btn--admin">Админка</a>
                    <?php elseif ($_SESSION['user_role'] === 'master'): ?>
                        <a href="/master/" class="nav-btn">Кабинет мастера</a>
                    <?php else: ?>
                        <a href="/profile/" class="nav-btn">Мой профиль</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <a href="/login/" class="nav-btn">Войти</a>
            <?php endif; ?>
        </div>

        <!-- БУРГЕР-КНОПКА (только для мобильных) -->
        <button class="burger-btn" id="burgerBtn" aria-label="Открыть меню">
            <i class="bi bi-list"></i>
        </button>

    </div>
</header>

<!-- ПОЛНОЭКРАННОЕ МОБИЛЬНОЕ МЕНЮ -->
<div class="fullscreen-menu" id="mobileMenu">
    <div class="fullscreen-menu-header">
        <div class="logo">
            <span>BARBERSHOP</span>
            <span style="color: var(--accent-color);">•</span>
            <span>TATTOO</span>
        </div>
        <button class="close-btn" id="closeBtn" aria-label="Закрыть меню">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="fullscreen-menu-content">
        <nav class="mobile-nav">
            <a href="/services/" class="mobile-nav-link">Услуги</a>
            <a href="/about/" class="mobile-nav-link">О нас</a>
            <a href="/certificates/" class="mobile-nav-link">Сертификаты</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="/admin/" class="mobile-nav-link mobile-nav-accent">Админка</a>
                <?php elseif ($_SESSION['user_role'] === 'master'): ?>
                    <a href="/master/" class="mobile-nav-link mobile-nav-accent">Кабинет мастера</a>
                <?php else: ?>
                    <a href="/profile/" class="mobile-nav-link mobile-nav-accent">Мой профиль</a>
                <?php endif; ?>
                <a href="/actions/logout.php" class="mobile-nav-link mobile-nav-muted">Выйти</a>
            <?php else: ?>
                <a href="/login/" class="mobile-nav-link mobile-nav-accent">Войти</a>
            <?php endif; ?>
        </nav>
    </div>
</div>

<!-- Подключаем скрипт бургер-меню -->
<script src="/template/burger_menu.js"></script>

<div class="site-main">
    <div class="container">
        <!-- Контент страницы начинается здесь -->