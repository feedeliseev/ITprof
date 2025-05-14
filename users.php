<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещён! <a href='auth.php'>Войти</a>");
}

$allowedRoles = ['admin', 'expert', 'consultant'];
if (!in_array($_SESSION['role'], $allowedRoles)) {
    die("У вас нет доступа! <a href='index.php'>На главную</a>");
}

// Подключение к БД
$host = 'localhost';
$db = 'feedelisee';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи</title>
    <link rel="stylesheet" href="styles/style.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f8;
            margin: 0;
            padding: 0;
        }

        main {
            max-width: 1000px;
            margin: 60px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        h1 {
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

        .btn {
            background-color: #1976d2;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color: #125aa2;
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
<main>
    <h1>Список пользователей</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Профиль</th>
        </tr>

        <?php
        $result = $conn->query("SELECT id, name, email FROM users");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td><a href='profile.php?user_id={$row['id']}' class='btn'>Перейти</a></td>
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