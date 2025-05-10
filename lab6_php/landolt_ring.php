<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_test_4.css">
    <title>Кольца Ландольта</title>
</head>
<body>
<div id="bg" class="bg_img">
    <div id ='landolt_ring' class="tester">
        <div id='Instruction' class="instruction text_instruction">
            Этот тест оценивает ваше внимание. Вам необходимо выбрать все кольца соответствующие с указаным.
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
            <button onclick="hideSettings()" class="save_settings">Сохранить</button>
        </div>
        <button id='Start_button' class="start_button" name="start" onclick="start_test()">Начать</button>
        <div id="Choosing_instruction" class="choosing_instruction text_choosing_instruction"></div>
        <div id="Choosing_img" class="choosing_instruction text_choosing_instruction" style="background-color: white; width: 7vh; height: 7vh; left: 46.5vw"></div>
        <div id='Timer' class="iteration">0s</div>
        <div id='Final Result' class="result"></div>
        <div id='Scene' class="scene" style="top: 6vh">
            <?php 
            $directions = ['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'];
            foreach($directions as ('/img/'+$dir): 
                for($i = 1; $i <= 8; $i++): 
                    $id = (array_search($dir, $directions) * 8) + $i;
            ?>
                <div class="cell" id="<?= $id ?>" onclick="cell(<?= array_search($dir, $directions) + 1 ?>, <?= $id ?>)">
                    <img src="<?= $dir ?>.png" class="cell_img">
                </div>
            <?php endfor; endforeach; ?>
        </div>
        <button id='Finish_button' class="restart_button" name="finish" onclick="finish_test()" style="top: 8vh">Готово</button>        
        <a href="/landolt_ring" class="restart_button" id='Retry'>Заново</a>
    </div>
</div>
<script src="landolt_ring.js"></script>
</body>
</html>