<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];
$testId = 3; // ID для этого теста в таблице Tests
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Множественная световая реакция</title>
    <link rel="stylesheet" href="style_test_1.css">
    <script>
        const USER_ID = <?= (int)$userId ?>;
        const TEST_ID = <?= (int)$testId ?>;
    </script>
</head>
<body>
<div id="bg" class="bg_img">
    <div class="top">
        <a href="tests_lab3.html"><img class="icon_back" src="back.png"></a>
        <a href="login"><img class="icon_menu" src="menu.png"></a>
    </div>
    <div class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Этот тест считывает скорость реакции на световой сигнал. Ваша цель — нажать на круг, как только он поменяет цвет.
        </div>
        <div id='Iteration' class="iteration"></div>
        <button id='Start_button' class="start_button" onclick="start_test()">Начать</button>
        <div id='Circle1' class="circle" onclick="onAnswer('Circle1')"></div>
        <div id='Circle2' class="circle" onclick="onAnswer('Circle2')"></div>
        <div id='Circle3' class="circle" onclick="onAnswer('Circle3')"></div>
        <div id='Circle4' class="circle" onclick="onAnswer('Circle4')"></div>
        <div id='Result' class="result"></div>
        <div id='Final Result' class="result"></div>
        <a href="med_eye_test.php" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="med_eye_test.js"></script>
</body>
</html>