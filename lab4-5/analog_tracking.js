// Константы теперь будут изменяться через настройки
let TEST_DURATION = 60000; // (milliseconds)
let BASE_ACCELERATION = 1.0; // базовое ускорение
let ACCELERATION_STEP = 0.5; // шаг ускорения
let CURRENT_ACCELERATION = 1.0; // текущее ускорение
let HORIZONTAL_MOVE = 1; // px (базовая скорость)
let HORIZONTAL_USER_MOVE = 30; // px
let DIRECTION_CHANGE_INTERVAL = 800; // (milliseconds)
const REFRESH_INTERVAL = 3; // (milliseconds)
let CHANGE_TYPE = 'random'; // тип изменения ускорения
let CHANGE_INTERVAL = 10000; // интервал изменения ускорения (ms)

const elements = {
    settings: document.getElementById('Settings'),
    startButton: document.getElementById('Start_button'),
    settingsButton: document.getElementById('Settings_button'),
    instruction: document.getElementById('Instruction'),
    testDuration: document.getElementById('testDuration'),
    accelerationInterval: document.getElementById('accelerationInterval'),
    accelerationStep: document.getElementById('accelerationStep'),
    changeTypeRandom: document.querySelector('input[name="changeType"][value="random"]'),
    changeTypeFixed: document.querySelector('input[name="changeType"][value="fixed"]'),
    changeInterval: document.getElementById('changeInterval'),
    showTimer: document.getElementById('showTimer'),
    showMinuteResults: document.getElementById('showMinuteResults')
};

// Данные для хранения результатов по минутам
let minuteData = {
    reactionTimes: []
};

function showSettings() {
    elements.settings.style.display = 'block';
    elements.startButton.style.display = 'none';
}

const globalContainer = document.getElementById('analog_tracking');
const circle = document.getElementById('Circle')
const rectangle1 = document.getElementById('Upper_marker')
const rectangle2 = document.getElementById('Lower_marker')
const rectangleGlow = "1px 1px 10px 5px darkgreen"

let isDuringTest = false;
let countDownTime;
let leftBorder = parseFloat(getComputedStyle(rectangle1).left.replace("px", ""));
let rightBorder = leftBorder + parseFloat(getComputedStyle(rectangle1).width.replace("px", ""));

let globalLeftBorder = 0;
let globalRightBorder = parseFloat(getComputedStyle(globalContainer).width.replace("px", ""));

// Для измерения времени реакции
let reactionTimes = [];
let lastDirectionChangeTime = 0;
let currentDirection = null;
let userReacted = false;

// Результаты
let testStartTime = 0;

document.addEventListener('keydown', function(event) {
    if (isDuringTest) {
        switch (event.code) {
            case "KeyA":
                moveLeft(HORIZONTAL_USER_MOVE);
                checkReaction('left');
                break;
            case "KeyD":
                moveRight(HORIZONTAL_USER_MOVE);
                checkReaction('right');
                break;
        }
    }
});

function checkReaction(userDirection) {
    if (!userReacted && currentDirection && userDirection === currentDirection) {
        const reactionTime = new Date().getTime() - lastDirectionChangeTime;
        reactionTimes.push(reactionTime);
        userReacted = true;
    }
}

function moveLeft(change_value) {
    let curr_left_value = getCurrentValuePX(circle, "left");
    let new_left_value = Math.max(globalLeftBorder, curr_left_value - change_value);
    circle.style.left = new_left_value + "px";
}

function moveRight(change_value) {
    let curr_left_value = getCurrentValuePX(circle, "left");
    let new_left_value = Math.min(globalRightBorder - parseFloat(getComputedStyle(circle).width.replace("px", "")), curr_left_value + change_value);
    circle.style.left = new_left_value + "px";
}

function getCurrentValuePX(element, property) {
    let style = getComputedStyle(element)
    let curr_left = style.getPropertyValue(property);
    return parseFloat(curr_left.replace("px", ""));
}

function hideSettings() {
    elements.settings.style.display = 'none';
    elements.startButton.style.display = 'block';
    
    // Обновляем параметры теста из настроек
    TEST_DURATION = parseInt(elements.testDuration.value) * 60 * 1000; // Минуты → миллисекунды
    BASE_ACCELERATION = parseFloat(elements.accelerationInterval.value);
    ACCELERATION_STEP = parseFloat(elements.accelerationStep.value);
    CURRENT_ACCELERATION = BASE_ACCELERATION;
    
    if (elements.changeTypeRandom.checked) {
        CHANGE_TYPE = 'random';
    } else {
        CHANGE_TYPE = 'fixed';
        CHANGE_INTERVAL = parseInt(elements.changeInterval.value) * 1000;
    }
}

