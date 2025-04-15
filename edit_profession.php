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

// Получаем ID профессии из параметра запроса
$profId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($profId === 0) {
    die("Ошибка: неверный идентификатор профессии");
}

// Получаем данные о профессии
$stmt = $conn->prepare("SELECT name, short_description, full_description FROM professions WHERE id = ?");
$stmt->bind_param("i", $profId);
$stmt->execute();
$result = $stmt->get_result();
$profession = $result->fetch_assoc();

if (!$profession) {
    die("Ошибка: профессия не найдена");
}

// Если форма обновления данных отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $short_description = trim($_POST['short_description']);
    $full_description = trim($_POST['full_description']);

    if (empty($name) || empty($short_description) || empty($full_description)) {
        $error = "Все поля обязательны!";
    } else {
        $updateStmt = $conn->prepare("UPDATE professions SET name = ?, short_description = ?, full_description = ? WHERE id = ?");
        $updateStmt->bind_param("sssi", $name, $short_description, $full_description, $profId);

        if ($updateStmt->execute()) {
            header("Location: profession_page.php?id=$profId");
            exit();
        } else {
            $error = "Ошибка при обновлении профессии!";
        }

        $updateStmt->close();
    }
}

// Если нажата кнопка удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    // Удаляем все связанные записи в pvk_prof
    $conn->query("DELETE FROM pvk_prof WHERE profid = $profId");

    // Удаляем профессию
    $deleteStmt = $conn->prepare("DELETE FROM professions WHERE id = ?");
    $deleteStmt->bind_param("i", $profId);

    if ($deleteStmt->execute()) {
        header("Location: professions.php"); // Перенаправление на список профессий
        exit();
    } else {
        $error = "Ошибка при удалении профессии!";
    }

    $deleteStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование профессии</title>
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
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .save-button {
            background-color: #007BFF;
            color: white;
        }

        .save-button:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #DC3545;
            color: white;
            margin-left: 10px;
        }

        .delete-button:hover {
            background-color: #b22222;
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

<div class="container">
    <h1>Редактирование профессии</h1>

    <?php if (isset($error)) : ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">Название профессии:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($profession['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="short_description">Краткое описание:</label>
            <input type="text" id="short_description" name="short_description" value="<?= htmlspecialchars($profession['short_description']) ?>" required>
        </div>

        <div class="form-group">
            <label for="full_description">Полное описание:</label>
            <textarea id="full_description" name="full_description" rows="5" required><?= htmlspecialchars($profession['full_description']) ?></textarea>
        </div>

        <button type="submit" name="update" class="save-button">Сохранить изменения</button>
        <button type="submit" name="delete" class="delete-button" onclick="return confirm('Вы уверены, что хотите удалить эту профессию? Это действие нельзя отменить!');">Удалить профессию</button>
    </form>

    <a href="profession_page.php?id=<?= $profId ?>" class="back-link">Вернуться к профессии</a>
</div>

<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>

</body>
</html>