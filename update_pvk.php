<?php
session_start();

$dsn = 'mysql:host=localhost;dbname=feedelisee;charset=utf8mb4';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Проверяем, авторизован ли эксперт
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'expert') {
    die("Доступ запрещен");
}

$userId = $_SESSION['user_id'];
$profId = isset($_POST['profid']) ? intval($_POST['profid']) : 0;
$selectedPvk = isset($_POST['pvk']) ? $_POST['pvk'] : [];

if ($profId === 0) {
    die("Ошибка: неверный идентификатор профессии");
}

// Ограничение: нельзя выбрать более 10 ПВК
if (count($selectedPvk) > 10) {
    die("Ошибка: Вы не можете выбрать более 10 ПВК для одной профессии.");
}

// Удаляем старые записи эксперта для данной профессии
$deleteQuery = $pdo->prepare("DELETE FROM pvk_prof WHERE profid = :profid AND userid = :userid");
$deleteQuery->execute(['profid' => $profId, 'userid' => $userId]);

// Вставляем новые записи
$insertQuery = $pdo->prepare("INSERT INTO pvk_prof (pvkid, profid, userid) VALUES (:pvkid, :profid, :userid)");
foreach ($selectedPvk as $pvkId) {
    $insertQuery->execute(['pvkid' => intval($pvkId), 'profid' => $profId, 'userid' => $userId]);
}

header("Location: profession_page.php?id=$profId");
exit();