function start_test() {
    // Сброс переменных
    isDuringTest = true;
    reactionTimes = [];
    CURRENT_ACCELERATION = BASE_ACCELERATION;
    elements.settingsButton.style.display = 'none';
    testStartTime = new Date().getTime();
    
    // Сброс данных по минутам
    minuteData = {
        reactionTimes: []
    };
    
    // Инициализация переменных для отслеживания реакции
    lastDirectionChangeTime = new Date().getTime();
    currentDirection = Math.floor(Math.random()*2) === 0 ? 'left' : 'right'; // Начальное направление
    userReacted = false;
    
    // Настройка интерфейса
    if (elements.showTimer.checked) {
        document.getElementById('Timer').style.display = 'flex';
    } else {
        document.getElementById('Timer').style.display = 'none';
    }
    
    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('bg').style.background = 'white';
    circle.style.display = 'flex';
    document.getElementById('Upper_marker').style.display = 'flex';
    document.getElementById('Lower_marker').style.display = 'flex';
    document.getElementById('Lower_marker').style.top = '3vh';

    // Обновляем таймер сразу
    countDownTime = new Date().getTime() + TEST_DURATION;
    if (elements.showTimer.checked) {
        updateTimer();
    }
    
    // Запускаем таймер
    let timer = setInterval(updateTimer, REFRESH_INTERVAL);

    // Движение круга начинаем сразу
    let movementInterval = setInterval(moveCircle, REFRESH_INTERVAL);
    
    // Изменение направления с заданным интервалом
    let directionInterval = setInterval(changeDirection, DIRECTION_CHANGE_INTERVAL);
    
    // Изменение ускорения в зависимости от режима
    let accelerationInterval;
    if (CHANGE_TYPE === 'random') {
        accelerationInterval = setInterval(randomChangeAcceleration, getRandomInterval());
    } else {
        accelerationInterval = setInterval(fixedChangeAcceleration, CHANGE_INTERVAL);
    }

    // Сбор данных по минутам
    let minuteInterval = setInterval(recordMinuteData, 60000);
    
    // Первое изменение направления делаем сразу
    changeDirection();

    function moveCircle() {
        if (!isDuringTest) {
            clearInterval(movementInterval);
            return;
        }
        
        // Рассчитываем текущую скорость с учетом ускорения
        const currentSpeed = HORIZONTAL_MOVE * CURRENT_ACCELERATION;
        
        // Движение маркера
        if (getCurrentValuePX(circle, "left") > (globalRightBorder - 100)) {
            moveLeft(currentSpeed);
        } else if (getCurrentValuePX(circle, "left") < (globalLeftBorder + 100)) {
            moveRight(currentSpeed);
        } else if (currentDirection === 'left') {
            moveLeft(currentSpeed);
        } else {
            moveRight(currentSpeed);
        }
    }

    function changeDirection() {
        currentDirection = Math.floor(Math.random()*2) === 0 ? 'left' : 'right';
        lastDirectionChangeTime = new Date().getTime();
        userReacted = false;
    }
    
    function randomChangeAcceleration() {
        // Случайное изменение ускорения
        const change = Math.random() > 0.5 ? ACCELERATION_STEP : -ACCELERATION_STEP;
        CURRENT_ACCELERATION = Math.max(0.1, BASE_ACCELERATION + change);
        
        // Устанавливаем следующий случайный интервал
        clearInterval(accelerationInterval);
        accelerationInterval = setInterval(randomChangeAcceleration, getRandomInterval());
    }
    
    function fixedChangeAcceleration() {
        // Регулярное изменение ускорения
        const change = Math.random() > 0.5 ? ACCELERATION_STEP : -ACCELERATION_STEP;
        CURRENT_ACCELERATION = Math.max(0.1, BASE_ACCELERATION + change);
    }
    
    function getRandomInterval() {
        // Генерируем случайный интервал между 5 и 15 секундами
        return Math.floor(Math.random() * 10000) + 5000;
    }
    
    function recordMinuteData() {
        minuteData.reactionTimes.push([...reactionTimes]);
        reactionTimes = []; // Сбрасываем для следующей минуты
    }

    // Подсветка маркеров при нахождении круга в зоне
    let highlightInterval = setInterval(function() {
        let circleCenter = getCurrentValuePX(circle, "left") + (getCurrentValuePX(circle, "width")/2);
        if (circleCenter > leftBorder && circleCenter < rightBorder) {
            rectangle1.style.boxShadow = rectangleGlow;
            rectangle2.style.boxShadow = rectangleGlow;
        } else {
            rectangle1.style.boxShadow = "1px 1px 10px 5px darkred";
            rectangle2.style.boxShadow = "1px 1px 10px 5px darkred";
        }
    }, REFRESH_INTERVAL);

    function updateTimer() {
        let now = new Date().getTime();
        let distance = countDownTime - now;
        let seconds = Math.ceil(distance/1000);

        // Обновляем отображение таймера только если он включен в настройках
        if (elements.showTimer.checked) {
            document.getElementById("Timer").innerHTML = seconds + "s";
        }

        if (distance <= 0) {
            clearInterval(directionInterval);
            clearInterval(movementInterval);
            clearInterval(timer);
            clearInterval(highlightInterval);
            clearInterval(accelerationInterval);
            clearInterval(minuteInterval);
            if (elements.showTimer.checked) {
                document.getElementById("Timer").innerHTML = "Время вышло";
            }
            finish_test();
        }
    }
}

