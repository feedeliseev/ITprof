const globalContainer = document.getElementById('landolt_ring');
const REFRESH_INTERVAL = 1000;
const container = document.querySelector('.scene');
const children = [...container.children];

// Уровни сложности
const DIFFICULTY_LEVELS = {
    EASY: {
        directions: [1, 2, 3, 4], // Север, С-В, Восток, Ю-В
        cellsToShow: 32 // 4 направления × 8 ячеек
    },
    MEDIUM: {
        directions: [1, 2, 3, 4, 5, 6], // + Юг, Ю-З
        cellsToShow: 48 // 6 направлений × 8
    },
    HARD: {
        directions: [1, 2, 3, 4, 5, 6, 7, 8], // Все направления
        cellsToShow: 64 // Все ячейки
    }
};

let isDuringTest = false;
let timePassed = 0;
let initTime = 0;
let randRing = 1;
const ringMap = new Map([
    [1, "img/n.png"],
    [2, "img/ne.png"],
    [3, "img/e.png"],
    [4, "img/se.png"],
    [5, "img/s.png"],
    [6, "img/sw.png"],
    [7, "img/w.png"],
    [8, "img/nw.png"]
]);
let correctChosen = [];
let incorrectChosen = [];
let currentDifficulty = DIFFICULTY_LEVELS.EASY;

// Элементы интерфейса
const elements = {
    settings: document.getElementById('Settings'),
    startButton: document.getElementById('Start_button'),
    finishButton: document.getElementById('Finish_button'),
    instruction: document.getElementById('Instruction'),
    choosingInstruction: document.getElementById('Choosing_instruction'),
    choosingImg: document.getElementById('Choosing_img'),
    timer: document.getElementById('Timer'),
    finalResult: document.getElementById('Final Result'),
    scene: document.getElementById('Scene'),
    retryButton: document.getElementById('Retry'),
    bg: document.getElementById('bg')
};

// Настройки теста
let testDuration = 60;
let showTimer = true;
let generationMode = 'random';
let difficultyPercentages = {
    easy: 33,
    medium: 33,
    hard: 34
};

// Показ настроек
function showSettings() {
    elements.settings.style.display = 'block';
    elements.startButton.style.display = 'none';
    
    document.querySelectorAll('input[name="generationMode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            generationMode = this.value;
            document.getElementById('progressiveOptions').style.display = 
                this.value === 'progressive' ? 'block' : 'none';
        });
    });
}

// Обновление процентов сложности
function updatePercentages() {
    const easyVal = parseInt(document.getElementById('easyPercent').value);
    const mediumVal = parseInt(document.getElementById('mediumPercent').value);
    const hardVal = parseInt(document.getElementById('hardPercent').value);
    
    // Нормализация значений, чтобы сумма была 100%
    const total = easyVal + mediumVal + hardVal;
    const normalizedEasy = Math.round((easyVal / total) * 100);
    const normalizedMedium = Math.round((mediumVal / total) * 100);
    const normalizedHard = 100 - normalizedEasy - normalizedMedium;
    
    document.getElementById('easyPercent').value = normalizedEasy;
    document.getElementById('mediumPercent').value = normalizedMedium;
    document.getElementById('hardPercent').value = normalizedHard;
    
    document.getElementById('easyPercentValue').textContent = normalizedEasy + '%';
    document.getElementById('mediumPercentValue').textContent = normalizedMedium + '%';
    document.getElementById('hardPercentValue').textContent = normalizedHard + '%';
    
    difficultyPercentages = {
        easy: normalizedEasy,
        medium: normalizedMedium,
        hard: normalizedHard
    };
}

// Скрытие настроек и сохранение значений
function hideSettings() {
    testDuration = parseInt(document.getElementById('testDuration').value) || 60;
    showTimer = document.getElementById('showTimer').checked;
    generationMode = document.querySelector('input[name="generationMode"]:checked').value;
    
    elements.settings.style.display = 'none';
    elements.startButton.style.display = 'block';
}

