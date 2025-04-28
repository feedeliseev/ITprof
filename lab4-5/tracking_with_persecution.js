// Основные настройки
let TEST_DURATION = 20000; // Начальное значение по умолчанию (мс)
const WAIT_TIME = 200; // Задержка перед началом движения цели (мс)
let HORIZONTAL_MOVE = 0.8; // Базовая скорость движения цели (пикселей за шаг)
const HORIZONTAL_USER_MOVE = 25; // Скорость движения перекрестия (пикселей за шаг)
let DIRECTION_CHANGE_INTERVAL = 800; // Интервал смены направления (мс)
const REFRESH_INTERVAL = 3; // Интервал обновления (мс)

// Получаем элементы DOM
const globalContainer = document.getElementById('tracking_with_persecution');
const target = document.getElementById('Target');
const crosshair = document.getElementById('Crosshair');
const rectangle1 = document.getElementById('Upper_marker');
const rectangle2 = document.getElementById('Lower_marker');
const rectangleGlow = "1px 1px 10px 5px #888888";
rectangle1.style.boxShadow = rectangleGlow;
rectangle2.style.boxShadow = rectangleGlow;

// Состояние теста
let isDuringTest = false;
let countDownTime;
let globalLeftBorder = 0;
let globalRightBorder = getCurrentValuePX(globalContainer, "width");
let circleLeftBorder = getCurrentValuePX(target, "left");
let circleRightBorder = getCurrentValuePX(target, "left") + getCurrentValuePX(target, "width");

// Результаты
let reactionTimes = []; // Массив для хранения времени реакции
let minuteResults = []; // Массив для хранения результатов по минутам
let lastDirectionChangeTime = 0; // Время последней смены направления
let isTargetInCrosshair = false; // Флаг нахождения цели в перекрестии
let currentSpeed = HORIZONTAL_MOVE; // Текущая скорость цели
let speedChangeTimer = null; // Таймер изменения скорости
let directionChangeTimer = null; // Таймер смены направления
let testStartTime = 0; // Время начала теста
let lastMinuteCheck = 0; // Время последней проверки минуты

// Новые переменные для устойчивости слежения
let currentTrackingStart = 0; // Время начала текущего периода слежения
let maxTrackingDuration = 0; // Максимальное время непрерывного слежения
let trackingDurations = []; // Все периоды слежения

// Обработка клавиш управления
document.addEventListener('keydown', function(event) {
    if (isDuringTest) {
        switch (event.code) {
            case "KeyA":
                moveLeft(crosshair, HORIZONTAL_USER_MOVE);
                break;
            case "KeyD":
                moveRight(crosshair, HORIZONTAL_USER_MOVE);
                break;
        }
    }
});

// Функции движения
function moveLeft(element, change_value) {
    let curr_left_value = getCurrentValuePX(element, "left");
    let new_left_value = curr_left_value - change_value;
    element.style.left = new_left_value + "px";
}

function moveRight(element, change_value) {
    let curr_left_value = getCurrentValuePX(element, "left");
    let new_left_value = curr_left_value + change_value;
    element.style.left = new_left_value + "px";
}

// Получение текущего значения свойства в пикселях
function getCurrentValuePX(element, property) {
    let style = getComputedStyle(element);
    let curr_left = style.getPropertyValue(property);
    return parseFloat(curr_left.replace("px", ""));
}

// Изменение скорости цели
function changeTargetSpeed() {
    const accelerationStep = parseFloat(document.getElementById('accelerationStep').value) || 0.5;
    const changeType = document.querySelector('input[name="changeType"]:checked').value;
    
    const changeDirection = Math.random() > 0.5 ? 1 : -1;
    currentSpeed = HORIZONTAL_MOVE + (changeDirection * accelerationStep);
    
    if (currentSpeed < 0.1) currentSpeed = 0.1;
    
    if (changeType === 'random') {
        const randomInterval = 1000 + Math.random() * 5000;
        speedChangeTimer = setTimeout(changeTargetSpeed, randomInterval);
    } else {
        const fixedInterval = (parseInt(document.getElementById('changeInterval').value) || 10) * 1000;
        speedChangeTimer = setTimeout(changeTargetSpeed, fixedInterval);
    }
}

