const TRUE_RESULT = ['southeast', 'east', 'south', 'south', 'southeast', 'west', 'southwest', 'northeast', 'northeast', 'northwest',
    'south', 'north', 'southeast', 'northwest', 'southeast', 'southeast', 'northwest', 'northeast', 'southeast', 'southwest',
    'south', 'southwest', 'southwest', 'north', 'southwest', 'southeast', 'west', 'southwest', 'west', 'west',
    'southeast', 'west', 'east', 'southeast', 'east', 'north', 'east', 'west', 'south', 'southeast',
    'southwest', 'southwest', 'southeast', 'southeast', 'west', 'northwest', 'south', 'southwest', 'southwest', 'west'];

let NUMBER_OF_COMPASSES = 25; // Значение по умолчанию
let timer;

const elements = {
    settings: document.getElementById('Settings'),
    startButton: document.getElementById('Start_button'),
};

function showSettings() {
    if (elements.settings && elements.startButton) {
        elements.settings.style.display = 'block';
        elements.startButton.style.display = 'none';
    } else {
        console.error("Не найдены элементы Settings или Start_button");
    }
}

function hideSettings() {
    testDuration = parseInt(document.getElementById('testDuration').value) * 1000;
    showTimerFlag = document.getElementById('showTimer').checked;
    
    if (elements.settings && elements.startButton) {
        elements.settings.style.display = 'none';
        elements.startButton.style.display = 'block';
    }
}

function updatePercentages() {
    const easyPercentInput = document.getElementById('easyPercent');
    const mediumPercentInput = document.getElementById('mediumPercent');
    const hardPercentInput = document.getElementById('hardPercent');

    let easyPercent = parseInt(easyPercentInput.value);
    let mediumPercent = parseInt(mediumPercentInput.value);
    let hardPercent = parseInt(hardPercentInput.value);

    const total = easyPercent + mediumPercent + hardPercent;
    const diff = total - 100;

    if (diff !== 0) {
        if (this.id === 'easyPercent') {
            const adjust = Math.min(mediumPercent + hardPercent, diff);
            const ratio = mediumPercent / (mediumPercent + hardPercent) || 0.5;
            mediumPercent = Math.max(0, mediumPercent - Math.round(adjust * ratio));
            hardPercent = Math.max(0, hardPercent - Math.round(adjust * (1 - ratio)));
        } else if (this.id === 'mediumPercent') {
            const adjust = Math.min(easyPercent + hardPercent, diff);
            const ratio = easyPercent / (easyPercent + hardPercent) || 0.5;
            easyPercent = Math.max(0, easyPercent - Math.round(adjust * ratio));
            hardPercent = Math.max(0, hardPercent - Math.round(adjust * (1 - ratio)));
        } else {
            const adjust = Math.min(easyPercent + mediumPercent, diff);
            const ratio = easyPercent / (easyPercent + mediumPercent) || 0.5;
            easyPercent = Math.max(0, easyPercent - Math.round(adjust * ratio));
            mediumPercent = Math.max(0, mediumPercent - Math.round(adjust * (1 - ratio)));
        }
    }

    const finalTotal = easyPercent + mediumPercent + hardPercent;
    if (finalTotal < 100) {
        hardPercent += 100 - finalTotal;
    } else if (finalTotal > 100) {
        hardPercent -= finalTotal - 100;
    }

    easyPercentInput.value = easyPercent;
    mediumPercentInput.value = mediumPercent;
    hardPercentInput.value = hardPercent;

    document.getElementById('easyPercentValue').textContent = `${easyPercent}%`;
    document.getElementById('mediumPercentValue').textContent = `${mediumPercent}%`;
    document.getElementById('hardPercentValue').textContent = `${hardPercent}%`;
}

document.querySelectorAll('input[name="generationMode"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const progressiveOptions = document.getElementById('progressiveOptions');
        progressiveOptions.style.display = radio.value === 'progressive' ? 'block' : 'none';
    });
});

let correctChosen = [];
let incorrectChosen = [];
let testDuration = 150000; // 2.5 минуты по умолчанию
let showTimerFlag = true;

