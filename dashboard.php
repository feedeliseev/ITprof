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
        tr.average_time,
        tr.correct_answers,
        tr.test_date,
        tr.test_name
    FROM users u
    LEFT JOIN test_results tr ON u.id = tr.user_id
    WHERE u.id = :user_id
    ORDER BY tr.test_date DESC
";
try {
    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при выполнении запроса. Пожалуйста, попробуйте позже.";
    $results = [];
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
<header><div id="header-container"></div></header>
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
          <h2>Результаты тестов</h2>
          <?php if (count($results) > 0): ?>
              <table>
                  <tr>
                      <th>Test</th>
                      <th>Average Time</th>
                      <th>Correct Answers</th>
                      <th>Test Date</th>
                  </tr>
                  <?php foreach ($results as $row): ?>
                      <tr>
                          <td><?= isset($row['test_name']) ? htmlspecialchars($row['test_name']) : '-' ?></td>
                          <td><?= isset($row['average_time']) ? number_format($row['average_time'], 2) : '-' ?></td>
                          <td><?= isset($row['correct_answers']) ? htmlspecialchars($row['correct_answers']) : '-' ?></td>
                          <td><?= isset($row['test_date']) ? htmlspecialchars($row['test_date']) : '-' ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты тестов отсутствуют.</p>
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
