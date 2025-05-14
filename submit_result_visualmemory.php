<?php
session_start();

$host = 'localhost';
$db   = 'feedelisee';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
$test_id = isset($_POST['test_id']) ? $_POST['test_id'] : null;
$accuracy_percent = isset($_POST['accuracy_percent']) ? $_POST['accuracy_percent'] : null;
$memorization_time_sec = isset($_POST['memorization_time_sec']) ? $_POST['memorization_time_sec'] : null;

// Проверка
if (!$user_id || !$test_id || $accuracy_percent === null || $memorization_time_sec === null) {
    die("Ошибка: не все данные переданы.");
}

try {
    $stmt = $pdo->prepare("INSERT INTO Test_result_visualmemory (test_id, user_id, accuracy_percent, memorization_time_sec) VALUES (?, ?, ?, ?)");
    $stmt->execute([$test_id, $user_id, $accuracy_percent, $memorization_time_sec]);
    echo "Результат сохранён.";
} catch (Exception $e) {
    echo "Ошибка при сохранении: " . $e->getMessage();
}