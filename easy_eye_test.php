<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
$testId = 2; // ← предполагаемый ID для easy_eye_test
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Тест на реакцию (свет)</title>
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
            Этот тест считывает скорость реакции на световой сигнал. Ваша цель — нажать на круг как только он поменяет цвет.
        </div>
        <div id='Iteration' class="iteration"></div>
        <button id='Start_button' class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id='Circle' class="circle" onclick="onAnswer()"></div>
        <div id='Result' class="result"></div>
        <div id='Final Result' class="result"></div>
        <a href="easy_eye_test.php" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="easy_eye_test.js"></script>
</body>
</html>