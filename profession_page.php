<?php
$dsn = 'mysql:host=localhost;dbname=feedelisee;charset=utf8mb4';
$username = 'root';
$password = '';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 1; // Получаем id из URL, по умолчанию 1

$query = $pdo->prepare("SELECT name, short_description, full_description FROM professions WHERE id = :id");
$query->execute(['id' => $id]);
$profession = $query->fetch(PDO::FETCH_ASSOC);

if (!$profession) {
    die("Профессия не найдена");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($profession['name']); ?></title>
    <link rel="stylesheet" href="styles/style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="styles/profStyle.css?v=<?php echo time(); ?>" />
</head>
<body>
<header><div id="header-container"></div></header>
<div class="container">
    <div class="title-block">
        <h1><?php echo htmlspecialchars($profession['name']); ?></h1>
        <img src="pictures/Web_developer.png" alt="<?php echo htmlspecialchars($profession['name']); ?>">
    </div>
    <div class="info-block">
        <p><?php echo nl2br(htmlspecialchars($profession['short_description'])); ?></p>
        <p><?php echo htmlspecialchars_decode($profession['full_description']); ?></p>
    </div>
</div>

<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>
</body>
</html>