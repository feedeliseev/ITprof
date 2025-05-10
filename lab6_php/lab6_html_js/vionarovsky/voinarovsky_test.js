// voinarovsky_test.js
let timerInterval;
let testEndTime;
let correctAnswers = 0;
let totalAnswered = 0;
let answers = []; // Массив для хранения ответов пользователя

const TOTAL_QUESTIONS = 30; // Общее количество вопросов в тесте

const TRUE_RESULT = new Array(90);
    TRUE_RESULT[1] = 0; TRUE_RESULT[2] = 1; TRUE_RESULT[3] = 0;
    TRUE_RESULT[4] = 0; TRUE_RESULT[5] = 1; TRUE_RESULT[6] = 0;
    TRUE_RESULT[7] = 0; TRUE_RESULT[8] = 0; TRUE_RESULT[9] = 1;
    TRUE_RESULT[10] = 1; TRUE_RESULT[11] = 0; TRUE_RESULT[12] = 0;
    TRUE_RESULT[13] = 0; TRUE_RESULT[14] = 0; TRUE_RESULT[15] = 1;
    TRUE_RESULT[16] = 1; TRUE_RESULT[17] = 0; TRUE_RESULT[18] = 0;
    TRUE_RESULT[19] = 1; TRUE_RESULT[20] = 0; TRUE_RESULT[21] = 0;
    TRUE_RESULT[22] = 0; TRUE_RESULT[23] = 1; TRUE_RESULT[24] = 0;
    TRUE_RESULT[25] = 0; TRUE_RESULT[26] = 0; TRUE_RESULT[27] = 1;
    TRUE_RESULT[28] = 0; TRUE_RESULT[29] = 0; TRUE_RESULT[30] = 1;
    TRUE_RESULT[31] = 0; TRUE_RESULT[32] = 0; TRUE_RESULT[33] = 1;
    TRUE_RESULT[34] = 1; TRUE_RESULT[35] = 0; TRUE_RESULT[36] = 0;
    TRUE_RESULT[37] = 0; TRUE_RESULT[38] = 0; TRUE_RESULT[39] = 1;
    TRUE_RESULT[40] = 0; TRUE_RESULT[41] = 1; TRUE_RESULT[42] = 0;
    TRUE_RESULT[43] = 0; TRUE_RESULT[44] = 1; TRUE_RESULT[45] = 0;
    TRUE_RESULT[46] = 0; TRUE_RESULT[47] = 1; TRUE_RESULT[48] = 0;
    TRUE_RESULT[49] = 0; TRUE_RESULT[50] = 1; TRUE_RESULT[51] = 0;
    TRUE_RESULT[52] = 0; TRUE_RESULT[53] = 0; TRUE_RESULT[54] = 1;
    TRUE_RESULT[55] = 1; TRUE_RESULT[56] = 0; TRUE_RESULT[57] = 0;
    TRUE_RESULT[58] = 0; TRUE_RESULT[59] = 0; TRUE_RESULT[60] = 1;
    TRUE_RESULT[61] = 1; TRUE_RESULT[62] = 0; TRUE_RESULT[63] = 0;
    TRUE_RESULT[64] = 0; TRUE_RESULT[65] = 0; TRUE_RESULT[66] = 1;
    TRUE_RESULT[67] = 0; TRUE_RESULT[68] = 1; TRUE_RESULT[69] = 0;
    TRUE_RESULT[70] = 0; TRUE_RESULT[71] = 0; TRUE_RESULT[72] = 1;
    TRUE_RESULT[73] = 1; TRUE_RESULT[74] = 0; TRUE_RESULT[75] = 0;
    TRUE_RESULT[76] = 0; TRUE_RESULT[77] = 1; TRUE_RESULT[78] = 0;
    TRUE_RESULT[79] = 1; TRUE_RESULT[80] = 0; TRUE_RESULT[81] = 0;
    TRUE_RESULT[82] = 0; TRUE_RESULT[83] = 0; TRUE_RESULT[84] = 1;
    TRUE_RESULT[85] = 0; TRUE_RESULT[86] = 0; TRUE_RESULT[87] = 1;
    TRUE_RESULT[88] = 1; TRUE_RESULT[89] = 0; TRUE_RESULT[90] = 0;

