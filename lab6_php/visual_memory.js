let selectedCells = [];
let isDuringTest = false;
let canClick = false;
let score = 0;
let memorizationTime = 3000; // default 3 seconds

// Difficulty settings
const difficultySettings = {
    easy: {
        totalCells: 16,  // 4x4 grid
        highlightedCells: 4,
        color: 'lightgreen'
    },
    medium: {
        totalCells: 36,  // 6x6 grid
        highlightedCells: 6,
        color: 'gold'
    },
    hard: {
        totalCells: 64,  // 8x8 grid (original)
        highlightedCells: 8,
        color: 'salmon'
    }
};

let currentDifficulty = null;
let progressiveMode = false;
let progressiveSettings = {
    easyPercent: 33,
    mediumPercent: 33,
    hardPercent: 34
};

const elements = {
    settings: document.getElementById('Settings'),
    startButton: document.getElementById('Start_button'),
    settingsButton: document.getElementById('Settings_button'),
    memorizationTimeInput: document.getElementById('memorizationTime'),
    generationModeRandom: document.querySelector('input[name="generationMode"][value="random"]'),
    generationModeProgressive: document.querySelector('input[name="generationMode"][value="progressive"]'),
    easyPercentInput: document.getElementById('easyPercent'),
    mediumPercentInput: document.getElementById('mediumPercent'),
    hardPercentInput: document.getElementById('hardPercent'),
    scene: document.getElementById('Scene')
};

window.onload = function() {
    const savedMemTime = localStorage.getItem('memorizationTime');
    const savedMode = localStorage.getItem('generationMode');
    const savedEasyPercent = localStorage.getItem('easyPercent');
    const savedMediumPercent = localStorage.getItem('mediumPercent');
    const savedHardPercent = localStorage.getItem('hardPercent');
    
    if (savedMemTime) {
        memorizationTime = parseInt(savedMemTime);
        elements.memorizationTimeInput.value = memorizationTime / 1000;
    }
    
    if (savedMode) {
        if (savedMode === 'progressive') {
            elements.generationModeProgressive.checked = true;
            document.getElementById('progressiveOptions').style.display = 'block';
            progressiveMode = true;
        } else {
            elements.generationModeRandom.checked = true;
        }
    }
    
    if (savedEasyPercent && savedMediumPercent && savedHardPercent) {
        progressiveSettings.easyPercent = parseInt(savedEasyPercent);
        progressiveSettings.mediumPercent = parseInt(savedMediumPercent);
        progressiveSettings.hardPercent = parseInt(savedHardPercent);
        
        elements.easyPercentInput.value = progressiveSettings.easyPercent;
        elements.mediumPercentInput.value = progressiveSettings.mediumPercent;
        elements.hardPercentInput.value = progressiveSettings.hardPercent;
        
        // Обновляем проценты на странице
        updatePercentages();
    }
};

function showSettings() {
    elements.settings.style.display = 'block';
    elements.startButton.style.display = 'none';
}

function hideSettings() {
    // Validate and save settings
    memorizationTime = (parseInt(elements.memorizationTimeInput.value) || 3) * 1000;
    progressiveMode = elements.generationModeProgressive.checked;
    
    // Save to localStorage
    localStorage.setItem('memorizationTime', memorizationTime);
    localStorage.setItem('generationMode', progressiveMode ? 'progressive' : 'random');
    
    if (progressiveMode) {
        progressiveSettings.easyPercent = parseInt(elements.easyPercentInput.value);
        progressiveSettings.mediumPercent = parseInt(elements.mediumPercentInput.value);
        progressiveSettings.hardPercent = parseInt(elements.hardPercentInput.value);
        
        localStorage.setItem('easyPercent', progressiveSettings.easyPercent);
        localStorage.setItem('mediumPercent', progressiveSettings.mediumPercent);
        localStorage.setItem('hardPercent', progressiveSettings.hardPercent);
    }
    
    elements.settings.style.display = 'none';
    elements.startButton.style.display = 'block';
}