function finish_test() {
    isDuringTest = false;

    // Добавляем последнюю минуту данных
    if (reactionTimes.length > 0) {
        minuteData.reactionTimes.push([...reactionTimes]);
    }

    // Рассчитываем статистику по времени реакции
    let allReactionTimes = minuteData.reactionTimes.flat();
    let reactionStats = calculateReactionStats(allReactionTimes);
    
    // Форматируем общие результаты
    let resultsHTML = `
        <div class="results-container">
            <h3>Результаты теста</h3>
            <h4>Время реакции на изменение направления</h4>
            <table class="results-table">
                <tr><th>Среднее</th><th>Минимальное</th><th>Максимальное</th><th>Кол-во измерений</th></tr>
                <tr>
                    <td>${allReactionTimes.length > 0 ? reactionStats.average.toFixed(0) + ' мс' : 'Нет данных'}</td>
                    <td>${allReactionTimes.length > 0 ? reactionStats.min + ' мс' : 'Нет данных'}</td>
                    <td>${allReactionTimes.length > 0 ? reactionStats.max + ' мс' : 'Нет данных'}</td>
                    <td>${allReactionTimes.length}</td>
                </tr>
            </table>
    `;
    
    // Добавляем результаты по минутам, если выбрана соответствующая настройка
    if (elements.showMinuteResults.checked && minuteData.reactionTimes.length > 0) {
        resultsHTML += `<h4>Результаты по минутам</h4><table class="minute-results">`;
        
        minuteData.reactionTimes.forEach((minuteReactions, index) => {
            const minuteStats = calculateReactionStats(minuteReactions);
            
            resultsHTML += `
                <tr class="minute-header">
                    <th colspan="4">Минута ${index + 1}</th>
                </tr>
                <tr>
                    <td>Среднее: ${minuteReactions.length > 0 ? minuteStats.average.toFixed(0) + ' мс' : 'Нет данных'}</td>
                    <td>Мин: ${minuteReactions.length > 0 ? minuteStats.min + ' мс' : 'Нет данных'}</td>
                    <td>Макс: ${minuteReactions.length > 0 ? minuteStats.max + ' мс' : 'Нет данных'}</td>
                    <td>Кол-во: ${minuteReactions.length}</td>
                </tr>
            `;
        });
        
        resultsHTML += `</table>`;
    }
    
    resultsHTML += `</div>`;
    
    // Отображаем результаты
    document.getElementById('Final Result').innerHTML = resultsHTML;
    document.getElementById('Final Result').style.display = 'block';
    circle.style.display = 'none';
    document.getElementById('Upper_marker').style.display = 'none';
    document.getElementById('Lower_marker').style.display = 'none';
    document.getElementById('Timer').style.display = 'none';
    document.getElementById('Retry').style.display = 'block';
}

function calculateReactionStats(times) {
    if (times.length === 0) {
        return {
            average: 0,
            min: 0,
            max: 0
        };
    }
    
    const sum = times.reduce((a, b) => a + b, 0);
    const avg = sum / times.length;
    const min = Math.min(...times);
    const max = Math.max(...times);
    
    return {
        average: avg,
        min: min,
        max: max
    };
}