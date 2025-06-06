let start_time;
let iteration = 0;
let maxIteration = 5;
let end_time;
let isDuringTest = false;

let correctTests = 0;
const resultList = [];

document.addEventListener('keydown', function(event) {
    if (iteration <= maxIteration && isDuringTest) {
        switch (event.code) {
            case "Digit1":
                onAnswer('Circle1');
                break;
            case "Digit2":
                onAnswer('Circle2');
                break;
            case "Digit3":
                onAnswer('Circle3');
                break;
            case "Digit4":
                onAnswer('Circle4');
                break;
        }
    }
});

function onAnswer(circle) {
    finish_test(circle);
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
    document.getElementById('Iteration').style.display = 'block';
    document.getElementById('Iteration').innerHTML = 'Попытка: ' + iteration + '/' + maxIteration;

    // Расставляем круги
    document.getElementById('Circle1').style.left = '15vw';
    document.getElementById('Circle2').style.left = '25vw';
    document.getElementById('Circle3').style.left = '35vw';
    document.getElementById('Circle4').style.left = '45vw';

    // Показываем круги
    for (let i = 1; i <= 4; i++) {
        const circle = document.getElementById('Circle' + i);
        circle.style.backgroundColor = 'midnightblue';
        circle.style.display = 'inline-block';
    }

    // Выбираем случайный круг
    let randCircle = 'Circle' + (Math.floor(Math.random() * 4) + 1);

    setTimeout(function () {
        if (isDuringTest) {
            document.getElementById(randCircle).style.backgroundColor = 'red';
            start_time = Date.now();
        }
    }, (Math.random() * 5000) + 1000);
}

function finish_test(circle) {
    end_time = Date.now() - start_time;
    document.getElementById('Result').style.display = 'block';

    if (isNaN(end_time)) {
        document.getElementById('Result').innerHTML = 'Не спешите!';
    } else if (document.getElementById(circle).style.backgroundColor === 'red') {
        end_time = end_time / 1000;
        resultList.push(end_time);
        document.getElementById('Result').innerHTML = 'Ваше время реакции: ' + end_time.toFixed(3) + 's';
        correctTests++;
    } else {
        document.getElementById('Result').innerHTML = 'Вы выбрали неверный ответ';
    }

    // Скрываем и сбрасываем все круги
    for (let i = 1; i <= 4; i++) {
        const c = document.getElementById('Circle' + i);
        c.style.backgroundColor = 'midnightblue';
        c.style.display = 'none';
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
            '\nКоличество правильных ответов: ' + percentageOfCorrectAnswers.toFixed(2) + "%";
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