function updatePercentages() {
    let easy = parseInt(elements.easyPercentInput.value) || 0;
    let medium = parseInt(elements.mediumPercentInput.value) || 0;
    let hard = parseInt(elements.hardPercentInput.value) || 0;

    // Нормализуем значения, если сумма не равна 100
    const total = easy + medium + hard;
    if (total !== 100) {
        easy = Math.round((easy / total) * 100);
        medium = Math.round((medium / total) * 100);
        hard = 100 - easy - medium;
        
        elements.easyPercentInput.value = easy;
        elements.mediumPercentInput.value = medium;
        elements.hardPercentInput.value = hard;
    }

    document.getElementById('easyPercentValue').textContent = `${easy}%`;
    document.getElementById('mediumPercentValue').textContent = `${medium}%`;
    document.getElementById('hardPercentValue').textContent = `${hard}%`;

    // Сохраняем обновлённые настройки
    progressiveSettings.easyPercent = easy;
    progressiveSettings.mediumPercent = medium;
    progressiveSettings.hardPercent = hard;
}

function shuffle(array) {
    let currentIndex = array.length, temporaryValue, randomIndex;

    while (0 !== currentIndex) {
        randomIndex = Math.floor(Math.random() * currentIndex);
        currentIndex -= 1;

        temporaryValue = array[currentIndex];
        array[currentIndex] = array[randomIndex];
        array[randomIndex] = temporaryValue;
    }

    return array;
}

function selectRandomDifficulty() {
    const rand = Math.random() * 100;
    
    if (rand < progressiveSettings.easyPercent) {
        return 'easy';
    } else if (rand < progressiveSettings.easyPercent + progressiveSettings.mediumPercent) {
        return 'medium';
    } else {
        return 'hard';
    }
}

function setupGrid(difficulty) {
    const settings = difficultySettings[difficulty];
    currentDifficulty = difficulty;
    
    // Hide all cells first
    const allCells = document.querySelectorAll('.cell');
    allCells.forEach(cell => {
        cell.style.display = 'none';
    });
    
    // Show only the needed amount of cells
    const gridSize = Math.sqrt(settings.totalCells);
    const cellSize = 100 / gridSize;
    
    for (let i = 1; i <= settings.totalCells; i++) {
        const cell = document.getElementById(i.toString());
        cell.style.display = 'block';
        cell.style.width = `calc(${cellSize}% - 2px)`;
        cell.style.height = `calc(${cellSize}% - 2px)`;
        cell.style.backgroundColor = 'white';
    }
    
    // Adjust scene size based on grid size
    if (gridSize <= 4) {
        elements.scene.style.width = '40vh';
        elements.scene.style.height = '40vh';
    } else if (gridSize <= 6) {
        elements.scene.style.width = '50vh';
        elements.scene.style.height = '50vh';
    } else {
        elements.scene.style.width = '60vh';
        elements.scene.style.height = '60vh';
    }
    
    return settings;
}

function start_test() {
    isDuringTest = true;
    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';
    elements.settingsButton.style.display = 'none';
    document.getElementById('bg').style.background = 'white';
    document.getElementById('Scene').style.display = 'block';
    elements.settings.style.display = 'none';
    
    // Select difficulty
    let difficulty;
    if (progressiveMode) {
        difficulty = selectRandomDifficulty();
    } else {
        // В случайном режиме выбираем сложность равномерно
        const difficulties = ['easy', 'medium', 'hard'];
        difficulty = difficulties[Math.floor(Math.random() * difficulties.length)];
    }
    
    const settings = setupGrid(difficulty);
    
    // Get active cells (only those that are displayed)
    const activeCells = [];
    for (let i = 1; i <= settings.totalCells; i++) {
        activeCells.push(document.getElementById(i.toString()));
    }
    
    shuffle(activeCells);
    
    // Highlight random cells
    for (let i = 0; i < settings.highlightedCells; i++) {
        activeCells[i].style.backgroundColor = settings.color;
    }
    
    setTimeout(fillAnswers, memorizationTime);
}

