<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Ошибка: пользователь не авторизован.");
}

$host = 'localhost';
$db   = 'feedelisee';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// Получение данных
$user_id = $_SESSION['user_id'];
$test_id = isset($_POST['test_id']) ? (int)$_POST['test_id'] : null;
$sd = isset($_POST['sd']) ? (float)$_POST['sd'] : null;

if (!$test_id || !is_numeric($sd)) {
    die("Ошибка: недостаточно данных.");
}

try {
    $stmt = $pdo->prepare("INSERT INTO Test_result2 (user_id, test_id, sd) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $test_id, $sd]);

    echo "Результат успешно сохранён.";
} catch (Exception $e) {
    echo "Ошибка при сохранении: " . $e->getMessage();
}