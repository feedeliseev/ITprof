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
$isExpert = isset($_SESSION['role']) && $_SESSION['role'] === 'expert';
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';


// Получаем профессии из базы
$stmt = $conn->query("SELECT * FROM professions");
$professions = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профессии</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .profsfeed {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px 0;
            background-color: #f9f9f9;
            border-radius: 8px;
            transition: background 0.3s ease-in-out;
        }

        .profsfeed:hover {
            background: #e0e0e0;
        }

        .hr {
            text-decoration: none;
            color: black;
            flex-grow: 1;
        }

        .profname2 {
            margin: 0;
            font-size: 22px;
        }

        .profdescr2 {
            font-size: 14px;
            color: #666;
        }

        .rating-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rating-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: white;
            background: #5bc0de;
            transition: background 0.3s;
        }

        .rating-input {
            display: none;
            width: 100px;
        }

        .rating-container.expert .rating-input {
            display: inline-block;
        }
    </style>
</head>
<body>
<header><div id="header-container"></div></header>

<main style="margin-top: 50px; ">
    <div style="display: flex; justify-content: center; align-items: center; height: 300px;" class="backimg"><h1 id="typing"></h1></div>

    <?php foreach ($professions as $prof) : ?>
        <div class="profsfeed">
            <a href="profession_page.php?id=<?= $prof['id'] ?>" class="hr">
                <h2 class="profname2"><?= htmlspecialchars($prof['name']) ?></h2>
                <p class="profdescr2"><?= htmlspecialchars($prof['short_description']) ?></p>
            </a>
            <div class="rating-container <?= $isExpert ? 'expert' : '' ?>">
                <?php if ($isAdmin) : ?>
                    <div class="rating-container expert">
                        <input type="range" class="rating-input" min="1" max="10" value="<?= $prof['rating'] ?>" data-id="<?= $prof['id'] ?>">
                        <div class="rating-circle" id="rating-<?= $prof['id'] ?>">
                            <?= $prof['rating'] ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</main>

<script>
    document.querySelectorAll('.rating-input').forEach(input => {
        input.addEventListener('input', function () {
            const rating = this.value;
            const professionId = this.dataset.id;
            document.getElementById('rating-' + professionId).textContent = rating;

            fetch('update_rating.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${professionId}&rating=${rating}`
            });
        });
    });
</script>
<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>
<script>
    const text = "Список профессий";
    let index = 0;

    function typeEffect() {
        if (index < text.length) {
            document.getElementById("typing").innerHTML += text[index];
            index++;
            setTimeout(typeEffect, 100); // Скорость печати (мс)
        }
    }

    typeEffect();
</script>
</body>
</html>