<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_test_4.css">
    <title>Вербальная память</title>
</head>
<body>
<div id="bg" class="bg_img">
    <div id ='verbal_memory' class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Этот тест оценивает вашу память на цифры. Вам необходимо запомнить как можно больше цифр и написать их в любом порядке.
        </div>
        <div id='Iteration' class="iteration"></div>
        <button id='Settings_button' class="settings_button" name="settings" onclick="showSettings()">Настройки</button>
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
                    <input type="range" id="easyPercent" min="0" max="100" value="33" oninput="updatePercentages()">
                    <span id="easyPercentValue">33%</span>
                </div>
                <div class="setting_item">
                    <label for="mediumPercent">Средней сложности:</label>
                    <input type="range" id="mediumPercent" min="0" max="100" value="33" oninput="updatePercentages()">
                    <span id="mediumPercentValue">33%</span>
                </div>
                <div class="setting_item">
                    <label for="hardPercent">Сложные:</label>
                    <input type="range" id="hardPercent" min="0" max="100" value="34" oninput="updatePercentages()">
                    <span id="hardPercentValue">34%</span>
                </div>
            </div>

            <div class="setting_item">
                <input type="checkbox" id="showTimer" checked>
                <label for="showTimer"></label>
            </div>
            <button onclick="hideSettings()" class="save_settings">Сохранить</button>
        </div>
        <button id='Start_button' class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id='Final Result' class="result"></div>
        <div id='Scene' class="scene">
            <?php for($i = 1; $i <= 16; $i++): ?>
                <div class="cell_verbal" id="cell<?= $i ?>"><?= $i ?></div>
            <?php endfor; ?>
        </div>
        <button id='Finish_button' class="restart_button" name="finish" onclick="onAnswer()" style="top: 10vh">Готово</button>
        <a href="/verbal_memory" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="verbal_memory.js"></script>
</body>
</html>