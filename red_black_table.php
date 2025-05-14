
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];
$testId = 12;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Red Black Table</title>
    <link rel="stylesheet" href="style_test_4.css">
    <script>
        const USER_ID = <?= (int)$userId ?>;
        const TEST_ID = <?= (int)$testId ?>;
    </script>
</head>
<body>
<div id="bg" class="bg_img">
    <div id='red_black_table' class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Этот тест оценивает внимание. 
            Вам необходимо выбрать в возрастающем порядке числа в красных квадратах и в убывающем порядке числа в чёрных квадратах.
            <br>1 пара:(1 - <b style="color: red">красный</b>, 24 - <b style="color: black; background-color: white">черный</b>)<br>2 пара:(2 - <b style="color: red">красный</b>, 23 - <b style="color: black; background-color: white">черный</b>)
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
                <label for="showTimer">Показывать таймер</label>
            </div>
            <div class="setting_item">
                <input type="checkbox" id="showMinuteResults" checked>
                <label for="showMinuteResults">Показывать результаты по минутам</label>
            </div>
            <button onclick="hideSettings()" class="save_settings">Сохранить</button>
        </div>
        <button id='Start_button' class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id="Choosing_instruction" class="choosing_instruction text_choosing_instruction"></div>
        <div id='Timer' class="iteration">0s</div>
        <div id='Final Result' class="result"></div>
        <div id='Scene' class="scene">
            <?php for($i = 1; $i <= 25; $i++): ?>
                <div class="cell-red" val="<?= $i ?>" onclick="cell(<?= $i ?>)"><?= $i ?></div>
            <?php endfor; ?>
            <?php for($i = 1; $i <= 24; $i++): ?>
                <div class="cell-black" val="<?= $i ?>" onclick="cell(<?= $i ?>)"><?= $i ?></div>
            <?php endfor; ?>
        </div>
        <a href="red_black_table.php" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="red_black_table.js"></script>
</body>
</html>