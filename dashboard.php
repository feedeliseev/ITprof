<?php
session_start();
if (!isset($_SESSION["user_id"])) {
header("Location: index.php?message=" . urlencode("Войдите в систему"));
exit();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
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
</body>
</html>
