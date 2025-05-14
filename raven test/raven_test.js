let correctChosen = 0;
let counter = 0;
let isAorBseries = true;
const TRUE_RESULT = [4, 5, 1, 2, 6, 3, 6, 2, 1, 3, 4, 5,
    2, 6, 1, 2, 1, 3, 5, 6, 4, 3, 4, 5,
    8, 2, 3, 8, 7, 4, 5, 1, 7, 6, 1, 2,
    3, 4, 3, 7, 8, 6, 5, 4, 1, 2, 5, 6,
    7, 6, 8, 2, 1, 5, 1, 6, 3, 2, 4, 5];

let timerInterval;
let timeLeft;
let showTimerFlag = true;
const timerElement = document.createElement('div');
timerElement.className = 'timer';
timerElement.style.display = 'none';
document.getElementById('raven_test').appendChild(timerElement);

// Определение сложности картинок
const difficultyLevels = {
    easy: ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10', 'A11', 'A12'],
    medium: ['B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8', 'B9', 'B10', 'B11', 'B12', 
             'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10', 'C11', 'C12'],
    hard: ['D1', 'D2', 'D3', 'D4', 'D5', 'D6', 'D7', 'D8', 'D9', 'D10', 'D11', 'D12',
           'E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9', 'E10', 'E11', 'E12']
};

let currentTestImages = [];

const elements = {
    settings: document.getElementById('Settings'),
    startButton: document.getElementById('Start_button')
};

function showSettings() {
    elements.settings.style.display = 'block';
    elements.startButton.style.display = 'none';
}

function hideSettings() {
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

function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

function prepareTestImages() {
    const generationMode = document.querySelector('input[name="generationMode"]:checked').value;
    const scene = document.getElementById('Scene');
    
    // Очищаем сцену
    while (scene.firstChild) {
        scene.removeChild(scene.firstChild);
    }
    
    if (generationMode === 'random') {
        // Случайный выбор уровня сложности
        const levels = ['easy', 'medium', 'hard'];
        const randomLevel = levels[Math.floor(Math.random() * levels.length)];
        currentTestImages = [...difficultyLevels[randomLevel]];
        
        // Добавляем все изображения выбранного уровня
        currentTestImages.forEach((img, index) => {
            const imgElement = document.createElement('img');
            imgElement.src = `${img}.png`;
            imgElement.style.position = 'absolute';
            imgElement.style.zIndex = 60 - index;
            scene.appendChild(imgElement);
        });
        
        // Обновляем флаг isAorBseries в зависимости от уровня
        isAorBseries = randomLevel === 'easy';
    } else {
        // Прогрессивный режим - учитываем проценты из ползунков
        const easyPercent = parseInt(document.getElementById('easyPercent').value) / 100;
        const mediumPercent = parseInt(document.getElementById('mediumPercent').value) / 100;
        const hardPercent = parseInt(document.getElementById('hardPercent').value) / 100;
        
        // Рассчитываем количество картинок каждого уровня
        const totalImages = 60;
        const easyCount = Math.round(totalImages * easyPercent);
        const mediumCount = Math.round(totalImages * mediumPercent);
        const hardCount = totalImages - easyCount - mediumCount;
        
        // Выбираем картинки для каждого уровня
        let selectedImages = [];
        
        // Легкие
        selectedImages = selectedImages.concat(
            shuffleArray([...difficultyLevels.easy]).slice(0, easyCount)
            .map(img => ({img, level: 'easy'}))
        );
        
        // Средние
        selectedImages = selectedImages.concat(
            shuffleArray([...difficultyLevels.medium]).slice(0, mediumCount)
            .map(img => ({img, level: 'medium'}))
        );
        
        // Сложные
        selectedImages = selectedImages.concat(
            shuffleArray([...difficultyLevels.hard]).slice(0, hardCount)
            .map(img => ({img, level: 'hard'}))
        );
        
        // Перемешиваем все выбранные картинки
        selectedImages = shuffleArray(selectedImages);
        currentTestImages = selectedImages.map(item => item.img);
        
        // Добавляем изображения на сцену
        selectedImages.forEach((item, index) => {
            const imgElement = document.createElement('img');
            imgElement.src = `${item.img}.png`;
            imgElement.style.position = 'absolute';
            imgElement.style.zIndex = 60 - index;
            scene.appendChild(imgElement);
            
            // Устанавливаем флаг isAorBseries в зависимости от уровня текущей картинки
            if (index === 0) {
                isAorBseries = item.level === 'easy';
            }
        });
    }
}

function start_test() {
    // Сброс переменных
    correctChosen = 0;
    counter = 0;
    
    // Подготовка тестовых изображений
    prepareTestImages();
    
    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('bg').style.background = 'white';
    document.getElementById('Scene').style.display = 'block';
    document.getElementById('Settings_button').style.display = 'none';

    // Получаем настройки
    const duration = parseInt(document.getElementById('testDuration').value) || 60;
    showTimerFlag = document.getElementById('showTimer').checked;
    
    // Инициализация таймера
    timeLeft = duration;
    updateTimerDisplay();
    
    if (showTimerFlag) {
        timerElement.style.display = 'block';
    }
    
    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        
        if (timeLeft <= 0) {
            finish_test();
        }
    }, 1000);

    document.getElementById('Scene').addEventListener('mousemove', mouseMoveOverPossibleAnswer);
    addEventListener('click', next_time);
}

