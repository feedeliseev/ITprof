<?php
// compasses_test.php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_test_4.css">
    <link href="/public/libs/itc-slider/itc-slider.css" rel="stylesheet">
    <script src="/public/libs/itc-slider/itc-slider.js" defer></script>
    <title>Компасы</title>
</head>
<body>
<div id="bg" class="bg_img">
    <div id="compasses_test" class="tester">
        <div id="Instruction" class="instruction text_instruction">
            С помощью теста по методике Компасы оценивается способность к пространственному и логическому мышлению.
            Вам необходимо определить в какую сторону света указывает компас по риске.
        </div>
        <button id="Settings_button" class="settings_button" name="settings" onclick="showSettings()">Настройки</button>
        <div id="Settings" class="settings_panel">
            <div class="setting_item">
                <label for="testDuration">Длительность теста (сек):</label>
                <input type="number" id="testDuration" min="10" max="2400" value="60">
            </div>
            <div class="setting_item">
                <label class="settings-label">Режим генерации заданий:</label>
                <div class="settings-radio-group">
                    <label class="settings-radio-label">
                        <input type="radio" name="generationMode" value="random" checked> Произвольный режим
                    </label>
                    <label class="settings-radio-label">
                        <input type="radio" name="generationMode" value="progressive"> От простого к сложному
                    </label>
                </div>
            </div>
            <div id="progressiveOptions" style="display: none; margin-left: 20px;">
                <div class="setting_item">
                    <label for="easyPercent">Очень простые:</label>
                    <input type="range" id="easyPercent" min="0" max="100" value="33" oninput="updatePercentages.call(this)">
                    <span id="easyPercentValue">33%</span>
                </div>
                <div class="setting_item">
                    <label for="mediumPercent">Средней сложности:</label>
                    <input type="range" id="mediumPercent" min="0" max="100" value="33" oninput="updatePercentages.call(this)">
                    <span id="mediumPercentValue">33%</span>
                </div>
                <div class="setting_item">
                    <label for="hardPercent">Сложные:</label>
                    <input type="range" id="hardPercent" min="0" max="100" value="34" oninput="updatePercentages.call(this)">
                    <span id="hardPercentValue">34%</span>
                </div>
            </div>
            <div class="setting_item">
                <input type="checkbox" id="showTimer" checked>
                <label for="showTimer">Показывать таймер</label>
            </div>
            <button onclick="hideSettings()" class="save_settings">Сохранить</button>
        </div>
        <button id="Start_button" class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id="Timer" class="iteration" style="position: sticky; top: 45px; margin-left: 89%">Время: 2m 30s</div>
        <div id="Final Result" class="result"></div>
        <div class="container container-compasses" id="container"></div>
        <button id="Finish_button" class="restart_button" name="finish" onclick="finish_test()" style="top: 5vh">Готово</button>
        <a href="/compasses_test.php" class="restart_button" id="Retry">Заново</a>
    </div>
</div>
<script src="compasses_test.js"></script>
</body>
</html>