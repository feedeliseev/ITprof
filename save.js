function saveResultToDatabase(userId, averageTime, correctAnswers, testName = 'hard_moving_test') {
    fetch('save_result.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: userId,
            average_time: averageTime,
            correct_answers: correctAnswers,
            test_name: testName
        })
    })
        .then(response => response.text())
        .then(data => {
            console.log('Сервер ответил:', data);
        })
        .catch(error => {
            console.error('Ошибка при отправке данных:', error);
        });
}