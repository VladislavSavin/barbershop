<?php
/**
 * Валидаторы данных
 * Файл: src/validators.php
 */

/**
 * Приводит номер к формату +7XXXXXXXXXX и проверяет его.
 * Возвращает нормализованный номер или null, если номер некорректный.
 */
function normalizePhone($phone) {
    // 1. Убираем всё, кроме цифр
    $digits = preg_replace('/\D/', '', $phone);

    if (empty($digits)) return null;

    // 2. Приводим к 11 цифрам, начинающимся с 7
    if (strlen($digits) === 10 && $digits[0] === '9') {
        // 9001234567 -> 79001234567
        $digits = '7' . $digits;
    } elseif (strlen($digits) === 11 && $digits[0] === '8') {
        // 89001234567 -> 79001234567
        $digits = '7' . substr($digits, 1);
    }

    // 3. Финальная проверка
    // Должно быть ровно 11 цифр, первая = 7, вторая = 9
    if (strlen($digits) === 11 && $digits[0] === '7' && $digits[1] === '9') {
        return '+' . $digits;
    }

    return null;
}