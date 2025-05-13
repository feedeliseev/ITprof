let start_time;
let iteration = 0;
let end_time;
let maxIteration = 5;
let isDuringTest = false;

let correctTests = 0;
const resultList = [];

document.addEventListener('keydown', function(event) {
    if (iteration <= maxIteration && isDuringTest) {
        let button;
        switch (event.code) {
            case "KeyF":
                event.preventDefault();
                button = document.getElementById('Even');
                button.classList.add('active');
                setTimeout(() => button.classList.remove('active'), 100);
                onAnswer(0);
                break;
            case "KeyJ":
                event.preventDefault();
                button = document.getElementById('Odd');
                button.classList.add('active');
                setTimeout(() => button.classList.remove('active'), 100);
                onAnswer(1);
                break;
        }
    }
});

document.addEventListener('keyup', function(event) {
    if (event.code === "KeyF") document.getElementById('Even').classList.remove('active');
    if (event.code === "KeyJ") document.getElementById('Odd').classList.remove('active');
});

function onAnswer(num) {
    finish_test(num);
    if (iteration !== maxIteration) {
        start_test();
    } else {
        iteration++;
    }
}

let num1, num2, sum;

function start_test() {
    iteration++;
    end_time = 0;
    start_time = NaN;
    isDuringTest = true;

    document.getElementById('Instruction').style.display = 'none';
    document.getElementById('Start_button').style.display = 'none';
    document.getElementById('Iteration').style.display = 'block';
    document.getElementById('Iteration').innerHTML = 'Попытка: ' + iteration + '/' + maxIteration;

    num1 = Math.floor(Math.random() * 100);
    num2 = Math.floor(Math.random() * 100);
    sum = (num1 + num2) % 2;

    document.getElementById('Calc').style.display = 'block';
    document.getElementById('Calc').innerHTML = num1 + ' + ' + num2;
    document.getElementById('Even').style.display = 'inline-block';
    document.getElementById('Odd').style.display = 'inline-block';

    start_time = Date.now();
}

function finish_test(answer) {
    end_time = Date.now() - start_time;
    document.getElementById('Result').style.display = 'block';
    document.getElementById('Even').style.display = 'none';
    document.getElementById('Odd').style.display = 'none';

    if (isNaN(end_time)) {
        if (iteration !== maxIteration) {
            document.getElementById('Result').innerHTML = 'Не спешите!';
        }
    } else if (sum === answer) {
        end_time = end_time / 1000;
        resultList.push(end_time);
        document.getElementById('Result').innerHTML = 'Ваше время реакции: ' + end_time.toFixed(3) + 's';
        correctTests++;
    } else {
        document.getElementById('Result').innerHTML = 'Вы выбрали неверный ответ';
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

        document.getElementById('Calc').style.display = 'none';
        document.getElementById('Calc').innerHTML = '';
        document.getElementById('Result').innerHTML = '';

        document.getElementById('Final Result').style.display = 'block';
        document.getElementById('Final Result').innerHTML =
            'Среднее время: ' + averageTime.toFixed(3) +
            '\nКоличество правильных ответов: ' + percentageOfCorrectAnswers.toFixed(2) + "%";
        document.getElementById('Retry').style.display = 'block';

        submitResult(
            percentageOfCorrectAnswers.toFixed(2),
            averageTime.toFixed(3),
            USER_ID,
            TEST_ID
        );
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