function start_test() {
    testDuration = parseInt(document.getElementById('testDuration').value) * 1000;
    showTimerFlag = document.getElementById('showTimer').checked;
    const generationMode = document.querySelector('input[name="generationMode"]:checked').value;

    // Определяем количество компасов
    if (generationMode === 'random') {
        const difficulties = [
            { level: 'easy', count: 5 },
            { level: 'medium', count: 15 },
            { level: 'hard', count: 25 }
        ];
        const randomDifficulty = difficulties[Math.floor(Math.random() * difficulties.length)];
        NUMBER_OF_COMPASSES = randomDifficulty.count;
    } else {
        const easyPercent = parseInt(document.getElementById('easyPercent').value);
        const mediumPercent = parseInt(document.getElementById('mediumPercent').value);
        const hardPercent = parseInt(document.getElementById('hardPercent').value);

        const totalCompasses = 35; // Максимум компасов
        const easyCount = Math.round((easyPercent / 100) * totalCompasses);
        const mediumCount = Math.round((mediumPercent / 100) * totalCompasses);
        const hardCount = totalCompasses - easyCount - mediumCount;
        NUMBER_OF_COMPASSES = easyCount + mediumCount + hardCount;
        if (NUMBER_OF_COMPASSES > 35) NUMBER_OF_COMPASSES = 35;
    }

    // Настройка таймера
    const timerElement = document.getElementById('Timer');
    if (showTimerFlag) {
        timerElement.style.display = 'flex';
        timerElement.textContent = 'Время: ' + formatTime(testDuration / 1000);
    } else {
        timerElement.style.display = 'none';
    }

    // Скрываем интерфейс
    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('Settings_button').style.display = 'none';
    document.getElementById('Finish_button').style.display = 'block';
    document.getElementById('bg').style.background = 'white';
    document.getElementById('container').style.display = 'block';

    generateItemCompass();

    setTimeout(finish_test, testDuration);

    if (showTimerFlag) {
        let end = (new Date().getTime() + 1000) + testDuration;
        timer = setInterval(() => {
            let now = new Date().getTime();
            let distance = Math.floor((end - now) / 1000);
            timerElement.textContent = 'Время: ' + formatTime(distance);
            if (distance <= 0) {
                clearInterval(timer);
            }
        }, 1000);
    }
}

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

function get_random_set() {
    const generationMode = document.querySelector('input[name="generationMode"]:checked').value;
    let randomSet = new Set();

    if (generationMode === 'random') {
        while (randomSet.size < NUMBER_OF_COMPASSES) {
            randomSet.add(Math.floor(Math.random() * 50));
        }
    } else {
        const easyPercent = parseInt(document.getElementById('easyPercent').value);
        const mediumPercent = parseInt(document.getElementById('mediumPercent').value);
        const hardPercent = parseInt(document.getElementById('hardPercent').value);

        // Диапазоны сложности
        const easyIndices = Array.from({ length: 17 }, (_, i) => i); // 0–16
        const mediumIndices = Array.from({ length: 17 }, (_, i) => i + 17); // 17–33
        const hardIndices = Array.from({ length: 16 }, (_, i) => i + 34); // 34–49

        // Рассчитываем количество компасов
        let easyCount = Math.round((easyPercent / 100) * NUMBER_OF_COMPASSES);
        let mediumCount = Math.round((mediumPercent / 100) * NUMBER_OF_COMPASSES);
        let hardCount = NUMBER_OF_COMPASSES - easyCount - mediumCount;

        // Корректировка, если hardCount отрицательный
        if (hardCount < 0) {
            const excess = -hardCount;
            const totalEasyMedium = easyCount + mediumCount;
            const easyReduction = Math.round((easyCount / totalEasyMedium) * excess);
            const mediumReduction = excess - easyReduction;
            easyCount -= easyReduction;
            mediumCount -= mediumReduction;
            hardCount = 0;
        }

        // Функция для случайного выбора индексов
        const getRandomElements = (arr, count) => {
            const shuffled = [...arr].sort(() => 0.5 - Math.random());
            return shuffled.slice(0, Math.min(count, arr.length));
        };

        // Выбираем индексы
        const selectedEasy = getRandomElements(easyIndices, easyCount);
        const selectedMedium = getRandomElements(mediumIndices, mediumCount);
        const selectedHard = getRandomElements(hardIndices, hardCount);

        // Объединяем выбранные индексы
        const allSelected = [...selectedEasy, ...selectedMedium, ...selectedHard];
        allSelected.forEach(index => randomSet.add(index));

        // Заполняем оставшиеся места, если нужно
        while (randomSet.size < NUMBER_OF_COMPASSES) {
            randomSet.add(Math.floor(Math.random() * 50));
        }
    }

    return randomSet;
}

function generateItemCompass() {
    let container = document.getElementById('container');
    container.innerHTML = '';
    let randomSet = get_random_set();
    for (let i of randomSet) {
        container.innerHTML += `
            <div class="itemCompass">
                <div class="answers">
                    <div class="butSideWorld" id="north_${i}" onclick="cell('north_${i}')">С</div>
                    <div class="butSideWorld" id="east_${i}" onclick="cell('east_${i}')">В</div>
                    <div class="butSideWorld" id="south_${i}" onclick="cell('south_${i}')">Ю</div>
                    <div class="butSideWorld" id="west_${i}" onclick="cell('west_${i}')">З</div>
                </div>
                <img style="width: 137px;" src="https://www.aviaknow.ru/img/compas/${i+1}.png">
                <div class="answers">
                    <div class="butSideWorld" id="northeast_${i}" onclick="cell('northeast_${i}')">СВ</div>
                    <div class="butSideWorld" id="southeast_${i}" onclick="cell('southeast_${i}')">ЮВ</div>
                    <div class="butSideWorld" id="southwest_${i}" onclick="cell('southwest_${i}')">ЮЗ</div>
                    <div class="butSideWorld" id="northwest_${i}" onclick="cell('northwest_${i}')">СЗ</div>
                </div>
            </div>`;
    }
}

