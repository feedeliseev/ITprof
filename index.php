<?php session_start();

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

// Получаем три профессии с самым высоким рейтингом
$stmt = $conn->query("SELECT id, name, short_description FROM professions ORDER BY rating DESC LIMIT 3");
$topProfessions = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ОПД</title>
    <link rel="stylesheet" href="styles/style.css" />
</head>
<body>

<header><div id="header-container"></div></header>

<main style="margin-top: 50px;">
    <div style="display: flex; justify-content: center; align-items: center; height: 300px;" class="backimg">

        <h1 id="typing"></h1>
    </div>
    <div class="proflist">
        <?php foreach ($topProfessions as $prof) : ?>
            <a href="profession_page.php?id=<?= $prof['id'] ?>" class="prof">
                <h2 class="profname"><?= htmlspecialchars($prof['name']) ?></h2>
                <p class="profdescr"><?= htmlspecialchars($prof['short_description']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<footer>
    <div style="width: 100%; height: 200px; background-color: #F1F3F4; display: flex; justify-content: center; align-items: center;">
        <h3>Больше профессий во вкладке <a href="ratings.php" class="hr">"список профессий"</a></h3>
    </div>
</footer>

<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>
<script>
    const text = "Добро пожаловать в IT";
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