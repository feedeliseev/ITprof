<?php
session_start();
if (!isset($_SESSION["user_id"])) {
header("Location: index.php?message=" . urlencode("Войдите в систему"));
exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedelisee";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES utf8mb4");
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];
$name = isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Не указано';
$email = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Не указано';
$role = isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : 'Не указано';

$query = "
    SELECT
        u.id,
        u.name,
        u.email,
        u.role,
        tr.test_date,
        tr.score_percent,
        t.test_name,
        tr.mid
    FROM users u
    LEFT JOIN Test_result tr ON u.id = tr.user_id
    LEFT JOIN Tests t ON tr.test_id = t.id
    WHERE u.id = :user_id
    ORDER BY t.test_name ASC, tr.test_date DESC
";
try {
    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при выполнении запроса. Пожалуйста, попробуйте позже.";
    $results = [];
}

$query2 = "
    SELECT
        tr2.test_id,
        t.test_name,
        tr2.test_date,
        tr2.sd
    FROM Test_result2 tr2
    LEFT JOIN Tests t ON tr2.test_id = t.id
    WHERE tr2.user_id = :user_id
    ORDER BY t.test_name ASC, tr2.test_date DESC
";

try {
    $stmt2 = $conn->prepare($query2);
    $stmt2->execute(['user_id' => $user_id]);
    $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении Test_result2: " . $e->getMessage();
    $results2 = [];
}

$conn = null;
?>



<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>ОПД</title>
  <link rel="stylesheet" href="styles/style.css" />

</head>
<body>
<header>
    <div id="header-container"></div>

</header>
<main style="margin-top: 50px;">
  <div style="display: flex; justify-content: center; align-items: center; height: 300px;" class="backimg"><h1 id="typing"></h1></div>
  <div class="container">
    <!-- Профиль -->
    <section class="profile">
      <h2>Имя пользователя</h2>
      <div class="profile-info">
        <div>
          <p><strong><?= htmlspecialchars($_SESSION['name'])?></strong></p>

        </div>
      </div>
    </section>

    <!-- Контакты (Email) -->
    <section class="contacts">
      <h2>Почта</h2>
      <p>Адрес электронной почты: <strong><?= htmlspecialchars($_SESSION['email'])?></strong></p>
    </section>


    <!-- Роль -->
    <section class="role">
      <h2>Роль</h2>
      <?= htmlspecialchars($_SESSION['role'])?>
    </section>

      <!-- Раздел для администратора -->
      <?php if ($isAdmin) : ?>
          <section class="admin-panel">
              <h2>Администрирование</h2>
              <a href="admin.php" class="admin-button">Перейти в панель администратора</a>
          </section>
      <?php endif; ?>



      <!-- Результаты тестов -->
      <section class="test-results">
          <h2>Результаты тестов 1 </h2>
          <?php if (count($results) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Результат</th>
                      <th>Дата</th>
                      <th>Среднее</th>
                  </tr>
                  <?php foreach ($results as $row): ?>
                      <tr>
                          <td><?= isset($row['test_name']) ? htmlspecialchars($row['test_name']) : '-' ?></td>
                          <td><?= isset($row['score_percent']) ? number_format($row['score_percent'], 2) . '%' : '-' ?></td>
                          <td><?= isset($row['test_date']) ? htmlspecialchars($row['test_date']) : '-' ?></td>
                          <td><?= isset($row['mid']) ? htmlspecialchars($row['mid']) : '-' ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты тестов отсутствуют.</p>
          <?php endif; ?>
      </section>

      <section class="test-results">
          <h2>Результаты тестов 2</h2>
          <?php if (count($results2) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Отклонение (SD)</th>
                  </tr>
                  <?php foreach ($results2 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= htmlspecialchars($row['sd']) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты по новым тестам отсутствуют.</p>
          <?php endif; ?>
      </section>
      <a href="logout.php">Выйти</a>
  </div>

  </div>
</main>
<script>
  fetch("siteheader.php")
          .then(response => response.text())
          .then(data => document.getElementById("header-container").innerHTML = data);

</script>

<script>
  const text = "Личный кабинет";
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

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 16px;
        background-color: #ffffffdd;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 12px 18px;
        text-align: left;
    }

    th {
        background-color: #f51cf1;
        color: white;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    tr:nth-child(even) {
        background-color: #f7f9fc;
    }

    tr:hover {
        background-color: #eef2f7;
        transition: background-color 0.2s;
    }

    td {
        color: #333;
    }
</style>
</body>
</html>
