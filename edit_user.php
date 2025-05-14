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


// Проверяем, что пользователь – админ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещён! <a href='index.php'>На главную</a>");
}

// Проверяем, передан ли `id` пользователя
if (!isset($_GET['id'])) {
    die("Ошибка: Не указан пользователь! <a href='admin.php'>Вернуться</a>");
}

$userId = $_GET['id'];

// Получаем данные пользователя из базы
$stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Если пользователь не найден
if (!$user) {
    die("Ошибка: Пользователь не найден! <a href='admin.php'>Вернуться</a>");
}

// Если форма отправлена (обновление данных)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = $_POST['name'];
    $newEmail = $_POST['email'];
    $newRole = $_POST['role'];

    // Обновляем данные в базе
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $newName, $newEmail, $newRole, $userId);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя</title>
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
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            margin-top: 20px;
            font-weight: 500;
            color: #555;
        }

        form input[type="text"],
        form input[type="email"],
        form select {
            width: 100%;
            padding: 10px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            margin-bottom: 10px;
            background-color: #fdfdfd;
        }

        form button,
        form a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        form button {
            background-color: #f51cf1;
            color: white;
            margin-right: 10px;
        }

        form button:hover {
            background-color: #d015d0;
        }

        form a {
            background-color: #e0e0e0;
            color: #333;
        }

        form a:hover {
            background-color: #ccc;
        }
        </style>
</head>
<body>
    <header><div id="header-container"></div></header>
    <main style="margin-top: 50px;">

    <h1>Редактирование пользователя</h1>
        <form method="POST">
            <label>Имя:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>



            <label>Роль:</label>
            <select name="role">
                <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>Пользователь</option>
                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Администратор</option>
                <option value="consultant" <?php echo ($user['role'] == 'consultant') ? 'selected' : ''; ?>>Консультант</option>
                <option value="expert" <?php echo ($user['role'] == 'expert') ? 'selected' : ''; ?>>Эксперт</option>
            </select><br>

            <button type="submit">Сохранить</button>
            <a href="admin.php">Отмена</a>
        </form>
    </main>
    <script>
        fetch("siteheader.php")
            .then(response => response.text())
            .then(data => document.getElementById("header-container").innerHTML = data);
    </script>
</body>
</html>