function isOverPossibleAnswer(e) {
    let mousePositionX = e.clientX - e.target.getBoundingClientRect().left;
    let mousePositionY = e.clientY - e.target.getBoundingClientRect().top;
    if  (isAorBseries) {
        if (1 <= mousePositionX && mousePositionX <= 129 &&
            247 <= mousePositionY && mousePositionY <= 326) return 1;
        if (151 <= mousePositionX && mousePositionX <= 279 &&
            247 <= mousePositionY && mousePositionY <= 326) return 2;
        if (301 <= mousePositionX && mousePositionX <= 429 &&
            247 <= mousePositionY && mousePositionY <= 326) return 3;
        if (1 <= mousePositionX && mousePositionX <= 129 &&
            360 <= mousePositionY && mousePositionY <= 438) return 4;
        if (151 <= mousePositionX && mousePositionX <= 279 &&
            360 <= mousePositionY && mousePositionY <= 438) return 5;
        if (301 <= mousePositionX && mousePositionX <= 429 &&
            360 <= mousePositionY && mousePositionY <= 438) return 6;
    } else {
        if (0 <= mousePositionX && mousePositionX <= 112 &&
            272 <= mousePositionY && mousePositionY <= 341) return 1;
        if (121 <= mousePositionX && mousePositionX <= 233 &&
            272 <= mousePositionY && mousePositionY <= 341) return 2;
        if (242 <= mousePositionX && mousePositionX <= 354 &&
            272 <= mousePositionY && mousePositionY <= 341) return 3;
        if (363 <= mousePositionX && mousePositionX <= 474 &&
            272 <= mousePositionY && mousePositionY <= 341) return 4;
        if (0 <= mousePositionX && mousePositionX <= 112 &&
            370 <= mousePositionY && mousePositionY <= 439) return 5;
        if (121 <= mousePositionX && mousePositionX <= 233 &&
            370 <= mousePositionY && mousePositionY <= 439) return 6;
        if (242 <= mousePositionX && mousePositionX <= 354 &&
            370 <= mousePositionY && mousePositionY <= 439) return 7;
        if (363 <= mousePositionX && mousePositionX <= 474 &&
            370 <= mousePositionY && mousePositionY <= 439) return 8;
    }
    return false;
}

function mouseMoveOverPossibleAnswer(e) {
    if (isOverPossibleAnswer(e)) {
        e.target.style.cursor = 'pointer';
    } else {
        e.target.style.cursor = 'crosshair';
    }
}