function start_test() {
    // Сбрасываем статистику
    correctAnswers = 0;
    totalAnswered = 0;
    answers = new Array(TOTAL_QUESTIONS).fill(null); // Инициализируем массив ответов

    // Скрываем элементы
    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('Settings_button').style.display = 'none';
    document.getElementById('Finish_button').style.display = 'block';
    document.getElementById('bg').style.background = 'white';
    document.getElementById('container-voinarovsky').style.display = 'block';
    document.getElementById('container-voinarovsky').style.zIndex = '10';

    // Получаем настройки
    const testDuration = parseInt(document.getElementById('testDuration').value) * 60 * 1000;
    const showTimer = document.getElementById('showTimer').checked;
    const showProgress = document.getElementById('showProgress').checked;

    // Устанавливаем время окончания теста
    testEndTime = Date.now() + testDuration;

    // Настройка таймера
    const timerElement = document.getElementById('Timer');
    if (showTimer) {
        timerElement.style.display = 'block';
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);
    } else {
        timerElement.style.display = 'none';
    }

    // Настройка прогресса
    const progressElement = document.getElementById('Progress');
    if (showProgress) {
        progressElement.style.display = 'block';
        updateProgress();
    } else {
        progressElement.style.display = 'none';
    }

    // Инициализация слайдера
    const sliderEl = document.querySelector('.itc-slider');
    if (sliderEl) {
        new ItcSlider(sliderEl, {
            loop: false,
            autoplay: false,
            swipe: true
        });
    }

    // Отслеживание ответов
    const form = document.getElementById('test');
    form.addEventListener('change', (event) => {
        if (event.target.classList.contains('question_inp')) {
            const questionName = event.target.name; // Например, "q1", "q2"
            const questionNumber = parseInt(questionName.replace('q', '')) - 1;
            const selectedIndex = Array.from(form.elements[questionName]).indexOf(event.target);

            // Проверяем, был ли уже ответ на этот вопрос
            if (answers[questionNumber] !== null) {
                // Если ранее ответ был правильным, уменьшаем счетчик
                if (answers[questionNumber] === TRUE_RESULT[questionNumber * 3 + answers[questionNumber] + 1]) {
                    correctAnswers--;
                }
                totalAnswered--;
            }

            // Сохраняем новый ответ
            answers[questionNumber] = selectedIndex;
            totalAnswered++;

            // Проверяем правильность нового ответа
            if (selectedIndex === TRUE_RESULT[questionNumber * 3 + selectedIndex + 1]) {
                correctAnswers++;
            }

            // Обновляем прогресс
            if (showProgress) {
                updateProgress();
            }
        }
    });

    // Устанавливаем таймер для автоматического завершения теста
    setTimeout(finish_test, testDuration);
}

function updateTimer() {
    const timeLeft = testEndTime - Date.now();
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        document.getElementById('Timer').textContent = 'Время: 00:00';
        finish_test();
        return;
    }

    const minutes = Math.floor(timeLeft / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
    document.getElementById('Timer').textContent = 
        `Время: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

function updateProgress() {
    const progressElement = document.getElementById('Progress');
    progressElement.textContent = `Правильных: ${correctAnswers} из ${totalAnswered}`;
}

function finish_test() {
    clearInterval(timerInterval);

    const form = document.getElementById('test');
    let result = correctAnswers;

    // Вычисляем проценты
    const answeredPercentage = totalAnswered > 0 ? Math.round((correctAnswers / totalAnswered) * 100) : 0;
    const totalPercentage = Math.round((correctAnswers / TOTAL_QUESTIONS) * 100);

    // Формируем таблицу результатов
    let resultsHTML = `
        <div class="results-container">
            <h3>Результаты теста Войнаровского</h3>
            <div style="margin: 20px 0; font-size: 24px; color: black;">
                Правильных ответов: <strong>${correctAnswers} из ${totalAnswered}</strong> 
                (${answeredPercentage}% от отвеченных, ${totalPercentage}% от общего)
            </div>
            <table class="results-table">
                <tr>
                    <th style="color: black;">Вопрос</th>
                    <th style="color: black;">Статус</th>
                </tr>
    `;

    answers.forEach((answer, index) => {
        if (answer !== null) {
            const isCorrect = answer === TRUE_RESULT[index * 3 + answer + 1];
            resultsHTML += `
                <tr style="color: black;">
                    <td style="color: black;">Вопрос ${index + 1}</td>
                    <td style="color: black;">${isCorrect ? 'Правильно' : 'Неправильно'}</td>
                </tr>
            `;
        }
    });

    resultsHTML += `
            </table>
            <div style="margin-top: 30px;">
                <p style="font-size: 18px; color: black;">
                    <a href="https://testometrika.com/blog/the-correct-answers-to-the-logical-test-wojnarowski/" target="_blank">Подробное разъяснение всех задач</a>
                </p>
            </div>
        </div>
    `;

    document.getElementById('Timer').style.display = 'none';
    document.getElementById('Progress').style.display = 'none';
    document.getElementById('Final Result').style.display = 'block';
    document.getElementById('Final Result').innerHTML = resultsHTML;
    document.getElementById('Finish_button').style.display = 'none';
    document.getElementById('container-voinarovsky').style.display = 'none';
    document.getElementById('Retry').style.display = 'block';
    document.getElementById('Retry').style.top = '10vh';

    sendUser(result);
    window.scroll(0, 0);
}

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

function showSettings() {
    document.getElementById('Settings').style.display = 'block';
}

function hideSettings() {
    document.getElementById('Settings').style.display = 'none';
}