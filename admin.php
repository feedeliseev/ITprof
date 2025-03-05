

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

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещён! <a href='auth.php'>Войти</a>");
}

// Получаем роль пользователя из сессии
if ($_SESSION['role'] !== 'admin') {
    die("У вас нет доступа! <a href='index.php'>На главную</a>");
}

// Если пользователь админ, продолжаем выполнение админки
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="styles/style.css" />

</head>
<body>
<header><div id="header-container"></div></header>
<main style="margin-top: 50px;">

<h1>Админ-панель</h1>
<p>Добро пожаловать, <?php echo $_SESSION['name']; ?>!</p>

<h2>Список пользователей</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Email</th>
        <th>Роль</th>
        <th>Действия</th>
    </tr>

    <?php
    // Получаем список пользователей
    $stmt = $conn->query("SELECT id, name, email, role FROM users");
    while ($row = $stmt->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['role']}</td>
                <td>
                    <a href='edit_user.php?id={$row['id']}'>Редактировать</a>
                    <a href='delete_user.php?id={$row['id']}'>Удалить</a>
                </td>
            </tr>";
    }
    ?>
</table>

    <a href="logout.php">Выйти</a>
    <a href="index.php">На главную</a>
</main>
<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>
</body>
</html>