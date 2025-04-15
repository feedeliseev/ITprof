<?php
session_start();

// Подключение к базе
$host = 'localhost';
$db = 'feedelisee';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещён! <a href='index.php'>На главную</a>");
}

// Если форма отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $short_description = trim($_POST['short_description']);
    $full_description = trim($_POST['full_description']);

    // Проверка, чтобы поля не были пустыми
    if (empty($name) || empty($short_description) || empty($full_description)) {
        $error = "Все поля обязательны для заполнения!";
    } else {
        // Подготовленный запрос для безопасности
        $stmt = $conn->prepare("INSERT INTO professions (name, short_description, full_description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $short_description, $full_description);

        if ($stmt->execute()) {
            header("Location: ratings.php"); // Перенаправляем на список профессий
            exit();
        } else {
            $error = "Ошибка при добавлении профессии!";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить профессию</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin: 50px auto;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #F1F3F4; /*молочно-бежевый с прозрачностью*/
            color: black;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            border: 2px solid #D3D3D3;

        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header><div id="header-container"></div></header>
<main  style="margin-top: 50px;">
<div class="container">
    <h1>Добавить новую профессию</h1>

    <?php if (isset($error)) : ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Название профессии:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="short_description">Краткое описание:</label>
            <input type="text" id="short_description" name="short_description" required>
        </div>

        <div class="form-group">
            <label for="full_description">Полное описание:</label>
            <textarea id="full_description" name="full_description" rows="5" required></textarea>
        </div>

        <button type="submit">Добавить профессию</button>
    </form>

    <a href="ratings.php" class="back-link">Вернуться к списку профессий</a>
</div>
</main>
<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>

</body>
</html>