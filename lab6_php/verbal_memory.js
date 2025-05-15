const dictionary = ['10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80', '81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'];
const maxIteration = 5;
const resultList = [];

let iteration = 0;
let testDuration = 60; // Значение по умолчанию - 60 секунд
let currentDifficulty = 'random'; // 'easy', 'medium', 'hard', 'random'
let showTimer = true;
let cellCount = 16; // По умолчанию

let selectedWords = [];
let isDuringTest = false;
let score = 0;

const elements = {
    settings: document.getElementById('Settings'),
    startButton: document.getElementById('Start_button'),
    progressiveOptions: document.getElementById('progressiveOptions'),
    generationModeRadios: document.getElementsByName('generationMode'),
    showTimerCheckbox: document.getElementById('showTimer')
};

// Настройки сложности
const difficultySettings = {
    easy: {
        cells: 9, // 3x3
        timeMultiplier: 1.5
    },
    medium: {
        cells: 16, // 4x4
        timeMultiplier: 1.0
    },
    hard: {
        cells: 25, // 5x5
        timeMultiplier: 0.7
    }
};

function showSettings() {
    elements.settings.style.display = 'block';
    elements.startButton.style.display = 'none';
}

function hideSettings() {
    testDuration = parseInt(document.getElementById('testDuration').value);
    showTimer = elements.showTimerCheckbox.checked;
    
    // Определяем выбранный режим генерации
    for (const radio of elements.generationModeRadios) {
        if (radio.checked) {
            currentDifficulty = radio.value;
            break;
        }
    }
    
    elements.settings.style.display = 'none';
    elements.startButton.style.display = 'block';
}

// Обновление процентов сложности
function updatePercentages() {
    document.getElementById('easyPercentValue').textContent = document.getElementById('easyPercent').value + '%';
    document.getElementById('mediumPercentValue').textContent = document.getElementById('mediumPercent').value + '%';
    document.getElementById('hardPercentValue').textContent = document.getElementById('hardPercent').value + '%';
}

// Показываем/скрываем опции прогрессивного режима
document.querySelectorAll('input[name="generationMode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        elements.progressiveOptions.style.display = this.value === 'progressive' ? 'block' : 'none';
    });
});

function shuffle(array) {
    let currentIndex = array.length,
        temporaryValue, randomIndex;

    while (0 !== currentIndex) {
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;

        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
    }

    return array;
}

function onAnswer() {
    score = 0;
    const children = document.querySelectorAll('.cell_verbal:not(.hidden)');
    
    for (const child of children) {
        let answer = child.innerHTML.toLowerCase();
        if (selectedWords.includes(answer)) {
            score++;
            selectedWords.splice(selectedWords.indexOf(answer), 1);
        }
    }
    
    resultList.push(score);
    if (iteration !== maxIteration) {
        start_test();
    } else {
        iteration++;
        finish_test();
    }
}

function createGrid() {
    const container = document.querySelector('.scene');
    container.innerHTML = '';
    
    // Определяем размер сетки
    const gridSize = Math.sqrt(cellCount);
    container.style.gridTemplateColumns = `repeat(${gridSize}, 1fr)`;
    container.style.gridTemplateRows = `repeat(${gridSize}, 1fr)`;
    
    // Создаем ячейки
    for (let i = 0; i < cellCount; i++) {
        const cell = document.createElement('div');
        cell.className = 'cell_verbal';
        cell.id = `cell${i+1}`;
        container.appendChild(cell);
    }
}

function determineDifficulty() {
    if (currentDifficulty === 'random') {
        // Случайный выбор сложности
        const rand = Math.random();
        if (rand < 0.33) return 'easy';
        if (rand < 0.66) return 'medium';
        return 'hard';
    } else if (currentDifficulty === 'progressive') {
        // Прогрессивный режим - выбираем сложность на основе процентов
        const easyPercent = parseInt(document.getElementById('easyPercent').value);
        const mediumPercent = parseInt(document.getElementById('mediumPercent').value);
        const hardPercent = parseInt(document.getElementById('hardPercent').value);
        
        const rand = Math.random() * 100;
        if (rand < easyPercent) return 'easy';
        if (rand < easyPercent + mediumPercent) return 'medium';
        return 'hard';
    }
    return currentDifficulty;
}

