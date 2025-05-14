<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$host = 'localhost';
$db   = 'feedelisee';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$userId     = $_SESSION['user_id'];
$testId     = 14; // Укажи правильный ID теста
$percentage = isset($data['percentage']) ? (int)$data['percentage'] : null;
$duration   = isset($data['duration'])   ? (int)$data['duration']   : null;

if ($percentage === null || $duration === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$sql = "INSERT INTO Test_result_landoltring (user_id, test_id, percentage, duration) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $testId, $percentage, $duration]);

echo json_encode(['status' => 'success']);