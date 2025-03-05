<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>В разработке</title>
    <link rel="stylesheet" href="styles/style.css" />
</head>
<body>

<header><div id="header-container"></div></header>
<main style="margin-top: 50px;">
<div style="width: 80%; height: 300px; display: flex; justify-content: center; align-items: center;">
    <a class="auth" href="index.php">
      <div>
        <p>Назад</p>
      </div>
    </a>
</div>
    <h1>В разработке</h1>
</main>


<script>
  fetch("siteheader.php")
          .then(response => response.text())
          .then(data => document.getElementById("header-container").innerHTML = data);
</script>
</body>
</html>