// Управление настройками
function showSettings() {
    document.getElementById('Settings').style.display = 'block';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('Settings_button').style.display = 'none';
}

function hideSettings() {
    document.getElementById('Settings').style.display = 'none';
    document.getElementById('Start_button').style.display = 'block';
    document.getElementById('Settings_button').style.display = 'block';
}

function start_test() {
    // Сброс результатов
    reactionTimes = [];
    minuteResults = [];
    currentSpeed = HORIZONTAL_MOVE;
    maxTrackingDuration = 0;
    trackingDurations = [];
    currentTrackingStart = 0;
    
    // Установка параметров из настроек
    const durationMinutes = parseFloat(document.getElementById('testDuration').value);
    TEST_DURATION = durationMinutes * 60000;
    HORIZONTAL_MOVE = parseFloat(document.getElementById('accelerationInterval').value) || 0.8;
    currentSpeed = HORIZONTAL_MOVE;
    
    // Настройка интерфейса
    isDuringTest = true;
    testStartTime = Date.now();
    lastMinuteCheck = 0;
    const showTimer = document.getElementById('showTimer').checked;

    if (showTimer) {
        document.getElementById('Timer').style.display = 'flex';
    } else {
        document.getElementById('Timer').style.display = 'none';
    }

    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('Settings_button').style.display = 'none';
    document.getElementById('bg').style.background = 'white';
    document.getElementById('Settings').style.display = 'none';
    target.style.display = 'flex';
    crosshair.style.display = 'flex';
    document.getElementById('Upper_marker').style.display = 'flex';
    document.getElementById('Lower_marker').style.display = 'flex';
    document.getElementById('Lower_marker').style.top = '3vh';
    document.getElementById('Final Result').style.display = 'none';
    document.getElementById('Retry').style.display = 'none';

    if (showTimer) {
        document.getElementById("Timer").innerHTML = (TEST_DURATION / 1000) + "s";
    }

    setTimeout(function(){
        if (isDuringTest) {
            lastDirectionChangeTime = Date.now();
            
            changeTargetSpeed();
            
            function changeDirection() {
                const rand_direction = Math.floor(Math.random()*2);
                const iterations = DIRECTION_CHANGE_INTERVAL/REFRESH_INTERVAL;
                let iterator = 0;
                lastDirectionChangeTime = Date.now();

                let movement_change = setInterval(function (){
                    if (!isDuringTest) {
                        clearInterval(movement_change);
                        return;
                    }
                    
                    iterator++;
                    if (getCurrentValuePX(target, "left") > (globalRightBorder - 100)) {
                        moveLeft(target, currentSpeed);
                    } else if (getCurrentValuePX(target, "left") < (globalLeftBorder + 100)) {
                        moveRight(target, currentSpeed);
                    } else if (rand_direction === 0) {
                        moveLeft(target, currentSpeed);
                    } else if (rand_direction === 1) {
                        moveRight(target, currentSpeed);
                    }
                    
                    if (iterator >= iterations) {
                        clearInterval(movement_change);
                        if (isDuringTest) {
                            changeDirection();
                        }
                    }
                }, REFRESH_INTERVAL);
            }
            
            directionChangeTimer = setTimeout(changeDirection, 0);

            let countScore = setInterval(function () {
                if (!isDuringTest) {
                    clearInterval(countScore);
                    return;
                }
                
                let crosshairCenter = getCurrentValuePX(crosshair, "left") + (getCurrentValuePX(crosshair, "width")/2);
                circleLeftBorder = getCurrentValuePX(target, "left");
                circleRightBorder = getCurrentValuePX(target, "left") + getCurrentValuePX(target, "width");
                
                const nowInCrosshair = crosshairCenter > circleLeftBorder && crosshairCenter < circleRightBorder;
                
                // Логика отслеживания устойчивости слежения
                if (nowInCrosshair) {
                    if (!isTargetInCrosshair) {
                        // Начало нового периода слежения
                        currentTrackingStart = Date.now();
                    } else {
                        // Продолжение слежения - обновляем максимальную длительность
                        const currentDuration = Date.now() - currentTrackingStart;
                        if (currentDuration > maxTrackingDuration) {
                            maxTrackingDuration = currentDuration;
                        }
                    }
                    
                    if (!isTargetInCrosshair) {
                        const reactionTime = Date.now() - lastDirectionChangeTime;
                        if (reactionTime > 50) {
                            reactionTimes.push({
                                time: Date.now() - testStartTime,
                                reactionTime: reactionTime
                            });
                        }
                    }
                    target.style.boxShadow = "1px 1px 10px 5px darkgreen";
                } else {
                    if (isTargetInCrosshair) {
                        // Завершение периода слежения
                        const trackingDuration = Date.now() - currentTrackingStart;
                        trackingDurations.push(trackingDuration);
                        if (trackingDuration > maxTrackingDuration) {
                            maxTrackingDuration = trackingDuration;
                        }
                    }
                    target.style.boxShadow = "1px 1px 10px 5px darkred";
                }
                
                isTargetInCrosshair = nowInCrosshair;
                
                // Улучшенная логика подсчета по минутам
                const currentTime = Date.now() - testStartTime;
                const currentMinute = Math.floor(currentTime / 60000);
                
                if (currentMinute > lastMinuteCheck) {
                    const minuteStart = lastMinuteCheck * 60000;
                    const minuteEnd = (lastMinuteCheck + 1) * 60000;
                    
                    const minuteReactions = reactionTimes.filter(item => 
                        item.time >= minuteStart && item.time < minuteEnd
                    );
                    
                    // Расчет устойчивости слежения за минуту
                    const minuteTrackingPeriods = trackingDurations.filter(time => 
                        time >= minuteStart && time < minuteEnd
                    );
                    const minuteMaxTracking = minuteTrackingPeriods.length > 0 ? 
                        Math.max(...minuteTrackingPeriods) : 0;
                    
                    if (minuteReactions.length > 0 || document.getElementById('showMinuteResults').checked) {
                        let avgReaction = 0;
                        let minReaction = 0;
                        let maxReaction = 0;
                        
                        if (minuteReactions.length > 0) {
                            avgReaction = minuteReactions.reduce((sum, item) => sum + item.reactionTime, 0) / minuteReactions.length;
                            minReaction = Math.min(...minuteReactions.map(item => item.reactionTime));
                            maxReaction = Math.max(...minuteReactions.map(item => item.reactionTime));
                        }
                        
                        minuteResults.push({
                            minute: lastMinuteCheck + 1,
                            avgReaction: avgReaction,
                            minReaction: minReaction,
                            maxReaction: maxReaction,
                            count: minuteReactions.length,
                            maxTrackingDuration: minuteMaxTracking
                        });
                    }
                    
                    lastMinuteCheck = currentMinute;
                }
            }, REFRESH_INTERVAL);

            countDownTime = new Date().getTime() + TEST_DURATION;
            let timer = setInterval(function() {
                if (!isDuringTest) {
                    clearInterval(timer);
                    return;
                }
                
                let now = new Date().getTime();
                let distance = countDownTime - now;
                let seconds = Math.round(distance/1000);

                if (showTimer) {
                    document.getElementById("Timer").innerHTML = seconds + "s";
                }

                if (distance <= 0) {
                    clearInterval(timer);
                    if (showTimer) {
                        document.getElementById("Timer").innerHTML = "Время вышло";
                    }
                    finish_test();
                }
            }, REFRESH_INTERVAL);
        }
    }, WAIT_TIME);
}

