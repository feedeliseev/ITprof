<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];
$testId = 5; // ID для sum_eye_test
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сложение глазами</title>
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
            Этот тест считывает скорость сложения чисел в уме и определение результата как чётное или нечётное число.
        </div>
        <div id='Iteration' class="iteration"></div>
        <button id='Start_button' class="start_button" onclick="start_test()">Начать</button>
        <div id="Calc" class="choosing_instruction text_choosing_instruction"></div>
        <button id="Even" class="number_button" onclick="onAnswer(0)">Чёт (F)</button>
        <button id="Odd" class="number_button" onclick="onAnswer(1)">Нечёт (J)</button>
        <div id='Result' class="result"></div>
        <div id='Final Result' class="result"></div>
        <a href="sum_eye_test.php" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="sum_eye_test.js"></script>
</body>
</html>