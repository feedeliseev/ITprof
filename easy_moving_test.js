const NUMBER_OF_ITERATIONS = 3;
let iteration = 0;
let testInterval;
let speedChangeInterval;
let testDuration = 5; // По умолчанию 5 минут (было 300 секунд)
let startTime;
let elapsedTime = 0;
let timerInterval;
let interval;
let currentSpeed = 1.0;
let accelerationStep = 0.5;
let currentAngle = 0;

const resultList = [];
const errorList = [];

function showSettings() {
    document.getElementById('Settings').style.display = 'block';
    document.getElementById('Start_button').style.display = 'none';
}

function updateDotSpeed(speedFactor) {
    const movingDotWrapper = document.getElementById('movingDotWrapper');

    const computedStyle = window.getComputedStyle(movingDotWrapper);
    const matrix = computedStyle.transform;

    if (matrix && matrix !== 'none') {
        const values = matrix.match(/matrix\((.+)\)/)[1].split(',');
        const a = parseFloat(values[0]);
        const b = parseFloat(values[1]);
        currentAngle = Math.atan2(b, a) * (180 / Math.PI);
        if (currentAngle < 0) currentAngle += 360;
    }

    const baseDuration = 5;
    const newDuration = baseDuration / speedFactor;

    movingDotWrapper.style.animation = 'none';
    movingDotWrapper.offsetHeight;

    movingDotWrapper.style.transform = `rotate(${currentAngle}deg)`;
    movingDotWrapper.style.animation = `rotate ${newDuration}s linear infinite`;
    movingDotWrapper.style.animationDelay = `-${(currentAngle / 360) * newDuration}s`;
}

function changeSpeed() {
    let newSpeed;
    const changeType = document.querySelector('input[name="changeType"]:checked').value;
    let direction;

    if (changeType === 'random') {
        direction = Math.random() > 0.5 ? 1 : -1;
    } else {
        direction = currentSpeed <= 1.0 ? 1 : -1;
    }

    newSpeed = currentSpeed + (direction * accelerationStep);
    newSpeed = Math.max(0.5, Math.min(3.0, newSpeed));

    currentSpeed = newSpeed;
    updateDotSpeed(currentSpeed);

    scheduleNextSpeedChange();
}

function scheduleNextSpeedChange() {
    const changeType = document.querySelector('input[name="changeType"]:checked').value;

    if (changeType === 'random') {
        const minInterval = 5;
        const maxInterval = 15;
        const randomInterval = (minInterval + Math.random() * (maxInterval - minInterval)) * 1000;
        clearTimeout(speedChangeInterval);
        speedChangeInterval = setTimeout(changeSpeed, randomInterval);
    } else {
        const fixedInterval = parseFloat(document.getElementById('changeInterval').value) * 1000;
        clearTimeout(speedChangeInterval);
        speedChangeInterval = setTimeout(changeSpeed, fixedInterval);
    }
}

function hideSettings() {
    document.getElementById('Settings').style.display = 'none';
    document.getElementById('Start_button').style.display = 'block';
    testDuration = parseInt(document.getElementById('testDuration').value) * 60; // Конвертируем минуты в секунды
    accelerationStep = parseFloat(document.getElementById('accelerationStep').value);
    currentSpeed = parseFloat(document.getElementById('accelerationInterval').value);
    updateDotSpeed(currentSpeed);
}

document.getElementById('accelerationInterval').addEventListener('input', function() {
    const newSpeed = parseFloat(this.value);
    if (!isNaN(newSpeed)) {
        currentSpeed = newSpeed;
        updateDotSpeed(currentSpeed);
    }
});

function start_test() {
    document.getElementById('Settings_button').style.display = 'none';
    startTime = Date.now();
    elapsedTime = 0;
    currentSpeed = parseFloat(document.getElementById('accelerationInterval').value);
    accelerationStep = parseFloat(document.getElementById('accelerationStep').value);
    testDuration = parseInt(document.getElementById('testDuration').value) * 60; // Конвертируем минуты в секунды

    updateDotSpeed(currentSpeed);

    if (document.getElementById('showTimer').checked) {
        timerInterval = setInterval(updateTimer, 100);
    }

    interval = setInterval(() => {
        elapsedTime = (Date.now() - startTime) / 1000;
        if (elapsedTime >= testDuration) {

            finish_test();
        }
    }, 100);

    scheduleNextSpeedChange();

    const showTimer = document.getElementById('showTimer').checked;
    const showProgress = document.getElementById('showProgress').checked;

    document.getElementById('Iteration').style.display =
        (showTimer || showProgress) ? 'block' : 'none';

    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';

    if (showProgress) {
        document.getElementById('Iteration').style.display = 'block';
        document.getElementById('Result').style.display = 'block';
    }

    document.getElementById('movingTest').style.display = 'block';
    document.addEventListener('keydown', next_time);
}