function finish_test() {
    isDuringTest = false;
    
    if (speedChangeTimer) clearTimeout(speedChangeTimer);
    if (directionChangeTimer) clearTimeout(directionChangeTimer);

    if (document.getElementById('showTimer').checked) {
        document.getElementById('Timer').style.display = 'none';
    }

    // Рассчет статистики
    const totalSeconds = TEST_DURATION / 1000;
    const totalMinutes = Math.ceil(totalSeconds / 60);
    
    let reactionStats = {
        average: 0,
        min: 0,
        max: 0,
        count: reactionTimes.length
    };
    
    if (reactionTimes.length > 0) {
        reactionStats.average = reactionTimes.reduce((a, b) => a + b.reactionTime, 0) / reactionTimes.length;
        reactionStats.min = Math.min(...reactionTimes.map(item => item.reactionTime));
        reactionStats.max = Math.max(...reactionTimes.map(item => item.reactionTime));
    }

    // Расчет общего процента времени в цели
    const totalTimeInTarget = trackingDurations.reduce((sum, duration) => sum + duration, 0);
    const trackingPercentage = (totalTimeInTarget / TEST_DURATION * 100).toFixed(1);

    // Формирование HTML с результатами
    let resultsHTML = `
        <div class="results-container">
            <h3>Общие результаты теста</h3>
            <table class="results-table">
                <tr>
                    <th>Метрика</th>
                    <th>Значение</th>
                </tr>
                <tr>
                    <td>1. Среднее время реакции на изменение движения</td>
                    <td>${reactionStats.count > 0 ? reactionStats.average.toFixed(0) + ' мс' : 'Нет данных'}</td>
                </tr>
                <tr>
                    <td>2. Минимальное время реакции</td>
                    <td>${reactionStats.count > 0 ? reactionStats.min + ' мс' : 'Нет данных'}</td>
                </tr>
                <tr>
                    <td>3. Максимальное время реакции</td>
                    <td>${reactionStats.count > 0 ? reactionStats.max + ' мс' : 'Нет данных'}</td>
                </tr>
                <tr>
                    <td>4. Всего изменений направления</td>
                    <td>${reactionStats.count}</td>
                </tr>
                <tr>
                    <td>5. Устойчивость слежения (макс. время)</td>
                    <td>${maxTrackingDuration > 0 ? maxTrackingDuration + ' мс' : 'Нет данных'}</td>
                </tr>
                <tr>
                    <td>6. Процент времени в цели</td>
                    <td>${trackingPercentage}%</td>
                </tr>
            </table>
    `;

    // Добавляем результаты по минутам
    const showMinuteResults = document.getElementById('showMinuteResults').checked;
    if (showMinuteResults && minuteResults.length > 0) {
        resultsHTML += `<h3>Результаты по минутам</h3>`;
        
        minuteResults.forEach(minute => {
            resultsHTML += `
                <h4>Минута ${minute.minute}</h4>
                <table class="results-table">
                    <tr>
                        <th>Метрика</th>
                        <th>Значение</th>
                    </tr>
                    <tr>
                        <td>Среднее время реакции</td>
                        <td>${minute.avgReaction > 0 ? minute.avgReaction.toFixed(0) + ' мс' : 'Нет данных'}</td>
                    </tr>
                    <tr>
                        <td>Минимальное время</td>
                        <td>${minute.minReaction > 0 ? minute.minReaction + ' мс' : 'Нет данных'}</td>
                    </tr>
                    <tr>
                        <td>Максимальное время</td>
                        <td>${minute.maxReaction > 0 ? minute.maxReaction + ' мс' : 'Нет данных'}</td>
                    </tr>
                    <tr>
                        <td>Количество изменений</td>
                        <td>${minute.count}</td>
                    </tr>
                    <tr>
                        <td>Устойчивость слежения</td>
                        <td>${minute.maxTrackingDuration > 0 ? minute.maxTrackingDuration + ' мс' : 'Нет данных'}</td>
                    </tr>
                </table>
            `;
        });
    }

    resultsHTML += `</div>`;

    // Отображение результатов
    document.getElementById('Final Result').innerHTML = resultsHTML;
    document.getElementById('Final Result').style.display = 'block';
    target.style.display = 'none';
    crosshair.style.display = 'none';
    document.getElementById('Upper_marker').style.display = 'none';
    document.getElementById('Lower_marker').style.display = 'none';
    document.getElementById('Timer').style.display = 'none';
    document.getElementById('Retry').style.display = 'block';
}