function cell(id) {
    if (canClick) {
        let cell = document.getElementById(id);
        if (cell.style.display !== 'none' && cell.style.backgroundColor !== 'gray') {
            selectedCells.push(cell);
            cell.style.backgroundColor = "gray";
        }
    }
}

function fillAnswers() {
    const settings = difficultySettings[currentDifficulty];
    
    // Reset all active cells to white
    for (let i = 1; i <= settings.totalCells; i++) {
        const cell = document.getElementById(i.toString());
        if (cell.style.display !== 'none') {
            cell.style.backgroundColor = 'white';
        }
    }
    
    canClick = true;
    document.getElementById('Finish_button').style.display = 'block';
}

function finish_test() {
    isDuringTest = false;
    canClick = false;
    document.getElementById('Final Result').style.display = 'block';
    document.getElementById('Finish_button').style.display = 'none';
    document.getElementById('Retry').style.display = 'block';
    elements.settingsButton.style.display = 'none';
    elements.settings.style.display = 'none';

    
    const settings = difficultySettings[currentDifficulty];
    let correct = 0;
    let incorrect = 0;
    
    // Determine which cells were correct (first N highlighted cells)
    const correctCells = [];
    for (let i = 1; i <= settings.totalCells; i++) {
        const cell = document.getElementById(i.toString());
        if (cell.style.display !== 'none') {
            correctCells.push(cell);
        }
    }
    
    shuffle(correctCells);
    const highlightedCells = correctCells.slice(0, settings.highlightedCells);
    
    // Check selected cells
    for (const cell of selectedCells) {
        if (highlightedCells.includes(cell)) {
            correct++;
            cell.style.backgroundColor = 'green';
        } else {
            incorrect++;
            cell.style.backgroundColor = 'red';
        }
    }
    
    // Highlight missed correct cells
    for (const cell of highlightedCells) {
        if (cell.style.backgroundColor === 'white') {
            cell.style.backgroundColor = 'blue';
        }
    }
    
    const totalSelected = selectedCells.length;
    const accuracy = totalSelected > 0 ? (correct / settings.highlightedCells * 100) : 0;
    const memorizationSeconds = memorizationTime / 1000;
    
    const difficultyNames = {
        easy: 'Легкий',
        medium: 'Средний',
        hard: 'Сложный'
    };
    
    const resultsHTML = `
        <div class="results-container" style="color: black;">
            <h3 style="color: black;">Результаты теста "Визуальная память"</h3>
            <table class="results-table">
                <tr>
                    <th style="color: black;">Уровень сложности</th>
                    <td style="color: black;">${difficultyNames[currentDifficulty]}</td>
                </tr>
                <tr>
                    <th style="color: black;">Размер сетки</th>
                    <td style="color: black;">${Math.sqrt(settings.totalCells)}×${Math.sqrt(settings.totalCells)}</td>
                </tr>
                <tr>
                    <th style="color: black;">Ячеек для запоминания</th>
                    <td style="color: black;">${settings.highlightedCells}</td>
                </tr>
                <tr>
                    <th style="color: black;">Всего выбрано ячеек</th>
                    <td style="color: black;">${totalSelected}</td>
                </tr>
                <tr>
                    <th style="color: black;">Правильно выбрано</th>
                    <td style="color: black;">${correct} из ${settings.highlightedCells}</td>
                </tr>
                <tr>
                    <th style="color: black;">Неправильно выбрано</th>
                    <td style="color: black;">${incorrect}</td>
                </tr>
                <tr>
                    <th style="color: black;">Точность</th>
                    <td style="color: black;">${accuracy.toFixed(1)}%</td>
                </tr>
                <tr>
                    <th style="color: black;">Время запоминания</th>
                    <td style="color: black;">${memorizationSeconds.toFixed(1)} сек</td>
                </tr>
            </table>
        </div>
    `;
    
    document.getElementById('Final Result').innerHTML = resultsHTML;
    
    score = 0;
    selectedCells = [];
}

// Event listeners for progressive mode options
document.querySelectorAll('input[name="generationMode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('progressiveOptions').style.display = 
            this.value === 'progressive' ? 'block' : 'none';
        progressiveMode = this.value === 'progressive';
    });
});