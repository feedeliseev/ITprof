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

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'expert') {
    die("Нет доступа");
}

$userId = $_SESSION['user_id'];
$profId = isset($_POST['profid']) ? intval($_POST['profid']) : 0;
$selectedPvk = isset($_POST['pvk']) ? $_POST['pvk'] : [];
$ratings = isset($_POST['rating']) ? $_POST['rating'] : [];

if ($profId === 0) {
    die("Ошибка: неверный идентификатор профессии");
}

if (count($selectedPvk) < 5 || count($selectedPvk) > 10) {
    die("Ошибка: выберите от 5 до 10 ПВК.");
}

// Удалить предыдущие оценки этого эксперта
$deleteStmt = $pdo->prepare("DELETE FROM pvk_prof WHERE profid = :profid AND userid = :userid");
$deleteStmt->execute(['profid' => $profId, 'userid' => $userId]);

// Вставить новые
$insertStmt = $pdo->prepare("INSERT INTO pvk_prof (pvkid, profid, userid, rating) VALUES (:pvkid, :profid, :userid, :rating)");

foreach ($selectedPvk as $pvkId => $on) {
    $rating = isset($ratings[$pvkId]) ? intval($ratings[$pvkId]) : 0;
    if ($rating < 1 || $rating > 10) {
        continue; // пропускаем недопустимые значения
    }
    $insertStmt->execute([
        'pvkid' => $pvkId,
        'profid' => $profId,
        'userid' => $userId,
        'rating' => $rating
    ]);
}

header("Location: profession_page.php?id=$profId");
exit();