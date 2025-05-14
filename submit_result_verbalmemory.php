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

$user_id = $_SESSION['user_id'];
$test_id = isset($_POST['test_id']) ? (int)$_POST['test_id'] : null;
$accuracy_percent = isset($_POST['accuracy_percent']) ? (float)$_POST['accuracy_percent'] : null;

if (!$test_id || !is_numeric($accuracy_percent)) {
    die("Ошибка: недостаточно или некорректные данные.");
}

try {
    $stmt = $pdo->prepare("INSERT INTO Test_result_verbalmemory (user_id, test_id, accuracy_percent) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $test_id, $accuracy_percent]);

    echo "Результат успешно сохранён.";
} catch (Exception $e) {
    echo "Ошибка при сохранении: " . $e->getMessage();
}
?>