<?php

class Database
{
    // Здесь будет храниться объект подключения PDO
    private static $instance = null;
    private $pdo;

    // Конструктор закрыт (чтобы нельзя было сделать new Database() напрямую)
    private function __construct()
    {
        $config = require __DIR__ . '/../config/db.php';

        // Строка подключения (DSN)
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";

        // Настройки для безопасной работы PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ошибки в виде исключений
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Возвращать ассоциативные массивы
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Использовать реальные подготовленные запросы (защита от SQL-инъекций)
        ];

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], $options);
        } catch (PDOException $e) {
            // В реальном проекте тут нужно писать в лог, а не выводить на экран
            die('Ошибка подключения к базе данных: ' . $e->getMessage());
        }
    }

    // Метод для получения единственного экземпляра класса
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Метод для получения самого объекта PDO (через него мы будем делать запросы)
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}