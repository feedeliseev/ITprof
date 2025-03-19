<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Страница в разработке</title>
    <link rel="stylesheet" href="styles/style.css" />
    <style>
        body {
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;

        }

        h1 {
            font-size: 26px;
            color: #333;
            margin-bottom: 10px;
        }

        p {
            font-size: 18px;
            color: #666;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            font-size: 18px;
            text-decoration: none;
            background-color: #F1F3F4; /*молочно-бежевый с прозрачностью*/
            color: black;
            border-radius: 5px;
            transition: 0.3s ease;
            border: 2px solid #D3D3D3;
            border-radius: 50px;

        }

        .back-button:hover {
            background-color: Gainsboro;
            border-color: black;

        }

        .construction-icon {
            font-size: 50px;
            color: #007BFF;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<header><div id="header-container"></div></header>

<div class="container">
    <div class="construction-icon">🚧</div>
    <h1>Страница в разработке</h1>
    <p>Когда-нибудь здесь что-нибудь будет.</p>
    <a class="back-button" href="index.php">Назад</a>
</div>

<script>
    fetch("siteheader.php")
        .then(response => response.text())
        .then(data => document.getElementById("header-container").innerHTML = data);
</script>

</body>
</html>