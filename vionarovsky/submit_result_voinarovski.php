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

$userId = $_SESSION['user_id'];
$testId = 15; // ID теста Войнаровского

$answered = isset($data['answered_percent']) ? (int)$data['answered_percent'] : null;
$total    = isset($data['total_percent'])    ? (int)$data['total_percent']    : null;

if ($answered === null || $total === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$sql = "INSERT INTO Test_result_voinarovski (user_id, test_id, percent_from_answered, percent_from_total)
        VALUES (?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $testId, $answered, $total]);

echo json_encode(['status' => 'success']);