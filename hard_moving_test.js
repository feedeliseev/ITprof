let iteration = 0;
let testDuration = 60;
let currentSpeed = 1.0;
let accelerationStep = 0.5;
let testStartTime;
let testTimeout;
let timerInterval;
let speedChangeInterval;
let changeType = 'random';
let changeInterval = 10;
let rotationAngles = {
    slow: 0,
    middle: 0,
    fast: 0
};

const resultList = {
    slow: [],
    middle: [],
    fast: []
};

const elements = {
    resultcab: document.getElementById('Resultcab'),
    settings: document.getElementById('Settings'),
    startButton: document.getElementById('Start_button'),
    settingsButton: document.getElementById('Settings_button'),
    instruction: document.getElementById('Instruction'),
    movingTest: document.getElementById('movingTest'),
    retry: document.getElementById('Retry'),
    iteration: document.getElementById('Iteration'),
    result: document.getElementById('Result'),
    testDuration: document.getElementById('testDuration'),
    accelerationInterval: document.getElementById('accelerationInterval'),
    accelerationStep: document.getElementById('accelerationStep'),
    showTimer: document.getElementById('showTimer'),
    showProgress: document.getElementById('showProgress'),
    showMinuteResults: document.getElementById('showMinuteResults'),
    changeInterval: document.getElementById('changeInterval'),
    slowMovingDot: document.getElementById('slowMovingDot'),
    middleMovingDot: document.getElementById('middleMovingDot'),
    fastMovingDot: document.getElementById('fastMovingDot')
};

function showSettings() {
    elements.settings.style.display = 'block';
    elements.startButton.style.display = 'none';
}

function hideSettings() {
    elements.settings.style.display = 'none';
    elements.startButton.style.display = 'block';
    testDuration = parseInt(elements.testDuration.value) * 60;
    accelerationStep = parseFloat(elements.accelerationStep.value);
    currentSpeed = parseFloat(elements.accelerationInterval.value);
    changeType = document.querySelector('input[name="changeType"]:checked').value;
    changeInterval = parseInt(elements.changeInterval.value);
    
    updateDotSpeed(currentSpeed);
}

function start_test() {
    iteration = 0;
    resultList.slow = [];
    resultList.middle = [];
    resultList.fast = [];
    
    elements.settingsButton.style.display = 'none';
    elements.instruction.style.display = 'none';
    elements.startButton.style.display = 'none';
    elements.movingTest.style.display = 'block';
    elements.retry.style.display = 'none';
    
    const showTimer = elements.showTimer.checked;
    const showProgress = elements.showProgress.checked;
    
    elements.iteration.style.display = showTimer ? 'block' : 'none';
    
    testStartTime = Date.now();
    
    if (showTimer) {
        updateTimerDisplay();
        timerInterval = setInterval(updateTimerDisplay, 1000);
    }
    
    elements.result.style.display = showProgress ? 'block' : 'none';
    
    testTimeout = setTimeout(() => {
        if (showTimer) clearInterval(timerInterval);
        finish_test();
    }, testDuration * 1000);
    
    // Инициализация углов вращения
    rotationAngles = {
        slow: getCurrentRotation(elements.slowMovingDot),
        middle: getCurrentRotation(elements.middleMovingDot),
        fast: getCurrentRotation(elements.fastMovingDot)
    };
    
    startSpeedChanges();
    document.addEventListener('keydown', next_time);
}

function getCurrentRotation(element) {
    const matrix = window.getComputedStyle(element).transform;
    if (matrix === 'none') return 0;
    const values = matrix.split('(')[1].split(')')[0].split(',');
    const a = parseFloat(values[0]);
    const b = parseFloat(values[1]);
    return Math.atan2(b, a) * (180 / Math.PI);
}

function startSpeedChanges() {
    if (speedChangeInterval) {
        if (changeType === 'random') {
            clearTimeout(speedChangeInterval);
        } else {
            clearInterval(speedChangeInterval);
        }
    }
    
    if (changeType === 'random') {
        scheduleRandomSpeedChange();
    } else {
        speedChangeInterval = setInterval(changeSpeed, changeInterval * 1000);
    }
}

function scheduleRandomSpeedChange() {
    const randomInterval = Math.floor(Math.random() * 10000) + 5000;
    speedChangeInterval = setTimeout(() => {
        changeSpeed();
        scheduleRandomSpeedChange();
    }, randomInterval);
}

