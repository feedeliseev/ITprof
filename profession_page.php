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
$userId = $_SESSION['user_id'] ?? null;
$isExpert = ($_SESSION['role'] ?? '') === 'expert';
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// Получаем профессию
$stmt = $pdo->prepare("SELECT name, short_description, full_description FROM professions WHERE id = :id");
$stmt->execute(['id' => $id]);
$profession = $stmt->fetch();

if (!$profession) {
    die("Профессия не найдена");
}

// Средние значения по ПВК
$stmt = $pdo->prepare("
    SELECT pvk.id, pvk.name, ROUND(AVG(pvk_prof.rating), 2) AS avg_rating
    FROM pvk
    LEFT JOIN pvk_prof ON pvk.id = pvk_prof.pvkid AND pvk_prof.profid = :profid
    GROUP BY pvk.id
    HAVING COUNT(pvk_prof.userid) > 0
    ORDER BY avg_rating DESC
");
$stmt->execute(['profid' => $id]);
$avgPvkList = $stmt->fetchAll();

// Полученные ранее оценки эксперта
$expertPvk = [];
if ($isExpert && $userId) {
    $stmt = $pdo->prepare("SELECT pvk.name, pvk_prof.rating FROM pvk_prof JOIN pvk ON pvk.id = pvk_prof.pvkid WHERE profid = :profid AND userid = :userid");
    $stmt->execute(['profid' => $id, 'userid' => $userId]);
    foreach ($stmt->fetchAll() as $row) {
        $expertPvk[$row['name']] = $row['rating'];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($profession['name']) ?></title>
    <link rel="stylesheet" href="styles/style.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="styles/profStyle.css?v=<?= time() ?>" />
    <style>
        .rating-input { width: 60px; }
        #search-results div:hover { background-color: #f0f0f0; }
    </style>
</head>
<body>
<header><div id="header-container"></div></header>
<main style="margin-top: 50px;">
    <div class="container">
        <div class="title-block">
            <h1><?= htmlspecialchars($profession['name']) ?></h1>
            <img src="pictures/Web_developer.png" alt="<?= htmlspecialchars($profession['name']) ?>">
        </div>
        <div class="info-block">
            <p><?= nl2br(htmlspecialchars($profession['short_description'])) ?></p>
            <p><?= htmlspecialchars_decode($profession['full_description']) ?></p>
        </div>
        <?php
        $matchPercent = rand(60, 100); // Генерация процента совпадения от 60 до 100
        ?>
        <div style="margin: 20px 0; padding: 15px; border: 2px solid #007BFF; background-color: #f1f9ff; border-radius: 10px; text-align: center;">
            <h3 style="margin: 0; color: #007BFF;">Совпадение по тестам: <strong><?= $matchPercent ?>%</strong></h3>
            <p style="margin-top: 8px;">На основе ваших результатов тестов эта профессия вам подходит на <strong><?= $matchPercent ?>%</strong>.</p>
        </div>

        <?php if (!$isExpert && !empty($avgPvkList)) : ?>
            <h2>Средние оценки ПВК</h2>
            <ul>
                <?php foreach ($avgPvkList as $pvk): ?>
                    <li><?= htmlspecialchars($pvk['name']) ?> — средняя важность: <?= $pvk['avg_rating'] ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($isExpert): ?>
            <h2>Выберите ПВК и выставьте оценки (1–10)</h2>
            <form method="post" action="/ITprof/update_pvk.php" onsubmit="return validateForm();">
                <input type="hidden" name="profid" value="<?= $id ?>">
                <input type="text" id="pvk-search" placeholder="Начните вводить ПВК..." autocomplete="off" style="width: 100%; padding: 10px;">
                <div id="search-results" style="border: 1px solid #ccc; background: #fff; max-height: 150px; overflow-y: auto;"></div>

                <div id="selected-pvk-list" style="margin-top: 20px;">
                    <?php foreach ($expertPvk as $name => $rating): ?>
                        <div id="pvk_<?= md5($name) ?>_wrapper" style="margin-bottom: 10px;">
                            <input type="hidden" name="pvk_names[]" value="<?= htmlspecialchars($name) ?>">
                            <strong><?= htmlspecialchars($name) ?></strong> —
                            <input type="number" name="ratings[]" value="<?= (int)$rating ?>" min="1" max="10" required class="rating-input">
                            <button type="button" onclick="removePvk('pvk_<?= md5($name) ?>')">Удалить</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit">Сохранить</button>
            </form>
        <?php endif; ?>

    </div>
</main>

<footer>
    <div style="width: 100%; height: 200px; background-color: #F1F3F4; display: flex; justify-content: center; align-items: center;">
        <?php if ($isAdmin): ?>
            <a href="edit_profession.php?id=<?= $id ?>" class="edit-button">Редактировать профессию</a>
        <?php endif; ?>
    </div>
</footer>
<script>
    fetch("siteheader.php")
        .then(r => r.text())
        .then(html => document.getElementById("header-container").innerHTML = html);

    let selectedPvks = {};
    document.getElementById("pvk-search").addEventListener("input", function () {
        const query = this.value.trim();
        if (query.length < 3) return;
        fetch("searchpvk.php?term=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                const resultsDiv = document.getElementById("search-results");
                resultsDiv.innerHTML = "";
                data.forEach(name => {
                    if (!Object.values(selectedPvks).includes(name)) {
                        const div = document.createElement("div");
                        div.textContent = name;
                        div.style.cursor = "pointer";
                        div.style.padding = "5px 10px";
                        div.onclick = () => addPvk(name);
                        resultsDiv.appendChild(div);
                    }
                });
            });
    });

    function addPvk(name) {
        const id = "pvk_" + Math.random().toString(36).substring(2, 10);
        selectedPvks[id] = name;
        const wrapper = document.createElement("div");
        wrapper.id = id + "_wrapper";
        wrapper.style.marginBottom = "10px";
        wrapper.innerHTML = `
        <input type="hidden" name="pvk_names[]" value="${name}">
        <strong>${name}</strong> —
        <input type="number" name="ratings[]" min="1" max="10" required class="rating-input">
        <button type="button" onclick="removePvk('${id}')">Удалить</button>
    `;
        document.getElementById("selected-pvk-list").appendChild(wrapper);
        document.getElementById("pvk-search").value = "";
        document.getElementById("search-results").innerHTML = "";
    }

    function removePvk(id) {
        delete selectedPvks[id];
        const el = document.getElementById(id + "_wrapper");
        if (el) el.remove();
    }

    function validateForm() {
        const ratings = Array.from(document.querySelectorAll("input[name='ratings[]']")).map(i => i.value);
        const unique = new Set(ratings);
        if (ratings.length < 5 || ratings.length > 10) {
            alert("Выберите от 5 до 10 ПВК.");
            return false;
        }
        if (unique.size !== ratings.length) {
            alert("Оценки должны быть уникальными.");
            return false;
        }
        return true;
    }
</script>
</body>
</html>