-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 24 2026 г., 21:37
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `barbershop_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `client_id` int DEFAULT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `master_id` int NOT NULL,
  `service_id` int NOT NULL,
  `certificate_id` int DEFAULT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `bookings`
--

INSERT INTO `bookings` (`id`, `client_id`, `client_name`, `client_phone`, `master_id`, `service_id`, `certificate_id`, `booking_date`, `start_time`, `end_time`, `total_price`, `status`, `created_at`) VALUES
(9, NULL, 'Иван', '89213456789', 1, 3, NULL, '2026-10-30', '10:00:00', '11:00:00', '1800.00', 'completed', '2026-10-29 20:40:51'),
(10, 4, 'Вася', '+79963245564', 10, 3, NULL, '2026-11-05', '10:00:00', '11:00:00', '1800.00', 'completed', '2026-11-05 14:03:47'),
(17, 3, 'administrator', '+79997056814', 1, 3, NULL, '2026-11-10', '10:00:00', '11:00:00', '1800.00', 'completed', '2026-11-09 20:29:55'),
(18, 4, 'Вася', '+79963245564', 1, 3, NULL, '2026-11-10', '11:00:00', '12:00:00', '1800.00', 'completed', '2026-11-09 20:30:51'),
(19, 3, 'administrator', '+79997056814', 3, 6, NULL, '2026-11-10', '10:00:00', '11:00:00', '2000.00', 'completed', '2026-11-09 20:49:57'),
(20, 3, 'administrator', '+79997056814', 2, 29, NULL, '2026-11-10', '10:00:00', '11:30:00', '3000.00', 'completed', '2026-11-09 20:50:13'),
(21, 4, 'Вася', '+79963245564', 1, 3, NULL, '2026-11-11', '10:30:00', '11:30:00', '1800.00', 'completed', '2026-11-10 18:52:39'),
(23, NULL, 'Вася', '+79999999999', 2, 12, NULL, '2026-11-11', '10:00:00', '11:00:00', '2200.00', 'completed', '2026-11-10 20:01:38'),
(24, 3, 'administrator', '+79997056814', 1, 3, NULL, '2026-11-12', '16:00:00', '17:00:00', '1800.00', 'cancelled', '2026-11-12 11:39:43'),
(25, 4, 'Вася', '+79963245564', 1, 3, NULL, '2026-11-25', '13:30:00', '14:30:00', '1800.00', 'pending', '2026-11-24 18:20:54');

-- --------------------------------------------------------

--
-- Структура таблицы `booking_certificates`
--

CREATE TABLE `booking_certificates` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `certificate_id` int NOT NULL,
  `amount_used` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `booking_certificates`
--

INSERT INTO `booking_certificates` (`id`, `booking_id`, `certificate_id`, `amount_used`) VALUES
(1, 17, 1, '1200.00'),
(2, 17, 3, '600.00'),
(3, 19, 3, '2000.00'),
(4, 20, 3, '2000.00');

-- --------------------------------------------------------

--
-- Структура таблицы `certificates`
--

CREATE TABLE `certificates` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) DEFAULT '0.00',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','used') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `used_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `certificates`
--

