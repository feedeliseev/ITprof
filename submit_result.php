<?php
// Показываем ошибки (для отладки)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Подключение к базе данных
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
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получаем POST-данные
$user_id        = isset($_POST['user_id']) ? $_POST['user_id'] : null;
$test_id        = isset($_POST['test_id']) ? $_POST['test_id'] : null;
$score_percent  = isset($_POST['score_percent']) ? $_POST['score_percent'] : null;
$mid            = isset($_POST['mid']) ? $_POST['mid'] : null;

if (!$user_id || !$test_id || $score_percent === null || $mid === null) {
    die("Ошибка: недостаточно данных для записи результата.");
}

try {
    // Проверка: существует ли пользователь
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    if (!$stmt->fetch()) {
        die("Пользователь с ID $user_id не найден.");
    }

    // Проверка: существует ли тест
    $stmt = $pdo->prepare("SELECT id FROM Tests WHERE id = ?");
    $stmt->execute([$test_id]);
    if (!$stmt->fetch()) {
        die("Тест с ID $test_id не найден.");
    }

    // Вставка результата в таблицу Test_result
    $stmt = $pdo->prepare("
        INSERT INTO Test_result (test_id, user_id, score_percent, mid)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$test_id, $user_id, $score_percent, $mid]);

    echo "Результат успешно сохранён.";

} catch (Exception $e) {
    die("Ошибка при выполнении запроса: " . $e->getMessage());
}
?>