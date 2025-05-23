<?php
session_start();

// Проверка на авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$testId = 1; // ID теста easy_audtest
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_test_1.css">
    <script>
        const USER_ID = <?= (int)$userId ?>;
        const TEST_ID = <?= (int)$testId ?>;
    </script>
</head>
<body>
<div id="bg" class="bg_img">
    <div class="top">
        <a href="tests_lab3.html">
            <img class="icon_back" src="back.png">
        </a>
        <a href="login">
            <img class="icon_menu" src="menu.png">
        </a>
    </div>
    <div class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Этот тест считывает скорость реакции на звуковой сигнал. Ваша цель — нажать на круг как только прозвучит звуковой сигнал.
        </div>
        <div id='Iteration' class="iteration"></div>
        <button id='Start_button' class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id='Circle' class="circle" onclick="onAnswer()"></div>
        <div id='Result' class="result"></div>
        <div id='Final Result' class="result"></div>
        <a href="easy_audtest.php" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="easy_aud_test.js"></script>
</body>
</html>