INSERT INTO `certificates` (`id`, `title`, `price`, `balance`, `code`, `status`, `user_id`, `created_at`, `used_at`) VALUES
(1, 'Подарочный сертификат на 3000 ₽', '3000.00', '0.00', 'GIFT-3000-TEST', 'used', 3, '2026-11-07 07:55:02', '2026-11-09 20:29:55'),
(2, 'Подарочный сертификат на 5000 ₽', '5000.00', '5000.00', 'GIFT-5000-TEST', 'active', 4, '2026-11-07 07:55:02', NULL),
(3, 'Подарочный сертификат на 10000 ₽', '10000.00', '0.00', 'GIFT-10000-TEST', 'used', 3, '2026-11-07 07:55:02', '2026-11-09 20:50:13'),
(4, 'Подарочный сертификат на 5000 ₽', '5000.00', '5000.00', 'GIFT-F19E7088', 'active', NULL, '2026-11-07 08:03:08', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `masters`
--

CREATE TABLE `masters` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `masters`
--

INSERT INTO `masters` (`id`, `user_id`, `name`, `specialization`, `avatar`, `is_active`) VALUES
(1, 7, 'Александр', 'Топ-барбер. Эксперт по классическим стрижкам и королевскому бритью.', '/assets/images/masters/master-1.webp', 1),
(2, 8, 'Мария', 'Стилист-колорист. Специализируется на сложных окрашиваниях и уходе.', '/assets/images/masters/master-2.webp', 1),
(3, 9, 'Мирослава', 'Универсальный мастер. Идеальные фейды, андеркаты и женские стрижки.', '/assets/images/masters/master-3.webp', 1),
(4, 10, 'Егор', 'Тату-мастер. Работает в стилях реализм, графика и лайнворк.', '/assets/images/masters/master-4.webp', 1),
(5, 11, 'Анна', 'Стилист. Создает красивые женские стрижки и вечерние укладки.', '/assets/images/masters/master-6.webp', 1),
(6, 12, 'Виктор', 'Барбер. Фанат текстурных стрижек (Crop, Quiff) и оформления бороды.', '/assets/images/masters/master-5.webp', 1),
(7, 13, 'Кристина', 'Мастер пирсинга. Профессиональный подход и стерильность.', '/assets/images/masters/master-8.webp', 1),
(8, 14, 'Максим', 'Барбер-эксперт. Лучший специалист по детским стрижкам и машинке.', '/assets/images/masters/master-9.webp', 1),
(9, 15, 'Ольга', 'Мастер по волосам. Лечебные процедуры, уходы и тонирование.', '/assets/images/masters/master-10.webp', 1),
(10, 16, 'Артем', 'Барбер. Специалист по камуфляжу седины и коррекции контуров.', '/assets/images/masters/master-7.webp', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `master_skills`
--

CREATE TABLE `master_skills` (
  `master_id` int NOT NULL,
  `service_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `master_skills`
--

INSERT INTO `master_skills` (`master_id`, `service_id`) VALUES
(1, 1),
(3, 1),
(6, 1),
(8, 1),
(10, 1),
(1, 2),
(3, 2),
(6, 2),
(8, 2),
(10, 2),
(1, 3),
(3, 3),
(6, 3),
(8, 3),
(10, 3),
(1, 4),
(3, 4),
(6, 4),
(8, 4),
(10, 4),
(1, 5),
(3, 5),
(6, 5),
(8, 5),
(10, 5),
(1, 6),
(3, 6),
(6, 6),
(8, 6),
(10, 6),
(1, 7),
(3, 7),
(6, 7),
(8, 7),
(10, 7),
(1, 8),
(3, 8),
(6, 8),
(8, 8),
(10, 8),
(1, 9),
(3, 9),
(6, 9),
(8, 9),
(10, 9),
(1, 10),
(3, 10),
(6, 10),
(8, 10),
(10, 10),
(2, 11),
(3, 11),
(5, 11),
(9, 11),
(2, 12),
(3, 12),
(5, 12),
(9, 12),
(2, 13),
(3, 13),
(5, 13),
(9, 13),
(2, 14),
(3, 14),
(5, 14),
(9, 14),
(2, 15),
(3, 15),
(5, 15),
(9, 15),
(2, 16),
(3, 16),
(5, 16),
(9, 16),
(1, 17),
(3, 17),
(8, 17),
(1, 18),
(3, 18),
(8, 18),
(1, 19),
(3, 19),
(8, 19),
(1, 20),
(6, 20),
(10, 20),
(1, 21),
(6, 21),
(10, 21),
(1, 22),
(6, 22),
(10, 22),
(1, 23),
(6, 23),
(10, 23),
(1, 24),
(6, 24),
(10, 24),
(2, 25),
(9, 25),
(10, 25),
(2, 26),
(9, 26),
(10, 26),
(2, 27),
(9, 27),
(10, 27),
(2, 28),
(9, 28),
(10, 28),
(2, 29),
(9, 29),
(10, 29),
(4, 30),
(4, 31),
(4, 32),
(4, 33),
(7, 34),
(7, 35),
(7, 36),
(7, 37),
(2, 38),
(5, 38),
(9, 38),
(2, 39),
(5, 39),
(9, 39),
(2, 40),
(5, 40),
(9, 40);

-- --------------------------------------------------------

--
-- Структура таблицы `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(29, 3, '6ef3b887a8b6ae5901eaea87e3c4b4c0bb7eddfcb5360250f5d90f8671de5244', '2026-11-12 00:07:47', 0, '2026-11-11 20:07:47'),
(30, 4, '1a3f7cd390cc7c3043810a902e9a9b17b0b31a7d553077fabefdd486d45b24c5', '2026-11-24 22:31:41', 0, '2026-11-24 18:31:41');

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio`
--

CREATE TABLE `portfolio` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `portfolio`
--

INSERT INTO `portfolio` (`id`, `title`, `image_path`, `category`, `created_at`) VALUES
(1, 'Мужская классическая стрижка', '/assets/images/portfolio/work-1.webp', 'Мужские стрижки', '2026-10-26 20:04:28'),
(2, 'Мужской фейд с четким переходом', '/assets/images/portfolio/work-2.webp', 'Мужские стрижки', '2026-10-26 20:04:28'),
(3, 'Мужская стрижка машинкой', '/assets/images/portfolio/work-3.webp', 'Мужские стрижки', '2026-10-26 20:04:28'),
(4, 'Женское окрашивание с укладкой', '/assets/images/portfolio/work-4.webp', 'Женские стрижки', '2026-10-26 20:04:28'),
(5, 'Художественная татуировка', '/assets/images/portfolio/work-5.webp', 'Тату', '2026-10-26 20:04:28'),
(6, 'Мужская стрижка короткая\r\n', '/assets/images/portfolio/work-6.webp', 'Мужские стрижки', '2026-10-27 07:39:54');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` int NOT NULL DEFAULT '5',
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `rating`, `text`, `created_at`) VALUES
(1, 1, 5, 'Лучший барбершоп в городе! Хожу только сюда, мастера знают свое дело.', '2026-11-04 15:20:40'),
(2, 4, 4, 'Отличная атмосфера и качественная стрижка. Единственный минус — сложно записаться, всё занято.', '2026-11-04 16:20:40'),
(3, 7, 5, 'Отличный сервис, хорошие мастера, приемлемые цены', '2026-11-05 15:27:16'),
(4, 17, 5, 'Всё было очень и очень хорошо', '2026-11-05 15:28:27');

-- --------------------------------------------------------

--
-- Структура таблицы `services`
--

CREATE TABLE `services` (
  `id` int NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `type` enum('fixed','area','zone') COLLATE utf8mb4_unicode_ci DEFAULT 'fixed',
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `services`
--

INSERT INTO `services` (`id`, `category`, `sort_order`, `name`, `price`, `duration`, `type`, `is_active`) VALUES
(1, 'Мужские стрижки', 1, 'Классическая мужская стрижка', '1500.00', 45, 'fixed', 1),
(2, 'Мужские стрижки', 1, 'Фейд (Low / Mid / High)', '1800.00', 60, 'fixed', 1),
(3, 'Мужские стрижки', 1, 'Андеркат (Undercut)', '1800.00', 60, 'fixed', 1),
(4, 'Мужские стрижки', 1, 'Кроп (Crop)', '1600.00', 45, 'fixed', 1),
(5, 'Мужские стрижки', 1, 'Текстурный кроп (Textured Crop)', '1800.00', 50, 'fixed', 1),
(6, 'Мужские стрижки', 1, 'Помпадур (Pompadour)', '2000.00', 60, 'fixed', 1),
(7, 'Мужские стрижки', 1, 'Киф (Quiff)', '1900.00', 55, 'fixed', 1),
(8, 'Мужские стрижки', 1, 'Стрижка \"Buzz Cut\" (под машинку)', '1000.00', 30, 'fixed', 1),
(9, 'Мужские стрижки', 1, 'Боковой пробор (Side Part)', '1700.00', 50, 'fixed', 1),
(10, 'Мужские стрижки', 1, 'Окантовка / Контуры (Line up)', '800.00', 20, 'fixed', 1),
(11, 'Женские стрижки', 2, 'Женская стрижка \"Каскад\"', '2500.00', 90, 'fixed', 1),
(12, 'Женские стрижки', 2, 'Женская стрижка \"Боб\"', '2200.00', 60, 'fixed', 1),
(13, 'Женские стрижки', 2, 'Женская стрижка \"Пикси\"', '2000.00', 60, 'fixed', 1),
(14, 'Женские стрижки', 2, 'Коррекция длины женской стрижки', '1500.00', 45, 'fixed', 1),
(15, 'Женские стрижки', 2, 'Оформление чёлки', '500.00', 15, 'fixed', 1),
(16, 'Женские стрижки', 2, 'Лечебная стрижка горячими ножницами', '3000.00', 90, 'fixed', 1),
(17, 'Детская стрижка', 3, 'Детская стрижка машинкой', '800.00', 30, 'fixed', 1),
(18, 'Детская стрижка', 3, 'Детская стрижка ножницами', '1000.00', 45, 'fixed', 1),
(19, 'Детская стрижка', 3, 'Детский фейд (плавный переход)', '1200.00', 45, 'fixed', 1),
(20, 'Борода и усы', 4, 'Королевское бритье (опасной бритвой)', '2000.00', 45, 'fixed', 1),
(21, 'Борода и усы', 4, 'Оформление бороды и усов', '1200.00', 40, 'fixed', 1),
(22, 'Борода и усы', 4, 'Камуфляж бороды (окрашивание)', '1500.00', 30, 'fixed', 1),
(23, 'Борода и усы', 4, 'Коррекция контуров бороды', '1000.00', 30, 'fixed', 1),
(24, 'Борода и усы', 4, 'Мытье и укладка бороды', '800.00', 20, 'fixed', 1),
(25, 'Окрашивание', 5, 'Мужское окрашивание (камуфляж седины)', '1500.00', 60, 'fixed', 1),
(26, 'Окрашивание', 5, 'Окрашивание корней', '2000.00', 60, 'fixed', 1),
(27, 'Окрашивание', 5, 'Тонирование волос', '2500.00', 90, 'fixed', 1),
(28, 'Окрашивание', 5, 'Обесцвечивание / Блонд', '3500.00', 120, 'fixed', 1),
(29, 'Окрашивание', 5, 'Кислотная смывка', '3000.00', 90, 'fixed', 1),
(30, 'Тату', 7, 'Нанесение татуировки', '500.00', 60, 'area', 1),
(31, 'Тату', 7, 'Коррекция / обновление старой тату', '1500.00', 45, 'fixed', 1),
(32, 'Тату', 7, 'Перекрытие старой татуировки (Cover-up)', '2000.00', 90, 'fixed', 1),
(33, 'Тату', 7, 'Разработка индивидуального эскиза', '1000.00', 30, 'fixed', 1),
(34, 'Пирсинг', 8, 'Пирсинг уха (мочка/хрящ)', '1000.00', 20, 'fixed', 1),
(35, 'Пирсинг', 8, 'Пирсинг носа', '1200.00', 20, 'fixed', 1),
(36, 'Пирсинг', 8, 'Пирсинг брови', '1500.00', 20, 'fixed', 1),
(37, 'Пирсинг', 8, 'Замена украшения', '500.00', 15, 'fixed', 1),
(38, 'Укладка', 6, 'Укладка волос (повседневная)', '600.00', 30, 'fixed', 1),
(39, 'Укладка', 6, 'Укладка с использованием утюжка', '800.00', 45, 'fixed', 1),
(40, 'Укладка', 6, 'Создание вечерней прически', '1500.00', 60, 'fixed', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('client','admin','master') COLLATE utf8mb4_unicode_ci DEFAULT 'client',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Иван', '89997056814', NULL, '$2y$10$9n3shO4QnU8RbLjkaKhIre.TzXe5aUNqlzxWRHp.HciSmJW589.z2', 'client', '2026-10-26 13:17:45'),
(2, 'Администратор', '+79990000000', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-10-27 09:32:30'),
(3, 'administrator', '+79997056814', 'dimasvladas@gmail.com', '$2y$10$rRzRb2rMRdB4oHKMkcogIume7n5o/9aTwjd9JfDnSR7O444FN/nOu', 'admin', '2026-10-27 16:08:38'),
(4, 'Вася', '+79963245564', 'savin.vladislav2302@gmail.com', '$2y$10$rKrrU.3hkB3k1dYNiKpsRO5EFy2H34GVRkODTP/a6cpyOoDK9jtoO', 'client', '2026-10-27 16:12:32'),
(7, 'Александр', '+79990000001', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 13:24:23'),
(8, 'Мария', '+79990000002', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 13:24:23'),
(9, 'Мирослава', '+79990000003', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 14:39:54'),
(10, 'Егор', '+79990000004', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 14:39:54'),
(11, 'Анна', '+79990000005', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 14:39:54'),
(12, 'Виктор', '+79990000006', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 14:39:54'),
(13, 'Кристина', '+79990000007', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 14:39:54'),
(14, 'Максим', '+79990000008', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 14:39:54'),
(15, 'Ольга', '+79990000009', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 14:39:54'),
(16, 'Артем', '+79990000010', NULL, '$2y$10$VHQfM01ceicipYoVl1HdoeamDHmb3zsymRFYjXDrtyAHygPftzpae', 'master', '2026-11-05 14:39:54'),
(17, 'Петя Петрович', '+799566743212', NULL, '$2y$10$n28wz7lRWyxodR5B/rxyDOSG.OpLQjTWFhu6MCWnbPICmexeznchq', 'client', '2026-11-05 15:28:04');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `master_id` (`master_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `fk_booking_cert` (`certificate_id`);

--
-- Индексы таблицы `booking_certificates`
--
ALTER TABLE `booking_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `certificate_id` (`certificate_id`);

--
-- Индексы таблицы `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `masters`
--
ALTER TABLE `masters`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `master_skills`
--
ALTER TABLE `master_skills`
  ADD PRIMARY KEY (`master_id`,`service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Индексы таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT для таблицы `booking_certificates`
--
ALTER TABLE `booking_certificates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `masters`
--
ALTER TABLE `masters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT для таблицы `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `services`
--
ALTER TABLE `services`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`master_id`) REFERENCES `masters` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `fk_booking_cert` FOREIGN KEY (`certificate_id`) REFERENCES `certificates` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `booking_certificates`
--
ALTER TABLE `booking_certificates`
  ADD CONSTRAINT `booking_certificates_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_certificates_ibfk_2` FOREIGN KEY (`certificate_id`) REFERENCES `certificates` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `master_skills`
--
ALTER TABLE `master_skills`
  ADD CONSTRAINT `master_skills_ibfk_1` FOREIGN KEY (`master_id`) REFERENCES `masters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `master_skills_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
