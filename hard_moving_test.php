<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];
$testId = 7; // ID для hard_moving_test
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Hard Moving Test</title>
    <link rel="stylesheet" href="style_test_2.css">
    <script>
        const USER_ID = <?= (int)$userId ?>;
        const TEST_ID = <?= (int)$testId ?>;
    </script>
</head>
<body>
<div id="bg" class="bg_img">
    <div class="top">
        <a href="/ITprof/tests_lab3.html">
            <img class="icon_back" src="back.png">
        </a>

    </div>
    <div class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Этот тест считывает скорость реакции на движущийся объект. Ваша цель — нажимать на клавиши
            <b style="color: green">A</b>, <b style="color: red">S</b> или <b style="color: purple">D</b> —
            когда зелёный, красный или фиолетовый круг соответственно совпадёт с белым.
        </div>
        <div id='Iteration' class="iteration"></div>
        <button id='Settings_button' class="settings_button" onclick="showSettings()">Настройки</button>
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
                        <input type="radio" name="changeType" value="random" checked> Случайно
                    </label>
                    <label class="settings-radio-label">
                        <input type="radio" name="changeType" value="fixed"> Каждые
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
        <button id='Start_button' class="start_button" onclick="start_test()">Начать</button>
        <div class="moving-test" id="movingTest">
            <div class="moving-test__moving-dot-wrapper moving-test__moving-dot-wrapper_speed_low" id='slowMovingDot'>
                <div class="moving-test__moving-dot" style="background-color: green"></div>
            </div>
            <div class="moving-test__moving-dot-wrapper" id='middleMovingDot'>
                <div class="moving-test__moving-dot" style="background-color: red"></div>
            </div>
            <div class="moving-test__moving-dot-wrapper moving-test__moving-dot-wrapper_speed_high" id='fastMovingDot'>
                <div class="moving-test__moving-dot" style="background-color: purple"></div>
            </div>
            <div class="moving-test__fixed-dot" id="fixedDot"></div>
            <div class="moving-test__fixed-dot1" id="fixedDot1"></div>
            <div class="moving-test__fixed-dot2" id="fixedDot2"></div>
        </div>
        <div id='Result' class="result"></div>
        <a href="dashboard.php" class="restart_button" id='Resultcab'>Все результаты</a>
        <a href="/hard_moving_test.php" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="hard_moving_test.js"></script>
</body>
</html>