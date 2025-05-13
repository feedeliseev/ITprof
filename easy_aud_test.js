let start_time;
let iteration = 0;
let maxIteration = 5;
let end_time;
let isDuringTest = false;

let correctTests = 0;
const resultList = [];

const audio = new Audio("audio/audiosignal.mp3");

document.addEventListener('keydown', function(event) {
    if (event.code === "Space" && iteration <= maxIteration && isDuringTest) {
        onAnswer();
    }
});

function onAnswer() {
    finish_test();
    if (iteration !== maxIteration) {
        start_test();
    } else {
        iteration++;
    }
}

function start_test() {
    iteration++;
    end_time = 0;
    start_time = NaN;
    isDuringTest = true;
    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('bg').style.background = 'white';
    document.getElementById('Iteration').style.display = 'block';
    document.getElementById('Iteration').innerHTML = 'Попытка: ' + iteration + '/' + maxIteration;
    let rand_time = (Math.random() * 5000) + 1000;
    document.getElementById('Circle').style.display = 'block';
    setTimeout(function () {
        if (isDuringTest) {
            audio.play();
            start_time = Date.now();
        }
    }, rand_time);
}

function finish_test() {
    isDuringTest = false;
    end_time = Date.now() - start_time;
    document.getElementById('Result').style.display = 'block';
    document.getElementById('Circle').style.display = 'none';

    if (isNaN(end_time)) {
        document.getElementById('Result').innerHTML = 'Не спешите!';
    } else {
        end_time = end_time / 1000;
        resultList.push(end_time);
        document.getElementById('Result').innerHTML = 'Ваше время реакции: ' + end_time.toFixed(3) + 's';
        correctTests++;
    }

    if (iteration === maxIteration) {
        let averageTime = 0;
        for (let i = 0; i < resultList.length; i++) {
            if (!isNaN(resultList[i])) {
                averageTime += resultList[i];
            }
        }
        averageTime /= correctTests;
        let percentageOfCorrectAnswers = correctTests / maxIteration * 100;

        document.getElementById('Final Result').style.display = 'block';
        document.getElementById('Final Result').innerHTML =
            'Среднее время: ' + averageTime.toFixed(3) +
            '<br>Правильных: ' + percentageOfCorrectAnswers.toFixed(2) + '%';
        document.getElementById('Retry').style.display = 'block';

        submitResult(percentageOfCorrectAnswers.toFixed(2), averageTime.toFixed(3), USER_ID, TEST_ID);
    }
}

function submitResult(scorePercent, mid, userId, testId) {
    fetch('submit_result.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            user_id: userId,
            test_id: testId,
            score_percent: scorePercent,
            mid: mid
        })
    })
        .then(response => response.text())
        .then(alert)
        .catch(err => console.error('Ошибка отправки:', err));
}