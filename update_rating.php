<?php
$host = 'localhost';  // Сервер базы данных
$db = 'feedelisee';  // Имя базы данных
$user = 'root';  // Имя пользователя базы
$pass = '';  // Пароль пользователя

// Подключение к базе
$conn = new mysqli($host, $user, $pass, $db);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Устанавливаем кодировку UTF-8
$conn->set_charset("utf8");
session_start();

// Проверяем, является ли пользователь экспертом
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'expert') {
    http_response_code(403);
    exit("Нет прав на изменение рейтинга!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['rating'])) {
    $id = (int) $_POST['id'];
    $rating = (int) $_POST['rating'];

    if ($rating < 1 || $rating > 10) {
        http_response_code(400);
        exit("Некорректный рейтинг!");
    }

    $stmt = $conn->prepare("UPDATE professions SET rating = ? WHERE id = ?");
    $stmt->bind_param("ii", $rating, $id);
    $stmt->execute();

    echo "Оценка обновлена!";
}
?>