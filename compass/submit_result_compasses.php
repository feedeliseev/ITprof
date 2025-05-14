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
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$userId = $_SESSION['user_id'];
$testId = 16; // Компасы
$accuracy = isset($data['accuracy']) ? (float)$data['accuracy'] : null;

if ($accuracy === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing accuracy']);
    exit;
}

$sql = "INSERT INTO Test_result_compasses (user_id, test_id, accuracy_percent) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $testId, $accuracy]);

echo json_encode(['status' => 'success']);