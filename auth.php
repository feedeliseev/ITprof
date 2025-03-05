<?php
// Подключение к БД
$host = "localhost";
$dbname = "feedelisee";
$username = "root";
$password = "";

try {
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
//Подключение к БД спомощью PHP Data Objects
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//setAttribute для легкого нахождения ошибок
} catch (PDOException $e) {
//exception для отображения ошибок
die("Ошибка подключения: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles/authstyle.css">
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Регистрация на Портал </h2>
        <a href="index.php"><img class="logo" src="styles/images/itmologo.png"></a>
    </div>

    <!-- Вывод сообщений (ошибок или успеха) -->
    <?php if (isset($_GET['message'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_GET['message']); ?></p>
    <?php endif; ?>


    <form action="register.php" method="POST">

        <input type="text" name="name" placeholder="Имя" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <select name="role">
                <option value="user">Пользователь</option>
                <option value="admin">Администратор</option>
                <option value="expert">Эксперт</option>
                <option value="consultant">Консультант</option>
            </select>
            <button type="submit">Зарегистрироваться</button>
    </form>

    <form action="login.php" method="GET">
        <button type="submit">Войти</button>
    </form>


</div>
</body>
</html>