function cell(id) {
    let cell = document.getElementById(id);
    let num = id.split('_')[1];
    let val = id.split('_')[0];
    let flag = false;
    correctChosen.forEach(elem => {
        if (elem.id.split('_')[1] === num) {
            flag = true;
        }
    });
    incorrectChosen.forEach(elem => {
        if (elem.id.split('_')[1] === num) {
            flag = true;
        }
    });
    if (correctChosen.includes(cell)) {
        correctChosen.splice(correctChosen.indexOf(cell), 1);
        cell.style.backgroundColor = "white";
    } else if (incorrectChosen.includes(cell)) {
        incorrectChosen.splice(incorrectChosen.indexOf(cell), 1);
        cell.style.backgroundColor = "white";
    } else if (flag) {
        return;
    } else {
        cell.style.backgroundColor = "gray";
        if (val === TRUE_RESULT[num]) {
            correctChosen.push(cell);
        } else {
            incorrectChosen.push(cell);
        }
    }
}

function finish_test() {
    clearInterval(timer);
    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Final Result').style.display = 'block';
    document.getElementById('Finish_button').style.display = 'none';
    document.getElementById('Timer').style.display = 'none';
    
    const totalQuestions = correctChosen.length + incorrectChosen.length;
    const correctPercentage = totalQuestions > 0 
        ? Math.round((correctChosen.length / totalQuestions) * 100) 
        : 0;
    
    const directionStats = {
        north: { correct: 0, total: 0 },
        northeast: { correct: 0, total: 0 },
        east: { correct: 0, total: 0 },
        southeast: { correct: 0, total: 0 },
        south: { correct: 0, total: 0 },
        southwest: { correct: 0, total: 0 },
        west: { correct: 0, total: 0 },
        northwest: { correct: 0, total: 0 }
    };
    
    correctChosen.forEach(cell => {
        const direction = cell.id.split('_')[0];
        directionStats[direction].correct++;
        directionStats[direction].total++;
    });
    
    incorrectChosen.forEach(cell => {
        const direction = cell.id.split('_')[0];
        directionStats[direction].total++;
    });
    
    let resultsHTML = `
        <div class="results-container" style="text-align: center; margin: 0 auto;">
            <h3 style="text-align: center; color: black;">Результаты теста "Компасы"</h3>
            <div style="margin: 20px 0; font-size: 24px; color: black;">
                Правильных ответов: <strong>${correctChosen.length} из ${totalQuestions}</strong> (${correctPercentage}%)
            </div>
            <h4 style="text-align: center; margin-top: 20px; color: black;">Статистика по направлениям</h4>
            <table class="results-table" style="margin: 0 auto; border-collapse: collapse; width: 80%;">
                <tr>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; color: black;">Направление</th>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; color: black;">Правильно</th>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; color: black;">Ошибок</th>
                    <th style="padding: 8px; border: 1px solid #ddd; background-color: #f2f2f2; color: black;">Точность</th>
                </tr>
    `;
    
    for (const [direction, stats] of Object.entries(directionStats)) {
        if (stats.total > 0) {
            const accuracy = Math.round((stats.correct / stats.total) * 100);
            const errorCount = stats.total - stats.correct;
            resultsHTML += `
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; color: black;">${getDirectionName(direction)}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; color: black;">${stats.correct}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; color: black;">${errorCount}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; color: black;">${accuracy}%</td>
                </tr>
            `;
        }
    }
    
    resultsHTML += `
            </table>
            <div style="margin-top: 30px;">
                <p style="font-size: 18px; color: black;">Время выполнения: ${formatTime(testDuration / 1000)}</p>
            </div>
        </div>
    `;
    
    document.getElementById('Final Result').innerHTML = resultsHTML;
    document.getElementById('Retry').style.top = '10vh';
    document.getElementById('Retry').style.display = 'block';
    
    for (let cell of correctChosen) {
        cell.style.backgroundColor = "green";
    }
    for (let cell of incorrectChosen) {
        cell.style.backgroundColor = "red";
    }
    
    window.scroll(0, 0);
}

function getDirectionName(direction) {
    const names = {
        north: "Север (С)",
        northeast: "Северо-восток (СВ)",
        east: "Восток (В)",
        southeast: "Юго-восток (ЮВ)",
        south: "Юг (Ю)",
        southwest: "Юго-запад (ЮЗ)",
        west: "Запад (З)",
        northwest: "Северо-запад (СЗ)"
    };
    return names[direction] || direction;
}