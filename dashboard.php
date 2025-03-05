<?php
session_start();
if (!isset($_SESSION["user_id"])) {
header("Location: index.php?message=" . urlencode("Войдите в систему"));
exit();
}
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
<main style="margin-top: 50px">
  <div style="display: flex; justify-content: center; align-items: center; height: 300px;" class="backimg"><h1 id="typing"></h1></div>
  <div class="container">
    <!-- Профиль -->
    <section class="profile">
      <h2>Профиль</h2>
      <div class="profile-info">
        <img src="pictures/Sys_admin.png" alt="User Avatar" class="avatar">
        <div>
          <p><strong><?= htmlspecialchars($_SESSION['name'])?></strong></p>

        </div>
      </div>
      <a href="#" class="edit">Изменить</a>
    </section>

    <!-- Контакты (Email) -->
    <section class="contacts">
      <h2>Почта</h2>
      <p>Основной адрес электронной почты: <strong><?= htmlspecialchars($_SESSION['email'])?></strong></p>
      <a href="#" class="edit">Изменить</a>
    </section>


    <!-- Роль -->
    <section class="role">
      <h2>Пароль</h2>
      <?= htmlspecialchars($_SESSION['role'])?>
      <a href="#" class="change">Изменить</a>
    </section>
      <a href="logout.php">Выйти</a>
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
