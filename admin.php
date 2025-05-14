

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM users WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="styles/style.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f8;
            margin: 0;
            padding: 0;
            color: #333;
        }

        main {
            max-width: 1100px;
            margin: 60px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 16px;
            background-color: #ffffffdd;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 18px;
            text-align: left;
        }

        th {
            background-color: #f51cf1;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f7f9fc;
        }

        tr:hover {
            background-color: #eef2f7;
            transition: background-color 0.2s;
        }

        td {
            color: #333;
        }

        form {
            display: inline;
        }

        .admin-button,
        button {
            background-color: #e53935;
            color: white;
            border: none;
            padding: 6px 12px;
            margin: 2px 0;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .admin-button {
            background-color: #1976d2;
        }

        .admin-button:hover {
            background-color: #125aa2;
        }

        button:hover {
            background-color: #c62828;
        }

        .top-links {
            text-align: center;
            margin-top: 30px;
        }

        .top-links a {
            text-decoration: none;
            margin: 0 10px;
            color: #1976d2;
            font-weight: 500;
        }

        .top-links a:hover {
            text-decoration: underline;
        }


    </style>
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
                    
                    <form method='post'>
                        <input type='hidden' name='id' value='$row[id]'>
                        <button type='submit'>Удалить</button>
                    </form>
                      
                </td>
            </tr>";
    }
    ?>
</table>

    <div class="top-links">
        <a href="index.php">На главную</a>
        <a href="logout.php">Выйти</a>
    </div>
</main>
<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>
</body>
</html>