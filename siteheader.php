<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ОПД</title>
    <link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<header style="position: fixed; top: 0; left: 0; width: 100%; z-index: 1000;">
    <div class="menu">
        <a class="logo" href="index.php">
            <div>
                <h1 class="text-color">Профессии в IT</h1>
            </div>
        </a>

        <a class="menubutton" href="ratings.php">
            <div>
                <p>Список профессий</p>
            </div>
        </a>
        <a class="menubutton" href="temporary.php">
            <div>
                <p>Что-то</p>
            </div>
        </a>
        <a class="menubutton" href="temporary.php">
            <div>
                <p>Что-то</p>
            </div>
        </a>
        <a class="menubutton" href="lab3-4 js/test_lab2/tests_lab3.html">
            <div>
                <p>Тесты</p>
            </div>
        </a>

        <div style="width: 20%; height: 100%; display: flex; justify-content: center; align-items: center;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Если пользователь авторизован, показываем "Личный кабинет" -->
                <a class="auth" href="dashboard.php">
                    <div>
                        <p>Личный кабинет</p>
                    </div>
                </a>
            <?php else: ?>
                <!-- Если пользователь НЕ авторизован, показываем "Авторизация" -->
                <a class="auth" href="auth.php">
                    <div>
                        <p>Авторизация</p>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
    
</header>
</body>
</html>