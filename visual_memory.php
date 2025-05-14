<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id'];
$testId = 10; // ID для visual_memory в таблице Tests
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_test_4.css">
    <title>Визуальная память</title>
    <script>
        const USER_ID = <?= (int)$userId ?>;
        const TEST_ID = <?= (int)$testId ?>;
    </script>
</head>
<body>
<div id="bg" class="bg_img">
    <div id ='visual_memory' class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Этот тест оценивает вашу визуальную память. Вам необходимо запомнить какие ячейки загорелись и кликнуть на них.
        </div>
        <button id='Settings_button' class="settings_button" name="settings" onclick="showSettings()">Настройки</button>
        <div id="Settings" class="settings_panel">
            <div class="setting_item">
                <label for="memorizationTime">Время запоминания (сек):</label>
                <input type="number" id="memorizationTime" min="1" max="60" value="3">
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

            <button onclick="hideSettings()" class="save_settings">Сохранить</button>
        </div>
        <button id='Start_button' class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id="Choosing_instruction" class="choosing_instruction text_choosing_instruction"></div>
        <div id="Choosing_img" class="choosing_instruction text_choosing_instruction" style="background-color: white; width: 5vh; height: 5vh; left: 48vw"></div>
        <div id='Final Result' class="result"></div>
        <div id='Scene' class="scene" style="top: 6vh">
            <?php for($i = 1; $i <= 64; $i++): ?>
                <div class="cell" id="<?= $i ?>" onclick="cell(<?= $i ?>)"></div>
            <?php endfor; ?>
        </div>
        <button id='Finish_button' class="restart_button" name="finish" onclick="finish_test()" style="top: 10vh">Готово</button>
        <a href="visual_memory.php" class="restart_button" id='Retry'style="top: 10vh">Заново</a>
    </div>
</div>
<script src="visual_memory.js"></script>
</body>
</html>