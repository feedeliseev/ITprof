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

// Получение среднего рейтинга ПВК
$pvkUserQuery = $pdo->prepare(
    "SELECT pvk.id, pvk.name, ROUND(AVG(pvk_prof.rating), 2) AS avg_rating
     FROM pvk
     LEFT JOIN pvk_prof ON pvk.id = pvk_prof.pvkid AND pvk_prof.profid = :profid
     GROUP BY pvk.id
     HAVING COUNT(pvk_prof.userid) > 0
     ORDER BY avg_rating DESC"
);
$pvkUserQuery->execute(['profid' => $id]);
$pvkUserList = $pvkUserQuery->fetchAll();

$pvkExpertQuery = $pdo->query("SELECT id, name FROM pvk ORDER BY name");
$pvkExpertList = $pvkExpertQuery->fetchAll();

$expertPvk = [];
if ($isExpert) {
    $expertPvkQuery = $pdo->prepare("SELECT pvkid, rating FROM pvk_prof WHERE profid = :profid AND userid = :userid");
    $expertPvkQuery->execute(['profid' => $id, 'userid' => $userId]);
    foreach ($expertPvkQuery->fetchAll() as $row) {
        $expertPvk[$row['pvkid']] = $row['rating'];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($profession['name']); ?></title>
    <link rel="stylesheet" href="styles/style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="styles/profStyle.css?v=<?php echo time(); ?>" />
    <style>
        .rating-input {
            width: 50px;
            text-align: center;
        }
    </style>
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
            <h2>Средние оценки ПВК</h2>
            <ul>
                <?php foreach ($pvkUserList as $pvk) : ?>
                    <li>
                        <?php echo htmlspecialchars($pvk['name']); ?> —
                        средняя важность: <?php echo $pvk['avg_rating']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif (!$isExpert) : ?>
            <p>Пока ни один эксперт не оценил ПВК для этой профессии.</p>
        <?php endif; ?>

        <?php if ($isExpert) : ?>
            <form method="post" action="/ITprof/update_pvk.php" onsubmit="return validateRatings();">
                <h3>Выберите до 10 ПВК и выставьте им важность (1–10)</h3>
                <div id="pvk-container">
                    <?php foreach ($pvkExpertList as $pvk) : ?>
                        <?php
                        $checked = isset($expertPvk[$pvk['id']]);
                        $rating = $checked ? $expertPvk[$pvk['id']] : '';
                        ?>
                        <div style="margin-bottom: 10px;">
                            <label>
                                <input type="checkbox" class="pvk-checkbox" name="pvk[<?php echo $pvk['id']; ?>]" <?php echo $checked ? 'checked' : ''; ?>>
                                <?php echo htmlspecialchars($pvk['name']); ?>
                            </label>
                            &nbsp;
                            <input type="number" name="rating[<?php echo $pvk['id']; ?>]" min="1" max="10" class="rating-input" value="<?php echo $rating; ?>" <?php echo $checked ? '' : 'disabled'; ?>>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="profid" value="<?php echo $id; ?>">
                <button type="submit" id="submit-btn">Сохранить</button>
            </form>

            <script>
                function validateRatings() {
                    const checked = document.querySelectorAll('.pvk-checkbox:checked');
                    if (checked.length < 5 || checked.length > 10) {
                        alert('Выберите от 5 до 10 ПВК');
                        return false;
                    }

                    const ratings = [];
                    checked.forEach(cb => {
                        const ratingInput = cb.closest('div').querySelector('.rating-input');
                        if (ratingInput) {
                            ratings.push(ratingInput.value);
                        }
                    });

                    const uniqueRatings = new Set(ratings);
                    if (uniqueRatings.size !== ratings.length) {
                        alert('Каждой ПВК должна быть присвоена уникальная оценка (без повторений).');
                        return false;
                    }

                    return true;

                }

                document.querySelectorAll('.pvk-checkbox').forEach(cb => {
                    cb.addEventListener('change', function () {
                        const ratingInput = this.closest('div').querySelector('.rating-input');
                        if (this.checked) {
                            ratingInput.disabled = false;
                            if (!ratingInput.value) ratingInput.value = 5;
                        } else {
                            ratingInput.disabled = true;
                            ratingInput.value = '';
                        }
                    });
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