function next_time(e) {
    if (!(document.getElementById('Scene').contains(e.target))) {
        return;
    }
    let answer = isOverPossibleAnswer(e)
    if (answer) {
        if (answer === TRUE_RESULT[counter]) {
            correctChosen += 1;
            e.target.remove();
        } else {
            e.target.style.zIndex = '0';
        }
        counter++;
        
        // Обновляем флаг isAorBseries в зависимости от текущей картинки
        if (counter < currentTestImages.length) {
            const currentImg = currentTestImages[counter];
            isAorBseries = difficultyLevels.easy.includes(currentImg);
        }
    }
    if (counter === currentTestImages.length) {
        finish_test();
    }
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    
    if (timeLeft <= 10) {
        timerElement.style.color = 'red';
        timerElement.style.fontWeight = 'bold';
    } else {
        timerElement.style.color = 'black';
        timerElement.style.fontWeight = 'normal';
    }
}

function finish_test() {
    clearInterval(timerInterval);
    timerElement.style.display = 'none';
    
    document.querySelector('.result').style.display = 'block';
    document.querySelector('.restart_button').style.display = 'block';
    document.querySelector('.settings_button').style.display = 'none';
    
    let percentOfCorrectAnswer = correctChosen / currentTestImages.length * 100;
    let message;
    if (percentOfCorrectAnswer <= 5) {
        message = 'V степень: дефектная интеллектуальная способность';
    } else if (6 <= percentOfCorrectAnswer && percentOfCorrectAnswer <= 24) {
        message = 'IV степень: интеллект ниже среднего';
    } else if (25 <= percentOfCorrectAnswer && percentOfCorrectAnswer <= 74) {
        message = 'III степень: средний интеллект';
    } else if (75 <= percentOfCorrectAnswer && percentOfCorrectAnswer <= 94) {
        message = 'II степень: незаурядный интеллект';
    } else {
        message = 'I степень: особо высокоразвитый интеллект';
    }

    const totalTimeSeconds = parseInt(document.getElementById('testDuration').value) || 60;
    const speed = totalTimeSeconds > 0 ? (correctChosen / totalTimeSeconds).toFixed(2) : 0;

    // Определяем уровень сложности теста
    let testDifficulty = '';
    if (currentTestImages.every(img => difficultyLevels.easy.includes(img))) {
        testDifficulty = 'Легкий';
    } else if (currentTestImages.every(img => difficultyLevels.hard.includes(img))) {
        testDifficulty = 'Сложный';
    } else {
        testDifficulty = 'Смешанный';
    }

    const resultsHTML = `
        <div class="results-container" style="color: black;">
            <h3 style="color: black;">Результаты теста "Прогрессивные матрицы Равена"</h3>
            <table class="results-table">
                <tr>
                    <th style="color: black;">Уровень сложности</th>
                    <td style="color: black;">${testDifficulty}</td>
                </tr>
                <tr>
                    <th style="color: black;">Всего заданий</th>
                    <td style="color: black;">${currentTestImages.length}</td>
                </tr>
                <tr>
                    <th style="color: black;">Правильных ответов</th>
                    <td style="color: black;">${correctChosen}</td>
                </tr>
                <tr>
                    <th style="color: black;">Точность</th>
                    <td style="color: black;">${percentOfCorrectAnswer.toFixed(1)}%</td>
                </tr>
                <tr>
                    <th style="color: black;">Скорость</th>
                    <td style="color: black;">${speed} ответов/сек</td>
                </tr>
                <tr>
                    <th style="color: black;">Уровень интеллекта</th>
                    <td style="color: black;">${message}</td>
                </tr>
            </table>
        </div>
    `;

    document.getElementById('Final Result').innerHTML = resultsHTML;
    document.getElementById('Final Result').style.display = 'block';
    document.getElementById('Scene').style.display = 'none';
    document.getElementById('Retry').style.display = 'block';
    document.getElementById('Retry').style.top = '10vh';
    fetch('submit_result_landoltring.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            test_id: TEST_ID,
            percentage: parseFloat(percentOfCorrectAnswer.toFixed(1)),
            duration: parseFloat(totalTimeSeconds)
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') {
                console.error('Ошибка при сохранении результата:', data.error || 'Неизвестная ошибка');
            }
        })
        .catch(error => {
            console.error('Ошибка запроса:', error);
        });
}