function start_test() {
    iteration++;
    isDuringTest = true;
    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Settings_button').style.display = 'none'; // Добавлено: скрываем кнопку настроек
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('bg').style.background = 'white';
    document.getElementById('Scene').style.display = 'grid'; // Изменено на grid
    document.getElementById('Finish_button').style.display = 'none';
    document.getElementById('Iteration').style.display = 'block';
    document.getElementById('Iteration').innerHTML = 'Попытка: ' + iteration + '/' + maxIteration;

    // Определяем сложность для этой попытки
    const difficulty = determineDifficulty();
    const settings = difficultySettings[difficulty];
    cellCount = settings.cells;
    
    // Создаем сетку соответствующего размера
    createGrid();
    
    const container = document.querySelector('.scene');
    const children = [...container.children];
    
    shuffle(children);
    for (const child of children) {
        container.appendChild(child);
    }
    
    shuffle(dictionary);
    selectedWords = [];
    let cursor = 0;
    for (const child of children) {
        cursor++;
        selectedWords.push(dictionary[cursor]);
        child.innerHTML = dictionary[cursor];
    }
    
    // Корректируем время в зависимости от сложности
    const adjustedDuration = testDuration * settings.timeMultiplier;
    
    if (showTimer) {
        // Показываем таймер, если включено в настройках
        const timerElement = document.createElement('div');
        timerElement.id = 'timer';
        timerElement.style.position = 'fixed';
        timerElement.style.top = '10px';
        timerElement.style.right = '10px';
        timerElement.style.fontSize = '24px';
        timerElement.style.color = 'black';
        document.body.appendChild(timerElement);
        
        let timeLeft = adjustedDuration;
        
        const timerInterval = setInterval(() => {
            timeLeft--;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.remove();
                fillAnswers();
            }
        }, 1000);
    }
    
    // Используем скорректированное время
    setTimeout(() => {
        if (showTimer) {
            const timerElement = document.getElementById('timer');
            if (timerElement) timerElement.remove();
        }
        fillAnswers();
    }, adjustedDuration * 1000);
}

function fillAnswers() {
    const children = document.querySelectorAll('.cell_verbal');
    for (const child of children) {
        child.innerHTML = "";
        child.contentEditable = "true";
    }
    document.getElementById('Finish_button').style.display = 'block';
}

function finish_test() {
    isDuringTest = false;
    document.getElementById('Finish_button').style.display = 'none';
    document.getElementById('Scene').style.display = 'none';
    document.getElementById('Retry').style.display = 'block';
    
    // Рассчитываем средний результат по всем попыткам
    let sum = 0;
    for (let i of resultList) {
        i = parseInt(i);
        sum += i;
    }
    const averageScore = (sum / (cellCount * maxIteration)) * 100;
    const bestScore = Math.max(...resultList) / cellCount * 100;
    const worstScore = Math.min(...resultList) / cellCount * 100;
    
    // Создаем HTML для отображения результатов
    let resultsHTML = `
        <div class="results-container" style="color: black;">
            <h3 style="color: black;">Результаты теста "Память на цифры"</h3>
            <p style="color: black;">Длительность запоминания: ${testDuration} сек</p>
            <p style="color: black;">Количество попыток: ${maxIteration}</p>
            <p style="color: black;">Режим: ${currentDifficulty === 'progressive' ? 'Прогрессивный' : 'Произвольный'}</p>
            
            <table class="results-table">
                <tr>
                    <th style="color: black;">Средний результат</th>
                    <td style="color: black;">${averageScore.toFixed(2)}%</td>
                </tr>
                <tr>
                    <th style="color: black;">Лучший результат</th>
                    <td style="color: black;">${bestScore.toFixed(2)}%</td>
                </tr>
                <tr>
                    <th style="color: black;">Худший результат</th>
                    <td style="color: black;">${worstScore.toFixed(2)}%</td>
                </tr>
            </table>
            
            <h4 style="color: black;">Результаты по попыткам</h4>
            <table class="minute-results">
                <tr class="minute-header">
                    <th style="color: black;">Попытка</th>
                    <th style="color: black;">Правильных ответов</th>
                    <th style="color: black;">Процент</th>
                </tr>
    `;
    
    // Добавляем строки с результатами каждой попытки
    for (let i = 0; i < resultList.length; i++) {
        const percentage = (resultList[i] / cellCount) * 100;
        resultsHTML += `
            <tr>
                <td style="color: black;">${i + 1}</td>
                <td style="color: black;">${resultList[i]} из ${cellCount}</td>
                <td style="color: black;">${percentage.toFixed(2)}%</td>
            </tr>
        `;
    }
    
    resultsHTML += `</table></div>`;
    
    document.getElementById('Final Result').innerHTML = resultsHTML;
    document.getElementById('Final Result').style.display = 'block';

}

function getDifficultyName(score) {
    // Определяем сложность по количеству ячеек
    if (cellCount === 9) return 'Легкий';
    if (cellCount === 16) return 'Средний';
    if (cellCount === 25) return 'Сложный';
    return 'Неизвестно';
}

