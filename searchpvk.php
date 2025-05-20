<?php
// Отключаем вывод ошибок на экран, чтобы не ломать JSON
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Устанавливаем заголовки
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Проверяем, установлен ли PDO
if (!extension_loaded('pdo_mysql')) {
    http_response_code(500);
    echo json_encode(['error' => 'Модуль PDO MySQL не установлен']);
    exit();
}

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedelisee";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8mb4");
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка подключения к базе данных: ' . $e->getMessage()]);
    exit();
}

// Функция для очистки входных данных
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Получение введенного пользователем текста
$searchTerm = isset($_GET['term']) ? sanitizeInput($_GET['term']) : '';

$results = [];

if (!empty($searchTerm) && strlen($searchTerm) >= 3) { // Минимальная длина запроса 3 символа
    try {
        $query = "SELECT name FROM pvk WHERE name LIKE :term";
        $stmt = $conn->prepare($query);
        $searchTermWild = '%' . $searchTerm . '%'; // Поиск по подстроке в любом месте
        $stmt->bindParam(':term', $searchTermWild, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row['name'];
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Ошибка выполнения запроса: ' . $e->getMessage()]);
        exit();
    }
}

echo json_encode($results);
?>