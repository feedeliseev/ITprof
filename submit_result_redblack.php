<?php
header('Content-Type: application/json');

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
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$userId = isset($data['user_id']) ? $data['user_id'] : null;
$testId = isset($data['test_id']) ? $data['test_id'] : null;
$speed  = isset($data['speed']) ? $data['speed'] : null;

if (!$userId || !$testId || $speed === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$sql = "INSERT INTO Test_result_redblack (user_id, test_id, speed) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $testId, $speed]);

echo json_encode(['status' => 'success']);