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
$names = isset($_POST['pvk_names']) ? $_POST['pvk_names'] : [];
$ratings = isset($_POST['ratings']) ? $_POST['ratings'] : [];

if (count($names) !== count($ratings)) {
    die("Ошибка: несоответствие данных");
}

if (count($names) < 5 || count($names) > 10) {
    die("Выберите от 5 до 10 ПВК");
}

// Получение ID по имени
$getIdStmt = $pdo->prepare("SELECT id FROM pvk WHERE name = :name");

// Удаляем старые
$pdo->prepare("DELETE FROM pvk_prof WHERE profid = :profid AND userid = :userid")
    ->execute(['profid' => $profId, 'userid' => $userId]);

$insertStmt = $pdo->prepare("INSERT INTO pvk_prof (pvkid, profid, userid, rating) VALUES (:pvkid, :profid, :userid, :rating)");

foreach ($names as $i => $name) {
    $rating = intval($ratings[$i]);
    if ($rating < 1 || $rating > 10) continue;

    $getIdStmt->execute(['name' => $name]);
    $pvk = $getIdStmt->fetch();
    if (!$pvk) continue;

    $insertStmt->execute([
        'pvkid' => $pvk['id'],
        'profid' => $profId,
        'userid' => $userId,
        'rating' => $rating
    ]);
}

header("Location: profession_page.php?id=$profId");
exit();