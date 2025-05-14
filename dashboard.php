<?php
session_start();
if (!isset($_SESSION["user_id"])) {
header("Location: index.php?message=" . urlencode("Войдите в систему"));
exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isExpert = isset($_SESSION['role']) && $_SESSION['role'] === 'expert';
$isConsultant = isset($_SESSION['role']) && $_SESSION['role'] === 'consultant';
$allowedRoles = ['admin', 'expert', 'consultant'];
$hasAccess = isset($_SESSION['role']) && in_array($_SESSION['role'], $allowedRoles);

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
$query3 = "
    SELECT
        tr3.test_id,
        t.test_name,
        tr3.test_date,
        tr3.mid,
        tr3.count
    FROM Test_result3 tr3
    LEFT JOIN Tests t ON tr3.test_id = t.id
    WHERE tr3.user_id = :user_id
    ORDER BY t.test_name ASC, tr3.test_date DESC
";

try {
    $stmt3 = $conn->prepare($query3);
    $stmt3->execute(['user_id' => $user_id]);
    $results3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении Test_result3: " . $e->getMessage();
    $results3 = [];
}

$query4 = "
    SELECT
        tr4.test_id,
        t.test_name,
        tr4.test_date,
        tr4.mid,
        tr4.percentage
    FROM Test_result4 tr4
    LEFT JOIN Tests t ON tr4.test_id = t.id
    WHERE tr4.user_id = :user_id
    ORDER BY t.test_name ASC, tr4.test_date DESC
";


try {
    $stmt4 = $conn->prepare($query4);
    $stmt4->execute(['user_id' => $user_id]);
    $results4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении Test_result4: " . $e->getMessage();
    $results4 = [];
}

$query6 = "
        SELECT
            tr.test_id,
            t.test_name,
            tr.test_date,
            tr.accuracy_percent
        FROM Test_result_verbalmemory tr
        LEFT JOIN Tests t ON tr.test_id = t.id
        WHERE tr.user_id = :user_id
        ORDER BY t.test_name ASC, tr.test_date DESC
    ";
try {
    $stmt6 = $conn->prepare($query6);
    $stmt6->execute(['user_id' => $user_id]);
    $results6 = $stmt6->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении Test_result_verbalmemory: " . $e->getMessage();
    $results6 = [];
}

$query5 = "
        SELECT
            tr.test_id,
            t.test_name,
            tr.test_date,
            tr.accuracy_percent,
            tr.memorization_time_sec
        FROM Test_result_visualmemory tr
        LEFT JOIN Tests t ON tr.test_id = t.id
        WHERE tr.user_id = :user_id
        ORDER BY t.test_name ASC, tr.test_date DESC
    ";
try {
    $stmt5 = $conn->prepare($query5);
    $stmt5->execute(['user_id' => $user_id]);
    $results5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении Test_result_visualmemory: " . $e->getMessage();
    $results5 = [];
}

$query7 = "
    SELECT
        tr.test_id,
        t.test_name,
        tr.test_date,
        tr.speed
    FROM Test_result_redblack tr
    LEFT JOIN Tests t ON tr.test_id = t.id
    WHERE tr.user_id = :user_id
    ORDER BY tr.test_date DESC
";

try {
    $stmt7 = $conn->prepare($query7);
    $stmt7->execute(['user_id' => $user_id]);
    $results7 = $stmt7->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении Test_result_redblack: " . $e->getMessage();
    $results7 = [];
}

// query8 — Landolt Ring
$query8 = "
    SELECT
        tr.test_id,
        t.test_name,
        tr.test_date,
        tr.percentage,
        tr.duration
    FROM Test_result_landoltring tr
    LEFT JOIN Tests t ON tr.test_id = t.id
    WHERE tr.user_id = :user_id AND tr.test_id = 13
    ORDER BY tr.test_date DESC
";
try {
    $stmt8 = $conn->prepare($query8);
    $stmt8->execute(['user_id' => $user_id]);
    $results8 = $stmt8->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении результатов Landolt Ring: " . $e->getMessage();
    $results8 = [];
}

// query9 — Raven Test
$query9 = "
    SELECT
        tr.test_id,
        t.test_name,
        tr.test_date,
        tr.percentage,
        tr.duration
    FROM Test_result_landoltring tr
    LEFT JOIN Tests t ON tr.test_id = t.id
    WHERE tr.user_id = :user_id AND tr.test_id = 14
    ORDER BY tr.test_date DESC
";
try {
    $stmt9 = $conn->prepare($query9);
    $stmt9->execute(['user_id' => $user_id]);
    $results9 = $stmt9->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении результатов Raven Test: " . $e->getMessage();
    $results9 = [];
}

$query10 = "
    SELECT
        tr.test_id,
        t.test_name,
        tr.test_date,
        tr.percent_from_answered,
        tr.percent_from_total
    FROM Test_result_voinarovski tr
    LEFT JOIN Tests t ON tr.test_id = t.id
    WHERE tr.user_id = :user_id
    ORDER BY tr.test_date DESC
";

try {
    $stmt10 = $conn->prepare($query10);
    $stmt10->execute(['user_id' => $user_id]);
    $results10 = $stmt10->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении Test_result_voinarovski: " . $e->getMessage();
    $results10 = [];
}

$query12 = "
    SELECT
        tr.test_id,
        t.test_name,
        tr.test_date,
        tr.accuracy_percent
    FROM Test_result_compasses tr
    LEFT JOIN Tests t ON tr.test_id = t.id
    WHERE tr.user_id = :user_id
    ORDER BY tr.test_date DESC
";

try {
    $stmt12 = $conn->prepare($query12);
    $stmt12->execute(['user_id' => $user_id]);
    $results12 = $stmt12->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Ошибка при получении Test_result_compasses: " . $e->getMessage();
    $results12 = [];
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
      <a href="logout.php">Выйти</a>

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

      <?php if ($hasAccess) : ?>
          <section class="admin-panel">
              <h2>Пользователи</h2>
              <a href="users.php" class="admin-button">Перейти к списку пользователей</a>
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
      <section class="test-results">
          <h2>Результаты тестов 3</h2>
          <?php if (count($results3) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Среднее (мс)</th>
                      <th>Измерений</th>
                  </tr>
                  <?php foreach ($results3 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= htmlspecialchars($row['mid']) ?></td>
                          <td><?= htmlspecialchars($row['count']) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты третьего теста отсутствуют.</p>
          <?php endif; ?>
      </section>
      <section class="test-results">
          <h2>Результаты тестов 4 (с процентом слежения)</h2>
          <?php if (count($results4) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Среднее время реакции (мс)</th>
                      <th>Время в цели (%)</th>
                  </tr>
                  <?php foreach ($results4 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= htmlspecialchars($row['mid']) ?></td>
                          <td><?= htmlspecialchars($row['percentage']) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты отсутствуют.</p>
          <?php endif; ?>
      </section>
      <section class="test-results">
          <h2>Результаты теста: Визуальная память</h2>

          <?php if (count($results5) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Точность (%)</th>
                      <th>Время запоминания (сек)</th>
                  </tr>
                  <?php foreach ($results5 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= htmlspecialchars($row['accuracy_percent']) ?></td>
                          <td><?= htmlspecialchars($row['memorization_time_sec']) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты отсутствуют.</p>
          <?php endif; ?>
      </section>
      <section class="test-results">
          <h2>Результаты теста: Вербальная память</h2>

          <?php if (count($results6) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Средняя точность (%)</th>
                  </tr>
                  <?php foreach ($results6 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= htmlspecialchars($row['accuracy_percent']) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты отсутствуют.</p>
          <?php endif; ?>
      </section>
      <section class="test-results">
          <h2>Результаты теста "Красно-черные таблицы"</h2>
          <?php if (count($results7) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Скорость (выборов/сек)</th>
                  </tr>
                  <?php foreach ($results7 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= htmlspecialchars(number_format($row['speed'], 2)) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты отсутствуют.</p>
          <?php endif; ?>
      </section>
      <section class="test-results">
          <h2>Результаты теста: Кольца Ландольта</h2>
          <?php if (count($results8) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Точность (%)</th>
                      <th>Время выполнения (сек)</th>
                  </tr>
                  <?php foreach ($results8 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= number_format($row['percentage'], 1) ?></td>
                          <td><?= number_format($row['duration'], 1) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты отсутствуют.</p>
          <?php endif; ?>
      </section>
      <section class="test-results">
          <h2>Результаты теста: Матрицы Равена</h2>
          <?php if (count($results9) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Точность (%)</th>
                      <th>Время выполнения (сек)</th>
                  </tr>
                  <?php foreach ($results9 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= number_format($row['percentage'], 1) ?></td>
                          <td><?= number_format($row['duration'], 1) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты отсутствуют.</p>
          <?php endif; ?>
      </section>
      <section class="test-results">
          <h2>Результаты теста Войнаровского</h2>
          <?php if (count($results10) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Точность (от отвеченных)</th>
                      <th>Точность (от общего)</th>
                  </tr>
                  <?php foreach ($results10 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= htmlspecialchars($row['percent_from_answered']) ?>%</td>
                          <td><?= htmlspecialchars($row['percent_from_total']) ?>%</td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты отсутствуют.</p>
          <?php endif; ?>
      </section>
      <section class="test-results">
          <h2>Результаты теста "Компасы"</h2>
          <?php if (count($results12) > 0): ?>
              <table>
                  <tr>
                      <th>Тест</th>
                      <th>Дата</th>
                      <th>Точность (%)</th>
                  </tr>
                  <?php foreach ($results12 as $row): ?>
                      <tr>
                          <td><?= htmlspecialchars($row['test_name']) ?></td>
                          <td><?= htmlspecialchars($row['test_date']) ?></td>
                          <td><?= htmlspecialchars($row['accuracy_percent']) ?>%</td>
                      </tr>
                  <?php endforeach; ?>
              </table>
          <?php else: ?>
              <p>Результаты отсутствуют.</p>
          <?php endif; ?>
      </section>
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