function changeSpeed() {
    // Сохраняем текущие углы вращения
    rotationAngles = {
        slow: getCurrentRotation(elements.slowMovingDot),
        middle: getCurrentRotation(elements.middleMovingDot),
        fast: getCurrentRotation(elements.fastMovingDot)
    };
    
    // Изменяем скорость без возврата назад
    const changeOptions = [0, accelerationStep]; // Только увеличение или без изменений
    const randomChange = changeOptions[Math.floor(Math.random() * changeOptions.length)];
    currentSpeed = Math.max(0.5, currentSpeed + randomChange);
    
    // Применяем новую скорость с сохранением позиции
    updateDotSpeed(currentSpeed);
}

function updateDotSpeed(speed) {
    // Обновляем скорость с сохранением текущего угла вращения
    elements.slowMovingDot.style.transform = `rotate(${rotationAngles.slow}deg)`;
    elements.slowMovingDot.style.animationDuration = `${3/speed}s`;
    elements.slowMovingDot.style.animationPlayState = 'running';
    
    elements.middleMovingDot.style.transform = `rotate(${rotationAngles.middle}deg)`;
    elements.middleMovingDot.style.animationDuration = `${2/speed}s`;
    elements.middleMovingDot.style.animationPlayState = 'running';
    
    elements.fastMovingDot.style.transform = `rotate(${rotationAngles.fast}deg)`;
    elements.fastMovingDot.style.animationDuration = `${1/speed}s`;
    elements.fastMovingDot.style.animationPlayState = 'running';
}

function updateTimerDisplay() {
    const elapsedSeconds = Math.floor((Date.now() - testStartTime) / 1000);
    const remainingSeconds = Math.max(0, testDuration - elapsedSeconds);
    
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    
    elements.iteration.innerHTML = 'Время: ' + 
        minutes + ':' + seconds.toString().padStart(2, '0') + 
        ' / ' + Math.floor(testDuration/60) + ':' + (testDuration%60).toString().padStart(2, '0');
    
    if (remainingSeconds <= 0) {
        clearInterval(timerInterval);
        finish_test();
    }
}

function next_time(e) {
    const showProgress = elements.showProgress.checked;
    const result = handleKeyPress(e);
    
    if (showProgress && result !== null) {
        showResult(e.code, result);
    }
}

function showResult(keyCode, result) {
    let speed;
    switch (keyCode) {
        case 'KeyA': speed = 'slow'; break;
        case 'KeyS': speed = 'middle'; break;
        case 'KeyD': speed = 'fast'; break;
        default: return;
    }

    elements.result.style.color = 'black';
    elements.result.innerHTML = 'Ваш результат: ' + result.toFixed(3) + ' с';
}

function handleKeyPress(e) {
    let speed;
    switch (e.code) {
        case 'KeyA': speed = 'slow'; break;
        case 'KeyS': speed = 'middle'; break;
        case 'KeyD': speed = 'fast'; break;
        default: return null;
    }
    
    return calculate_wrong(speed);
}

function calculate_wrong(speed) {
    let targetMovingDot = elements[speed + 'MovingDot'];
    targetMovingDot.children[0].classList.remove('dot_highlight');

    let rotationalSpeed = 2 * Math.PI / (parseFloat(window.getComputedStyle(targetMovingDot).animationDuration.slice(0, -1)));

    let angle = getCurrentRotation(targetMovingDot) * (Math.PI / 180);
    let wrong = angle / rotationalSpeed;
    
    if (wrong < 0.35 && wrong > -0.35) {
        resultList[speed].push(wrong);
        targetMovingDot.children[0].classList.add('dot_highlight');
        return wrong;
    }
    return null;
}

