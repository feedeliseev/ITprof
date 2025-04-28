<?php
// Настройки подключения к базе данных
$host = 'localhost';
$db   = 'your_database';
$user = 'your_user';
$pass = 'your_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получаем данные из POST-запроса
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
$test_name = isset($_POST['test_name']) ? $_POST['test_name'] : null;
$average_time = isset($_POST['average_time']) ? $_POST['average_time'] : null;
$correct_answers = isset($_POST['correct_answers']) ? $_POST['correct_answers'] : null;

if (!$user_id || !$test_name || !$average_time || !$correct_answers) {
    die("Ошибка: недостаточно данных для записи результата.");
}

try {
    // Проверка, что пользователь существует
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    if (!$stmt->fetch()) {
        die("Пользователь с ID $user_id не найден.");
    }

    // 1. Вставляем результат теста (test_date создается автоматически)
    $sql_insert = "INSERT INTO test_results (user_id, test_name, average_time, correct_answers) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql_insert);
    $stmt->execute([$user_id, $test_name, $average_time, $correct_answers]);

    // 2. Обновляем агрегированные данные в dashboard
    $sql_agg = "
        SELECT 
            ROUND(AVG(average_time), 2) AS avg_time,
            SUM(correct_answers) AS total_correct
        FROM test_results
        WHERE user_id = ?
    ";
    $stmt = $pdo->prepare($sql_agg);
    $stmt->execute([$user_id]);
    $agg = $stmt->fetch();

    // Проверяем, есть ли уже запись в dashboard
    $stmt = $pdo->prepare("SELECT user_id FROM dashboard WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        // Обновляем
        $sql_update = "UPDATE dashboard SET avg_time = ?, total_correct = ? WHERE user_id = ?";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([$agg['avg_time'], $agg['total_correct'], $user_id]);
    } else {
        // Вставляем новую запись
        $sql_insert_dashboard = "INSERT INTO dashboard (user_id, avg_time, total_correct) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql_insert_dashboard);
        $stmt->execute([$user_id, $agg['avg_time'], $agg['total_correct']]);
    }

    echo "Результаты успешно сохранены и обновлены в dashboard.";

} catch (Exception $e) {
    die("Ошибка при работе с базой данных: " . $e->getMessage());
}
?>