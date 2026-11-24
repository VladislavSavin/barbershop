<?php
require_once __DIR__ . '/../src/init.php';

$pageTitle = 'О нас — Barbershop & Tattoo';
require_once __DIR__ . '/../template/header.php';

// --- ДАННЫЕ ДЛЯ КОМПОНЕНТОВ ---

// 1. Данные для вступления
$introData = [
    'title' => 'Больше, чем просто барбершоп',
    'text' => 'Мы создали пространство, где каждый мужчина может расслабиться, выпить чашечку кофе и доверить свою внешность настоящим профессионалам. Мы не просто стрижем — мы создаем стиль, который подчеркивает вашу индивидуальность.'
];

// 2. Данные для блока инструментов
$toolsData = [
    [
        'icon' => 'bi-tools',
        'title' => 'Профессиональные инструменты',
        'text' => 'Машинки Wahl, ножницы Kasho и опасные бритвы. Мы используем только премиальный инструмент, который проходит тщательную стерилизацию после каждого клиента.'
    ],
    [
        'icon' => 'bi-droplet',
        'title' => 'Элитная косметика',
        'text' => 'Шампуни, бальзамы, краски и средства для укладки от брендов American Crew, Uppercut Deluxe и Reuzel. Никакой дешевой химии — только безопасный и качественный уход.'
    ],
    [
        'icon' => 'bi-brush',
        'title' => 'Оборудование для тату',
        'text' => 'Индукционные и роторные машинки, одноразовые иглы и премиальные пигменты ведущих мировых производителей для долговечного и яркого результата.'
    ],
    [
        'icon' => 'bi-house-heart',
        'title' => 'Атмосфера и интерьер',
        'text' => 'Лофт-дизайн, кожаные кресла, приглушенный свет и хорошая музыка. У нас нет спешки — только комфорт, расслабление и внимание к каждой детали.'
    ]
];

// 3. Данные для блока интерьера
$interiorImages = [
    [
        'src' => '/assets/images/interior/room-1.jpg',
        'alt' => 'Интерьер барбершопа',
        'class' => 'large' // эта картинка будет большой
    ],
    [
        'src' => '/assets/images/interior/room-2.jpg',
        'alt' => 'Рабочее место',
        'class' => ''
    ],
    [
        'src' => '/assets/images/interior/room-3.jpg',
        'alt' => 'Зона ожидания',
        'class' => ''
    ]
];

// --- ПОДКЛЮЧАЕМ СТИЛИ И КОМПОНЕНТЫ ---

echo '<link rel="stylesheet" href="/template/components/about-intro/style.css">';
require_once __DIR__ . '/../components/about-intro/component.php';
echo renderAboutIntro($introData);

echo '<link rel="stylesheet" href="/template/components/masters-list/style.css">';
require_once __DIR__ . '/../components/masters-list/component.php';
echo renderMastersList($db);

echo '<link rel="stylesheet" href="/template/components/about-tools/style.css">';
require_once __DIR__ . '/../components/about-tools/component.php';
echo renderAboutTools($toolsData);
echo '<link rel="stylesheet" href="/template/components/about-interior/style.css">';
require_once __DIR__ . '/../components/about-interior/component.php';
echo renderAboutInterior($interiorImages);

require_once __DIR__ . '/../template/footer.php';
?>