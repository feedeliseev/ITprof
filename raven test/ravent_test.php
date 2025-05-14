<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];
$testId = 14;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_test_4.css">
    <title>Матрицы Равена</title>
    <script>
        const USER_ID = <?= (int)$userId ?>;
        const TEST_ID = 14;
    </script>
</head>
<body>
<div id="bg" class="bg_img">
    <div id='raven_test' class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Вам предлагается ряд картинок. На каждом рисунке недостает одной фигуры, Вам необходимо выбрать один из
            вариантов предложенных фигур, которая на ваш взгляд подходит к рисунку больше всего.
        </div>
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
                <label for="showTimer">Показывать таймер</label>
            </div>
            <button onclick="hideSettings()" class="save_settings">Сохранить</button>
        </div>
        <button id='Start_button' class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id='Final Result' class="result"></div>
        <div id='Scene' class="scene raven-scene" style="top: 6vh">
            <?php
            // Генерация изображений матриц Равена
            $sets = ['A', 'B', 'C', 'D', 'E'];
            foreach($sets as $set) {
                for($i = 1; $i <= 12; $i++) {
                    $zIndex = 60 - (array_search($set, $sets) * 12) - $i;
                    echo '<img src="'.$set.$i.'.png" style="position: absolute; z-index: '.$zIndex.'">';
                }
            }
            ?>
        </div>
        <a href="ravent_test.php" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="raven_test.js"></script>
</body>
</html>