function finish_test() {
    document.removeEventListener('keydown', next_time);
    clearTimeout(testTimeout);
    
    if (elements.showTimer.checked) {
        clearInterval(timerInterval);
    }
    
    if (speedChangeInterval) {
        if (changeType === 'random') {
            clearTimeout(speedChangeInterval);
        } else {
            clearInterval(speedChangeInterval);
        }
    }
    
    elements.movingTest.style.display = 'none';
    // Всегда показываем результаты в конце теста, независимо от showProgress
    elements.result.style.display = 'block';
    elements.result.style.color = 'black';
    elements.result.style.margin = '0 auto';
    elements.result.style.width = '80%';
    
    // Разделяем результаты по минутам
    const minuteResults = {};
    const totalMinutes = Math.ceil(testDuration / 60);
    
    for (let minute = 1; minute <= totalMinutes; minute++) {
        minuteResults[minute] = {
            slow: [],
            middle: [],
            fast: []
        };
    }
    
    // Распределяем результаты по минутам
    for (let speed in resultList) {
        resultList[speed].forEach((result, index) => {
            const resultTime = index * (testDuration * 1000 / resultList[speed].length);
            const minute = Math.floor(resultTime / 60000) + 1;
            if (minute <= totalMinutes) {
                minuteResults[minute][speed].push(result);
            }
        });
    }
    
    // Рассчитываем статистику для общего результата
    let sd = {slow: 0, middle: 0, fast: 0};
    let sdMinus = {slow: 0, middle: 0, fast: 0};
    let sdPlus = {slow: 0, middle: 0, fast: 0};
    
    for (let result in resultList) {
        let dispersion = resultList[result].reduce((acc, number) => acc + Math.pow(number, 2), 0) / (resultList[result].length);
        let dispersionMinus = 0, dispersionPlus = 0;
        let minusCount = 0, plusCount = 0;
        
        resultList[result].forEach(elem => {
            if (elem < 0) {
                dispersionMinus += Math.pow(elem, 2);
                minusCount++;
            } else if (elem > 0) {
                dispersionPlus += Math.pow(elem, 2);
                plusCount++;
            }
        });
        
        sd[result] = Math.sqrt(dispersion).toFixed(3);
        sdMinus[result] = minusCount ? Math.sqrt(dispersionMinus / minusCount).toFixed(3) : "0";
        sdPlus[result] = plusCount ? Math.sqrt(dispersionPlus / plusCount).toFixed(3) : "0";
    }

    let average = Object.values(sd).filter(v => v !== "0").map(parseFloat);
    let averageReduced = average.length ? (average.reduce((acc, num) => acc + num, 0) / average.length).toFixed(3) : "0";

    // Формируем HTML с результатами
    let resultsHTML = `
        <div class="results-container" style="text-align: center; margin: 0 auto;">
            <h3 style="text-align: center;">Результаты теста</h3>
            <h4 style="text-align: center;">Общие результаты</h4>
            <table style="margin: 0 auto; border-collapse: collapse;">
                <tr><th></th><th>Общее</th><th>Минус</th><th>Плюс</th></tr>
                <tr><td>Медленный круг</td><td>${sd.slow}</td><td>${sdMinus.slow}</td><td>${sdPlus.slow}</td></tr>
                <tr><td>Средний круг</td><td>${sd.middle}</td><td>${sdMinus.middle}</td><td>${sdPlus.middle}</td></tr>
                <tr><td>Быстрый круг</td><td>${sd.fast}</td><td>${sdMinus.fast}</td><td>${sdPlus.fast}</td></tr>
            </table>
    `;

    // Всегда показываем результаты по минутам, если включена соответствующая опция
    if (elements.showMinuteResults.checked && totalMinutes > 1) {
        resultsHTML += `<h4 style="text-align: center; margin-top: 20px;">Результаты по минутам</h4>`;
        
        for (let minute = 1; minute <= totalMinutes; minute++) {
            const minuteData = minuteResults[minute];
            let minuteSd = {slow: "0", middle: "0", fast: "0"};
            
            for (let speed in minuteData) {
                if (minuteData[speed].length > 0) {
                    const dispersion = minuteData[speed].reduce((acc, num) => acc + Math.pow(num, 2), 0) / minuteData[speed].length;
                    minuteSd[speed] = Math.sqrt(dispersion).toFixed(3);
                }
            }
            
            resultsHTML += `
                <div style="margin-top: 15px;">
                    <h5 style="margin-bottom: 5px;">Минута ${minute}</h5>
                    <table style="margin: 0 auto; border-collapse: collapse;">
                        <tr><th></th><th>Отклонение</th><th>Попыток</th></tr>
                        <tr><td>Медленный</td><td>${minuteSd.slow}</td><td>${minuteResults[minute].slow.length}</td></tr>
                        <tr><td>Средний</td><td>${minuteSd.middle}</td><td>${minuteResults[minute].middle.length}</td></tr>
                        <tr><td>Быстрый</td><td>${minuteSd.fast}</td><td>${minuteResults[minute].fast.length}</td></tr>
                    </table>
                </div>
            `;
        }
    }

    resultsHTML += `
            <p style="text-align: center; margin-top: 20px;">Среднее отклонение: ${averageReduced} с.</p>
            <p style="text-align: center;">Финальная скорость: ${currentSpeed.toFixed(1)}x</p>
        </div>
    `;

    elements.result.innerHTML = resultsHTML;
    elements.retry.style.display = 'block';
    elements.resultcab.style.display = 'block';
    elements.settingsButton.style.display = 'block';
    elements.startButton.style.display = 'none';
    elements.settingsButton.style.display = 'none';

}