// Перемешивание массива
function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

// Выбор случайного кольца с учетом сложности
function getRandomRing(difficultyLevel) {
    const directions = difficultyLevel.directions;
    return directions[Math.floor(Math.random() * directions.length)];
}

// Определение уровня сложности на основе процентов
function determineDifficultyLevel() {
    const rand = Math.random() * 100;
    
    if (rand < difficultyPercentages.easy) {
        return DIFFICULTY_LEVELS.EASY;
    } else if (rand < difficultyPercentages.easy + difficultyPercentages.medium) {
        return DIFFICULTY_LEVELS.MEDIUM;
    } else {
        return DIFFICULTY_LEVELS.HARD;
    }
}

// Начало теста
function start_test() {
    correctChosen = [];
    incorrectChosen = [];
    timePassed = 0;
    isDuringTest = true;
    initTime = Date.now();
    
    // Выбор режима генерации
    if (generationMode === 'progressive') {
        currentDifficulty = determineDifficultyLevel();
        randRing = getRandomRing(currentDifficulty);
    } else {
        // В произвольном режиме случайно выбираем уровень сложности
        const randomLevel = Math.random();
        if (randomLevel < 0.33) {
            currentDifficulty = DIFFICULTY_LEVELS.EASY;
        } else if (randomLevel < 0.66) {
            currentDifficulty = DIFFICULTY_LEVELS.MEDIUM;
        } else {
            currentDifficulty = DIFFICULTY_LEVELS.HARD;
        }
        randRing = Math.floor(Math.random() * 8) + 1;
    }
    
    // Настройка интерфейса
    elements.instruction.style.display = 'none';
    elements.choosingInstruction.style.display = 'block';
    elements.choosingInstruction.textContent = 'Нажмите все кольца вида:';
    elements.choosingImg.style.display = 'block';
    elements.choosingImg.innerHTML = `<img src="${ringMap.get(randRing)}" class="cell_img">`;
    elements.startButton.style.display = 'none';
    elements.finishButton.style.display = 'block';
    elements.bg.style.background = 'white';
    elements.scene.style.display = 'block';
    elements.finalResult.style.display = 'none';
    elements.retryButton.style.display = 'none';
    document.getElementById('Settings_button').style.display = 'none';
    
    // Настройка таймера
    elements.timer.style.display = showTimer ? 'flex' : 'none';
    elements.timer.textContent = '0s';
    
    // Перемешивание и отображение колец
    shuffle(children);
    
    // Определяем сколько и какие кольца показывать
    const showCells = generationMode === 'progressive' 
        ? currentDifficulty.cellsToShow 
        : Math.random() < 0.5 ? DIFFICULTY_LEVELS.MEDIUM.cellsToShow : 
          Math.random() < 0.5 ? DIFFICULTY_LEVELS.EASY.cellsToShow : DIFFICULTY_LEVELS.HARD.cellsToShow;
    
    // Показываем только нужные кольца
    children.forEach((child, index) => {
        if (index < showCells) {
            child.style.display = 'inherit';
            child.style.backgroundColor = 'white';
            container.appendChild(child);
        } else {
            child.style.display = 'none';
        }
    });
    
    // Запуск таймера
    const timerInterval = setInterval(() => {
        timePassed = Math.floor((Date.now() - initTime) / 1000);
        
        if (showTimer) {
            elements.timer.textContent = timePassed + 's';
        }
        
        if (timePassed >= testDuration) {
            clearInterval(timerInterval);
            finish_test();
        }
        
        if (!isDuringTest) {
            clearInterval(timerInterval);
        }
    }, REFRESH_INTERVAL);
}

