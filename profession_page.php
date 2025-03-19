<?php
session_start();

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

$id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$isExpert = isset($_SESSION['role']) && $_SESSION['role'] === 'expert';

$query = $pdo->prepare("SELECT name, short_description, full_description FROM professions WHERE id = :id");
$query->execute(['id' => $id]);
$profession = $query->fetch(PDO::FETCH_ASSOC);

if (!$profession) {
    die("Профессия не найдена");
}

// Получение списка ПВК и количества выборов
$pvkQuery = $pdo->prepare(
    "SELECT pvk.id, pvk.name, COUNT(pvk_prof.userid) AS total_count 
     FROM pvk 
     LEFT JOIN pvk_prof ON pvk.id = pvk_prof.pvkid AND pvk_prof.profid = :profid 
     GROUP BY pvk.id 
     ORDER BY total_count DESC"
);
$pvkQuery->execute(['profid' => $id]);
$pvkList = $pvkQuery->fetchAll();

// Количество экспертов, выбравших хотя бы одно ПВК для профессии
$totalExpertsQuery = $pdo->prepare("SELECT COUNT(DISTINCT userid) FROM pvk_prof WHERE profid = :profid");
$totalExpertsQuery->execute(['profid' => $id]);
$totalExperts = $totalExpertsQuery->fetchColumn();

// Получение выбранных ПВК эксперта
$expertPvk = [];
if ($isExpert) {
    $expertPvkQuery = $pdo->prepare("SELECT pvkid FROM pvk_prof WHERE profid = :profid AND userid = :userid");
    $expertPvkQuery->execute(['profid' => $id, 'userid' => $userId]);
    $expertPvk = array_column($expertPvkQuery->fetchAll(), 'pvkid');
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

    <h2>Перечень ПВК</h2>
    <ul>
        <?php foreach ($pvkList as $pvk) : ?>
            <li>
                <?php echo htmlspecialchars($pvk['name']); ?> -
                <?php echo $pvk['total_count']; ?> выборов
                <?php if ($totalExperts > 0) : ?>
                    (среднее: <?php echo round($pvk['total_count'] / $totalExperts, 2); ?>)
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($isExpert) : ?>
        <form method="post" action="/ITprof/update_pvk.php">
            <h3>Выберите ПВК для профессии</h3>
            <?php foreach ($pvkList as $pvk) : ?>
                <label>
                    <input type="checkbox" name="pvk[]" value="<?php echo $pvk['id']; ?>" <?php echo in_array($pvk['id'], $expertPvk) ? 'checked' : ''; ?>>
                    <?php echo htmlspecialchars($pvk['name']); ?>
                </label><br>
            <?php endforeach; ?>
            <input type="hidden" name="profid" value="<?php echo $id; ?>">
            <button type="submit">Подтвердить</button>
        </form>
    <?php endif; ?>
</div>

<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>
</body>
</html>
