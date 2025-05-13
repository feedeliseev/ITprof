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
    if (event.code === "KeyF") {
        document.getElementById('Even').classList.remove('active');
    }
    if (event.code === "KeyJ") {
        document.getElementById('Odd').classList.remove('active');
    }
});

function onAnswer(num) {
    finish_test(num);
    if (iteration !== maxIteration) {
        start_test();
    } else {
        iteration++;
    }
}

let num1;
let num2;
let sum;
let chosenAudio1;
let chosenAudio2;
let audiosMap = new Map();
for (let i = 1; i <= 9; i++) {
    audiosMap.set(i, new Audio("audio/" + i + ".mp3"));
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

    num1 = Math.floor(Math.random() * 9) + 1;
    num2 = Math.floor(Math.random() * 9) + 1;
    sum = (num1 + num2) % 2;

    chosenAudio1 = audiosMap.get(num1);
    chosenAudio2 = audiosMap.get(num2);
    const audio = new Audio('audio/plus.mp3');


    document.getElementById('Even').style.display = 'inline-block';
    document.getElementById('Odd').style.display = 'inline-block';

    if (isDuringTest) {
        chosenAudio1.play();

    }

    setTimeout(function () {
        if (isDuringTest) {
            audio.play();
            start_time = Date.now();
        }
    }, 300);

    setTimeout(function () {
        if (isDuringTest) {
            chosenAudio2.play();
            start_time = Date.now();
        }
    }, 1500);
}

function finish_test(answer) {
    let end_time = Date.now() - start_time;
    document.getElementById('Result').style.display = 'block';
    document.getElementById('Even').style.display = 'none';
    document.getElementById('Odd').style.display = 'none';

    if (isNaN(end_time)) {
        document.getElementById('Result').innerHTML = 'Не спешите!';
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