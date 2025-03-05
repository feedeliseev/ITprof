<?php
session_start();

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$db_name = "feedelisee";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $role = $_POST["role"];

        // Валидация данных
        if (strlen($name) < 3 || strlen($password) < 6) {
            header("Location: auth.php?message=" . urlencode("Имя минимум 3 символа, пароль минимум 6"));
            exit();
        }

        // Проверяем, существует ли уже пользователь с таким email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(["email" => $email]);
        if ($stmt->rowCount() > 0) {
            header("Location: auth.php?message=" . urlencode("Пользователь с таким email уже зарегистрирован"));
            exit();
        }

        // Хешируем пароль
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Записываем нового пользователя
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        $stmt->execute([
            "name" => $name,
            "email" => $email,
            "password" => $hashedPassword,
            "role" => $role
        ]);

        // Получаем ID нового пользователя
        $user_id = $conn->lastInsertId();

        // Создаём сессию
        $_SESSION["user_id"] = $user_id;
        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["role"] = $role;

        // Перенаправляем в личный кабинет
        header("Location: dashboard.php");
        exit();
    }
} catch (PDOException $e) {
    header("Location: auth.php?message=" . urlencode("Ошибка сервера: " . $e->getMessage()));
    exit();
}
?>