function updateTimer() {
    elapsedTime = (Date.now() - startTime) / 1000;
    const remainingTime = testDuration - elapsedTime;
    const minutes = Math.floor(remainingTime / 60);
    const seconds = Math.floor(remainingTime % 60);

    if (document.getElementById('showTimer').checked) {
        document.getElementById('Iteration').innerHTML =
            `Осталось: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    }
}

let lastSpaceTime = 0;
const SPACE_COOLDOWN = 500;

function next_time(e) {
    const currentTime = Date.now();

    if (e.code === 'Space' && currentTime - lastSpaceTime > SPACE_COOLDOWN && !e.repeat) {
        lastSpaceTime = currentTime;

        let movingDot = document.getElementById('movingDotWrapper');
        let animationDuration = window.getComputedStyle(movingDot).animationDuration;
        let rotationalSpeed = 2 * Math.PI / parseFloat(animationDuration);

        let matrixValues = window.getComputedStyle(movingDot).transform
                         .match(/matrix\((.+)\)/)[1].split(',');
        let angle = Math.atan2(parseFloat(matrixValues[1]), parseFloat(matrixValues[0]));

        let reactionError = angle / rotationalSpeed;

        if (Math.abs(reactionError) < 0.35) {
            resultList.push(reactionError);
            updateResultsDisplay(reactionError, false);
        } else {
            errorList.push(reactionError);
            updateResultsDisplay(null, true);
        }
    }
}

function updateResultsDisplay(error, isError) {
    const resultElement = document.getElementById('Result');
    if (document.getElementById('showProgress').checked) {
        const successCount = resultList.length;
        const errorCount = errorList.length;
        let text = `Результаты:<br>Удачные: ${successCount}<br>Ошибки: ${errorCount}`;

        if (!isError && !document.getElementById('showTimer').checked) {
            text += `<br>Текущий: ${error.toFixed(3)} сек`;
        }

        if (isError) text += "<br>Ошибка!";
        resultElement.innerHTML = text;
    } else {
        resultElement.innerHTML = isError ? "Ошибка!" : `Ваш результат: ${error.toFixed(3)} сек`;
    }
}

function finish_test() {
    document.getElementById('Settings_button').style.display = 'none';
    clearInterval(interval);
    clearInterval(timerInterval);
    clearTimeout(speedChangeInterval);
    document.removeEventListener('keydown', next_time);

    // Рассчет общего стандартного отклонения
    let sd, sdMinus, sdPlus;
    let dispersionMinusCounter = 0;
    let dispersionPlusCounter = 0;

    let dispersion = resultList.reduce((acc, number) => acc + Math.pow(number, 2), 0) / (resultList.length);
    let dispersionMinus = 0;
    let dispersionPlus = 0;
    resultList.forEach(elem => {
        if (elem < 0) {
            dispersionMinus += Math.pow(elem, 2);
            dispersionMinusCounter += 1;
        } else if (elem > 0) {
            dispersionPlus += Math.pow(elem, 2);
            dispersionPlusCounter += 1;
        }
    })
    sd = Math.sqrt(dispersion).toFixed(3);
    sdMinus = dispersionMinusCounter ? Math.sqrt(dispersionMinus / dispersionMinusCounter).toFixed(3) : "0";
    sdPlus = dispersionPlusCounter ? Math.sqrt(dispersionPlus / dispersionPlusCounter).toFixed(3) : "0";

    // Рассчет результатов по минутам
    let minuteResults = [];
    if (document.getElementById('showMinuteResults').checked) {
        const totalMinutes = Math.ceil(testDuration / 60);
        const minuteInterval = 60; // 60 секунд в минуте

        for (let minute = 0; minute < totalMinutes; minute++) {
            const startSecond = minute * minuteInterval;
            const endSecond = (minute + 1) * minuteInterval;

            // Фильтруем результаты для текущей минуты
            const minuteReactions = resultList.filter((_, index) => {
                const reactionTime = (errorList[index] ? errorList[index] : resultList[index]);
                return reactionTime >= startSecond && reactionTime < endSecond;
            });

            // Рассчитываем статистику для минуты
            let minuteSd = "0";
            let minuteSdMinus = "0";
            let minuteSdPlus = "0";

            if (minuteReactions.length > 0) {
                // Общее отклонение
                const minuteDispersion = minuteReactions.reduce((acc, number) => acc + Math.pow(number, 2), 0) / minuteReactions.length;
                minuteSd = Math.sqrt(minuteDispersion).toFixed(3);

                // Отрицательное отклонение
                const minusReactions = minuteReactions.filter(x => x < 0);
                if (minusReactions.length > 0) {
                    const minusDispersion = minusReactions.reduce((acc, number) => acc + Math.pow(number, 2), 0) / minusReactions.length;
                    minuteSdMinus = Math.sqrt(minusDispersion).toFixed(3);
                }

                // Положительное отклонение
                const plusReactions = minuteReactions.filter(x => x > 0);
                if (plusReactions.length > 0) {
                    const plusDispersion = plusReactions.reduce((acc, number) => acc + Math.pow(number, 2), 0) / plusReactions.length;
                    minuteSdPlus = Math.sqrt(plusDispersion).toFixed(3);
                }
            }

            minuteResults.push({
                minute: minute + 1,
                count: minuteReactions.length,
                sd: minuteSd,
                sdMinus: minuteSdMinus,
                sdPlus: minuteSdPlus
            });
        }
    }

    document.getElementById('movingTest').style.display = 'none';
    document.getElementById('Iteration').style.display = 'none';
    document.getElementById('Result').style.display = 'block';
    document.getElementById('Result').style.color = 'black';
    document.getElementById('Result').style.margin = '0 auto';
    document.getElementById('Result').style.width = '80%';

    // Формируем итоговый результат
    let resultHTML = `
        <div class="results-container" style="text-align: center; margin: 0 auto;">
            <h3 style="text-align: center;">Результаты теста</h3>
            <p style="text-align: center;">Удачные попытки: ${resultList.length}</p>
            <p style="text-align: center;">Ошибки: ${errorList.length}</p>
            
            <h4 style="text-align: center; margin-top: 20px;">Общие результаты</h4>
            <table style="margin: 0 auto; border-collapse: collapse;">
                <tr><th></th><th>Общее</th><th>Минус</th><th>Плюс</th></tr>
                <tr>
                    <td>Стандартное отклонение</td>
                    <td>${sd}</td>
                    <td>${sdMinus}</td>
                    <td>${sdPlus}</td>
                </tr>
            </table>`;

    // Добавляем результаты по минутам, если нужно
    if (document.getElementById('showMinuteResults').checked && minuteResults.length > 0) {
        resultHTML += `
            <h4 style="text-align: center; margin-top: 30px;">Результаты по минутам</h4>
            <table style="margin: 0 auto; border-collapse: collapse;">
                <tr>
                    <th>Минута</th>
                    <th>Попыток</th>
                    <th>Общее СКО</th>
                    <th>СКО минус</th>
                    <th>СКО плюс</th>
                </tr>`;

        minuteResults.forEach(result => {
            resultHTML += `
                <tr>
                    <td>${result.minute}</td>
                    <td>${result.count}</td>
                    <td>${result.sd}</td>
                    <td>${result.sdMinus}</td>
                    <td>${result.sdPlus}</td>
                </tr>`;
        });

        resultHTML += `</table>`;
    }

    resultHTML += `</div>`;

    document.getElementById('Result').innerHTML = resultHTML;
    document.getElementById('Retry').style.display = 'block';
    document.getElementById('Resultcab').style.display = 'block';

    if (isNaN(sd)) {
        sd = 0;
    }
    submitMovingResult(parseFloat(sd), TEST_ID);
}
function submitMovingResult(sd, testId) {
    fetch('submit_result2.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            test_id: testId,
            sd: sd
        })
    })
        .then(response => response.text())
        .then(alert)
        .catch(err => console.error('Ошибка отправки результата:', err));
}