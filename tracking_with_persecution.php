<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];
$testId = 9; // ID easy_moving_test в таблице Tests
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_test_3.css">
    <script>
        const USER_ID = <?= (int)$userId ?>;
        const TEST_ID = <?= (int)$testId ?>;
    </script>
</head>
<body>
<div id="bg" class="bg_img">
    <div id ='tracking_with_persecution' class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Этот тест считывает вашу реакцию на случайное перемещение мишени. Ваша цель - преследовать цель за мишенью.
        </div>
         <div id='Iteration' class="iteration"></div>
               <button id='Settings_button' class="settings_button" name="settings" onclick="showSettings()">Настройки</button>
        <div id="Settings" class="settings_panel">
            <div class="setting_item">
                <label for="testDuration">Длительность теста (мин):</label>
                <input type="number" id="testDuration" min="2" max="40" value="1">
            </div>
            <div class="setting_item">  
                <label for="accelerationInterval">Базовое ускорение:</label>
                <input type="number" id="accelerationInterval" min="0.5" max="3.0" value="1.0">
            </div>
            <div class="setting_item">
                <label for="accelerationStep">Изменение ускорения:</label>
                <input type="number" id="accelerationStep" min="0" max="2" step="0.1" value="0.5">
            </div>

            <div class="setting_item">
                <label class="settings-label">Частота изменений:</label>
                <div class="settings-radio-group">
                    <label class="settings-radio-label">
                        <input type="radio" name="changeType" value="random" checked> Случайно</label>
                    <label class="settings-radio-label">
                        <input type="radio" name="changeType" value="fixed"> Через каждые 
                        <input type="number" id="changeInterval" value="10" min="5" max="60" style="width: 50px;"> сек
                    </label>
                </div>
            </div>
            <div class="setting_item">
                <input type="checkbox" id="showTimer" checked>
                <label for="showTimer">Показывать таймер</label>
            </div>
            <div class="setting_item">
                <input type="checkbox" id="showProgress" checked>
                <label for="showProgress">Показывать прогресс</label>
            </div>
            <div class="setting_item">
                <input type="checkbox" id="showMinuteResults" checked>
                <label for="showMinuteResults">Показывать результаты по минутам</label>
            </div>
            <button onclick="hideSettings()" class="save_settings">Сохранить</button>
        </div>
        <button id='Start_button' class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id='Target' class="circle"></div>
        <div id='Crosshair' class="crosshair"></div>
        <div id='Timer' class="iteration">20s</div>
        <div id='Score' class="iteration">Score: 0</div>
        <div id='Upper_marker' class="rectangle"></div>
        <div id='Lower_marker' class="rectangle"></div>
        <div id='Final Result' class="result"></div>
        <a href="/tracking_with_persecution" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="tracking_with_persecution.js"></script>
</body>
</html>