// Обработка клика по кольцу
function cell(val, id) {
    if (!isDuringTest) return;
    
    const cell = document.getElementById(id);
    if (!cell || cell.style.display === 'none') return;
    
    if (correctChosen.includes(cell)) {
        correctChosen.splice(correctChosen.indexOf(cell), 1);
        cell.style.backgroundColor = "white";
    } else if (incorrectChosen.includes(cell)) {
        incorrectChosen.splice(incorrectChosen.indexOf(cell), 1);
        cell.style.backgroundColor = "white";
    } else {
        cell.style.backgroundColor = "gray";
        if (val === randRing) {
            correctChosen.push(cell);
        } else {
            incorrectChosen.push(cell);
        }
    }
}

// Завершение теста
function finish_test() {
    document.getElementById('Settings_button').style.display = 'none';

    isDuringTest = false;
    
    elements.choosingImg.style.display = 'none';
    elements.choosingInstruction.style.display = 'none';
    elements.finishButton.style.display = 'none';
    
    // Подсветка результатов
    correctChosen.forEach(cell => cell.style.backgroundColor = "green");
    incorrectChosen.forEach(cell => cell.style.backgroundColor = "red");
    
    // Пропущенные кольца
    const targetRingCells = children.filter(child => {
        if (child.style.display !== 'none') {
            const id = parseInt(child.id);
            return id > (randRing-1)*8 && id <= randRing*8;
        }
        return false;
    });
    
    targetRingCells.forEach(cell => {
        if (!correctChosen.includes(cell) && !incorrectChosen.includes(cell)) {
            cell.style.backgroundColor = "yellow";
        }
    });
    
    // Расчет результатов - теперь учитываем сколько колец нужного типа было показано
    const totalPossible = targetRingCells.length;
    
    const accuracy = totalPossible > 0 
        ? Math.round((correctChosen.length / totalPossible) * 100) 
        : 0;
    
    // Отображение результатов
    elements.finalResult.style.display = 'block';
    elements.finalResult.style.position = 'relative';
    elements.finalResult.style.top = '20px';
    elements.finalResult.style.color = 'black';
    elements.finalResult.style.backgroundColor = 'white';
    elements.finalResult.style.padding = '20px';
    elements.finalResult.style.borderRadius = '10px';
    elements.finalResult.style.margin = '0 auto';
    elements.finalResult.style.width = '80%';
    elements.finalResult.style.zIndex = '100';
    
    elements.finalResult.innerHTML = `
        <h3>Результаты теста</h3>
        <p>Режим: ${generationMode === 'progressive' ? 'Прогрессивный' : 'Произвольный'}</p>
        <p>Уровень сложности: ${getDifficultyName(currentDifficulty)}</p>
        <p>Вы верно выбрали ${correctChosen.length} из ${totalPossible} колец (${accuracy}%)</p>
        <p>Ошибочно выбрали ${incorrectChosen.length} колец</p>
        <p>Время выполнения: ${timePassed} секунд</p>
    `;
    
    elements.retryButton.style.display = 'block';
    elements.retryButton.style.position = 'relative';
    elements.retryButton.style.top = '30px';
    elements.retryButton.style.margin = '20px auto';
    fetch('submit_result_landoltring.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            percentage: accuracy,
            duration: timePassed
        })
    })
        .then(res => res.json())
        .then(data => console.log('Результат сохранён:', data))
        .catch(err => console.error('Ошибка сохранения результата:', err));
}




// Получение названия уровня сложности
function getDifficultyName(difficulty) {
    if (difficulty === DIFFICULTY_LEVELS.EASY) return "Очень простой";
    if (difficulty === DIFFICULTY_LEVELS.MEDIUM) return "Средний";
    return "Сложный";
}

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[name="generationMode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            generationMode = this.value;
            document.getElementById('progressiveOptions').style.display = 
                this.value === 'progressive' ? 'block' : 'none';
        });
    });
    
    updatePercentages();
    
    document.getElementById('easyPercent').addEventListener('input', updatePercentages);
    document.getElementById('mediumPercent').addEventListener('input', updatePercentages);
    document.getElementById('hardPercent').addEventListener('input', updatePercentages);
});