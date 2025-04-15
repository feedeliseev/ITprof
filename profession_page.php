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
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

$query = $pdo->prepare("SELECT name, short_description, full_description FROM professions WHERE id = :id");
$query->execute(['id' => $id]);
$profession = $query->fetch(PDO::FETCH_ASSOC);

if (!$profession) {
    die("Профессия не найдена");
}

// Получение списка ПВК и количества выборов
// SQL-запрос для отображения ПВК пользователям (фильтруем только с голосами)
$pvkUserQuery = $pdo->prepare(
    "SELECT pvk.id, pvk.name, COUNT(pvk_prof.userid) AS total_count 
     FROM pvk 
     LEFT JOIN pvk_prof ON pvk.id = pvk_prof.pvkid AND pvk_prof.profid = :profid 
     GROUP BY pvk.id 
     HAVING total_count > 0 
     ORDER BY total_count DESC"
);
$pvkUserQuery->execute(['profid' => $id]);
$pvkUserList = $pvkUserQuery->fetchAll();

// SQL-запрос для экспертов (полный список ПВК)
$pvkExpertQuery = $pdo->prepare(
    "SELECT pvk.id, pvk.name, 
        (SELECT COUNT(*) FROM pvk_prof WHERE pvkid = pvk.id AND profid = :profid) AS total_count
     FROM pvk
     ORDER BY total_count DESC"
);
$pvkExpertQuery->execute(['profid' => $id]);
$pvkExpertList = $pvkExpertQuery->fetchAll();


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
<main style="margin-top: 50px;">
<div class="container">
    <div class="title-block">
        <h1><?php echo htmlspecialchars($profession['name']); ?></h1>
        <img src="pictures/Web_developer.png" alt="<?php echo htmlspecialchars($profession['name']); ?>">
    </div>
    <div class="info-block">
        <p><?php echo nl2br(htmlspecialchars($profession['short_description'])); ?></p>
        <p><?php echo htmlspecialchars_decode($profession['full_description']); ?></p>
    </div>

    <?php if (!$isExpert && !empty($pvkUserList)) : ?>
        <h2>Перечень ПВК</h2>
        <ul>
            <?php foreach ($pvkUserList as $pvk) : ?>
                <li>
                    <?php echo htmlspecialchars($pvk['name']); ?> -
                    <?php echo $pvk['total_count']; ?> выборов
                    <?php if ($totalExperts > 0) : ?>
                        (средний рейтинг: <?php echo round($pvk['total_count'] / $totalExperts, 2); ?>)
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>ы
    <?php elseif (!$isExpert) : ?>
        <p>Пока ни один эксперт не выбрал ПВК для этой профессии.</p>
    <?php endif; ?>

    <?php if ($isExpert) : ?>
        <form method="post" action="/ITprof/update_pvk.php">
            <h3>Выберите ПВК для профессии (не более 10)</h3>
            <div id="pvk-container">
                <?php foreach ($pvkExpertList as $pvk) : ?>
                    <label>
                        <input type="checkbox" name="pvk[]" value="<?php echo $pvk['id']; ?>"
                               class="pvk-checkbox"
                            <?php echo in_array($pvk['id'], $expertPvk) ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($pvk['name']); ?>
                        (<?php echo $pvk['total_count']; ?> голосов)
                    </label><br>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="profid" value="<?php echo $id; ?>">
            <button type="submit" id="submit-btn">Подтвердить</button>


        </form>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const checkboxes = document.querySelectorAll(".pvk-checkbox");
                const submitButton = document.getElementById("submit-btn");

                function updateCheckboxState() {
                    const checkedCount = [...checkboxes].filter(cb => cb.checked).length;
                    checkboxes.forEach(cb => {
                        if (!cb.checked) {
                            cb.disabled = checkedCount >= 10;
                        }
                    });
                }

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener("change", updateCheckboxState);
                });

                updateCheckboxState(); // Запускаем проверку при загрузке
            });
        </script>
    <?php endif; ?>
</div>
</main>

<footer>
    <div style="width: 100%; height: 200px; background-color: #F1F3F4; display: flex; justify-content: center; align-items: center;">
        <?php if ($isAdmin) : ?>
            <a href="edit_profession.php?id=<?= $id ?>" class="edit-button">Редактировать профессию</a>
        <?php endif; ?>
    </div>
    <style>
        .edit-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 18px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }
    </style>
</footer>
<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>
</body>
</html>
