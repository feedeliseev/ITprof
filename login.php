<?php
session_start();
ob_start(); // Включаем буферизацию вывода для избежания ошибок

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к БД
$host = "localhost";
$db_name = "feedelisee";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["name"]);
        $password = trim($_POST["password"]);

        // Проверяем, что поля не пустые
        if (empty($name) || empty($password)) {
            header("Location: login.php?message=" . urlencode("Заполните все поля"));
            exit();
        }

        // Проверяем пользователя в БД
        $stmt = $conn->prepare("SELECT id, name, password, email, role FROM users WHERE name = :name");
        $stmt->execute(["name" => $name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Проверяем пароль
            if (password_verify($password, $user["password"])) {
                // Успешный вход — создаём сессию
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

                // Проверяем, сохранились ли данные в сессии
                if (!isset($_SESSION["user_id"])) {
                    die("Ошибка: сессия не была установлена!");
                }

                // Перенаправляем в личный кабинет
                if (!headers_sent()) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo '<script>window.location.href = "dashboard.php";</script>';
                    exit();
                }
            } else {
                header("Location: login.php?message=" . urlencode("Неверное имя или пароль"));
                exit();
            }
        } else {
            header("Location: login.php?message=" . urlencode("Пользователь не найден"));
            exit();
        }
    }
} catch (PDOException $e) {
    die("Ошибка сервера: " . $e->getMessage());
}

ob_end_flush(); // Завершаем буферизацию
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="styles/authstyle.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Авторизация на Портал</h2>
        <a href="index.php"><img class="logo" src="styles/images/itmologo.png"></a>
    </div>

    <?php if (isset($_GET['message'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>

    <!-- Форма с исправленным методом и правильным action -->
    <form action="login.php" method="POST">
        <input type="text" name="name" placeholder="Имя" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>


</div>
</body>
</html>