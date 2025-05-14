const elements = {
    settings: document.getElementById('Settings'),
    startButton: document.getElementById('Start_button')
};

const REFRESH_INTERVAL = 100; // Обновление таймера каждые 100 мс
let testDuration = 60; // Значение по умолчанию (60 секунд)
let testTimer;
let updateTimer;

// Правильные последовательности для черных чисел (в убывающем порядке)
const correctSequences = {
    easy: [12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
    medium: [18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1],
    hard: [24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1]
};

let currentDifficulty = 'medium';
let isDuringTest = false;
let timePassed = 0;
let initTime = 0;
let cursor = 0;
let score = 0;

function showSettings() {
    elements.settings.style.display = 'block';
    elements.startButton.style.display = 'none';
}

function hideSettings() {
    testDuration = parseInt(document.getElementById('testDuration').value);
    elements.settings.style.display = 'none';
    elements.startButton.style.display = 'block';
}

document.querySelectorAll('input[name="generationMode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('progressiveOptions').style.display =
            this.value === 'progressive' ? 'block' : 'none';
    });
});

function updatePercentages() {
    const easy = parseInt(document.getElementById('easyPercent').value);
    const medium = parseInt(document.getElementById('mediumPercent').value);
    const hard = parseInt(document.getElementById('hardPercent').value);

    const total = easy + medium + hard;

    if (total !== 100) {
        const scale = 100 / total;
        document.getElementById('easyPercent').value = Math.round(easy * scale);
        document.getElementById('mediumPercent').value = Math.round(medium * scale);
        document.getElementById('hardPercent').value = Math.round(hard * scale);
    }

    document.getElementById('easyPercentValue').textContent = document.getElementById('easyPercent').value + '%';
    document.getElementById('mediumPercentValue').textContent = document.getElementById('mediumPercent').value + '%';
    document.getElementById('hardPercentValue').textContent = document.getElementById('hardPercent').value + '%';
}

function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

function getRandomDifficulty() {
    const rand = Math.random();
    const easyPercent = parseInt(document.getElementById('easyPercent').value) / 100;
    const mediumPercent = parseInt(document.getElementById('mediumPercent').value) / 100;

    if (rand < easyPercent) return 'easy';
    if (rand < easyPercent + mediumPercent) return 'medium';
    return 'hard';
}

function createCells(difficulty) {
    const scene = document.getElementById('Scene');
    scene.innerHTML = '';

    let maxNumber;

    switch(difficulty) {
        case 'easy': maxNumber = 12; break;
        case 'medium': maxNumber = 18; break;
        case 'hard': maxNumber = 24; break;
        default: maxNumber = 18;
    }

    // Создаем красные ячейки (1..maxNumber)
    for (let i = 1; i <= maxNumber; i++) {
        const redCell = document.createElement('div');
        redCell.className = 'cell-red';
        redCell.setAttribute('val', i);
        redCell.textContent = i;
        redCell.onclick = function() { cell(i); };
        scene.appendChild(redCell);
    }

    // Создаем черные ячейки (1..maxNumber)
    for (let i = 1; i <= maxNumber; i++) {
        const blackCell = document.createElement('div');
        blackCell.className = 'cell-black';
        blackCell.setAttribute('val', i);
        blackCell.textContent = i;
        blackCell.onclick = function() { cell(i); };
        scene.appendChild(blackCell);
    }

    // Перемешиваем ячейки
    const cells = Array.from(scene.children);
    shuffle(cells);

    scene.innerHTML = '';
    cells.forEach(cell => scene.appendChild(cell));

    return maxNumber;
}

function start_test() {
    testDuration = parseInt(document.getElementById('testDuration').value) || 60;
    currentDifficulty = getRandomDifficulty();

    isDuringTest = true;
    document.getElementById('Settings_button').style.display = 'none';

    const showTimer = document.getElementById('showTimer').checked;
    document.getElementById('Timer').style.display = showTimer ? 'flex' : 'none';

    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Choosing_instruction').style.display = 'block';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('Final Result').style.display = 'none';
    document.getElementById('Retry').style.display = 'none';

    document.getElementById('Choosing_instruction').innerHTML =
        `<b style="color: red">Красные на увеличение!</b>
         <b style="color: black"> Черные на уменьшение</b><br>
         Уровень: ${currentDifficulty === 'easy' ? 'Легкий' :
            currentDifficulty === 'medium' ? 'Средний' : 'Сложный'}`;

    document.getElementById('Scene').style.display = 'block';
    createCells(currentDifficulty);

    timePassed = 0;
    cursor = 0;
    score = 0;
    initTime = new Date().getTime();

    testTimer = setTimeout(finish_test, testDuration * 1000);

    if (showTimer) {
        updateTimer = setInterval(function() {
            let now = new Date().getTime();
            timePassed = (now - initTime) / 1000;
            document.getElementById("Timer").innerHTML = timePassed.toFixed(1) + "s";
        }, REFRESH_INTERVAL);
    }
}

