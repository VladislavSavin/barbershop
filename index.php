<?php
// Инициализация (БД, сессии)
require_once __DIR__ . '/src/init.php';

// Подключаем шапку
require_once __DIR__ . '/template/header.php';

// --- ВЫВОДИМ БАННЕР ---
echo '<link rel="stylesheet" href="/template/components/banner/style.css">';
require_once __DIR__ . '/components/banner/component.php';

echo renderHeroBanner([
    'badge'    => 'Барбершоп & Тату-студия',
    'title'    => 'Стиль начинается здесь',
    'subtitle' => 'Мужские и женские стрижки, тату, пирсинг и уход за бородой. Ваш идеальный образ ждет вас.',
    'btn_text' => 'Записаться онлайн',
    'btn_link' => '/booking/',
    'bg_image' => '/assets/images/banner-photo1.png'
]);

// --- ВЫВОДИМ УСЛУГИ ---
echo '<link rel="stylesheet" href="/template/components/list-services/style.css">';
require_once __DIR__ . '/components/list-services/component.php';
echo renderServicesList($db, 4);

// --- ВЫВОДИМ ПРЕИМУЩЕСТВА ---
echo '<link rel="stylesheet" href="/template/components/features/style.css">';
require_once __DIR__ . '/components/features/component.php';
echo renderFeatures();

// --- ВЫВОДИМ СЛАЙДЕР РАБОТ ---
// 1. Делаем запрос к базе прямо здесь (в контроллере)
$stmtSlider = $db->query("SELECT * FROM portfolio ORDER BY id ASC LIMIT 8");
$sliderWorks = $stmtSlider->fetchAll();
// 2. Подключаем стили
echo '<link rel="stylesheet" href="/template/components/slider/style.css">';
// 3. Подключаем компонент и передаем ему данные
require_once __DIR__ . '/components/slider/component.php';
echo renderSlider($sliderWorks);

// --- ВЫВОДИМ ОТЗЫВЫ ---
echo '<link rel="stylesheet" href="/template/components/reviews/style.css">';
require_once __DIR__ . '/components/reviews/component.php';
echo renderReviews($db);

// Подключаем подвал
require_once __DIR__ . '/template/footer.php';
//Добавить отзывы и события
?>