function cell(val) {
    if (!isDuringTest) return;

    const isRedCell = event.target.classList.contains('cell-red');
    const isBlackCell = event.target.classList.contains('cell-black');

    // Определяем, какой тип ячейки ожидается следующим
    const isRedExpected = cursor % 2 === 0; // Четные шаги - красные, нечетные - черные

    if (isRedCell && isRedExpected) {
        // Проверяем красную ячейку (должна быть следующей по порядку)
        const expectedRed = Math.floor(cursor / 2) + 1;
        if (val === expectedRed) {
            score++;
            event.target.style.visibility = 'hidden';
            cursor++;
        }
    } else if (isBlackCell && !isRedExpected) {
        // Проверяем черную ячейку (должна быть следующей в убывающей последовательности)
        const sequenceIndex = Math.floor(cursor / 2);
        const expectedBlack = correctSequences[currentDifficulty][sequenceIndex];
        if (val === expectedBlack) {
            score++;
            event.target.style.visibility = 'hidden';
            cursor++;
        }
    }
}

function finish_test() {
    isDuringTest = false;
    clearTimeout(testTimer);
    clearInterval(updateTimer);

    const accuracy = (score / (cursor || 1)) * 100;
    const speed = score / timePassed;

    let resultsHTML = `
        <div class="results-container" style="color: black;">
            <h3 style="color: black;">Результаты теста "Красно-черные таблицы"</h3>
            <p style="color: black;">Уровень сложности: ${currentDifficulty === 'easy' ? 'Легкий' : currentDifficulty === 'medium' ? 'Средний' : 'Сложный'}</p>
            <table class="results-table">
                <tr>
                    <th style="color: black;">Продолжительность</th>
                    <td style="color: black;">${timePassed.toFixed(1)} сек</td>
                </tr>
                <tr>
                    <th style="color: black;">Всего выборов</th>
                    <td style="color: black;">${cursor}</td>
                </tr>
                <tr>
                    <th style="color: black;">Правильных выборов</th>
                    <td style="color: black;">${score}</td>
                </tr>
                <tr>
                    <th style="color: black;">Точность</th>
                    <td style="color: black;">${accuracy.toFixed(1)}%</td>
                </tr>
                <tr>
                    <th style="color: black;">Скорость</th>
                    <td style="color: black;">${speed.toFixed(2)} выборов/сек</td>
                </tr>
            </table>
    `;

    if (timePassed > 60 && document.getElementById('showMinuteResults').checked) {
        resultsHTML += `<h4 style="color: black;">Производительность по минутам</h4><table class="minute-results">`;

        const minutes = Math.ceil(timePassed / 60);
        for (let i = 0; i < minutes; i++) {
            const minuteSpeed = cursor > 0 ? (score / timePassed).toFixed(2) : 'Нет данных';
            resultsHTML += `
                <tr class="minute-header">
                    <th colspan="2" style="color: black;">Минута ${i + 1}</th>
                </tr>
                <tr>
                    <td style="color: black;">Правильных выборов</td>
                    <td style="color: black;">${score}</td>
                </tr>
                <tr>
                    <td style="color: black;">Скорость</td>
                    <td style="color: black;">${minuteSpeed} выборов/сек</td>
                </tr>
            `;
        }
        resultsHTML += `</table>`;
    }

    resultsHTML += `</div>`;

    document.getElementById('Final Result').innerHTML = resultsHTML;
    document.getElementById('Final Result').style.display = 'block';
    document.getElementById('Choosing_instruction').style.display = 'none';
    document.getElementById('Timer').style.display = 'none';
    document.getElementById('Scene').style.display = 'none';
    document.getElementById('Retry').style.display = 'block';
    // Убрана строка, которая показывала кнопку настроек:
    // document.getElementById('Settings_button').style.display = 'block';
    fetch('submit_result_redblack.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: USER_ID,
            test_id: TEST_ID,
            speed: parseFloat(speed.toFixed(2)) // или score / timePassed
        })
    })
        .then(res => res.json())
        .then(data => {
            console.log('Результат отправлен:', data);
        })
        .catch(err => {
            console.error('Ошибка при отправке результата:', err);
        });
}