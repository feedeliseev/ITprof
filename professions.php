<?php session_start(); ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ОПД</title>
    <link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<header><div id="header-container"></div></header>

<main style="margin-top: 50px; " >
    <div style="display: flex; justify-content: center; align-items: center; height: 300px;" class="backimg"><h1 id="typing"></h1></div>

    <div class="profsfeed">
        <a href="cybersecurity.html" class="hr">
            <h2 class="profname2">Инженер по кибербезопасности</h2>
            <p class="profdescr2">Специалист, обеспечивающий защиту информационных систем и данных от несанкционированного доступа, кибератак
                и других угроз. Основные задачи включают анализ рисков, настройку средств защиты,
                мониторинг активности сети, выявление и ликвидацию уязвимостей, а также разработку стратегий безопасности.
                Профессия требует глубоких знаний в области сетевых технологий, операционных систем,
                методов взлома и противодействия им, а также навыков работы с современными решениями для защиты информации.</p>
        </a>
    </div>

    <div class="profsfeed">
        <a href="dataanalyst.html" class="hr">
            <h2 class="profname2">Аналитик данных</h2>
        <p class="profdescr2">Профессионал, который помогает компаниям принимать обоснованные решения на основе анализа данных.
            Он работает с различными источниками данных, очищает и структурирует их, а затем анализирует,
            чтобы выявить тенденции, закономерности и проблемы.</p>
        </a>
    </div>

    <div class="profsfeed">
        <a href="sysAdmin.html" class="hr">
            <h2 class="profname2">Системный администратор</h2>
            <p class="profdescr2">Работник, должностные обязанности которого подразумевают
                обеспечение штатной работы парка компьютерной техники, сети и программного обеспечения.
                Зачастую системному администратору вменяется обеспечение информационной безопасности в
                организации.</p>
        </a>
    </div>

    <div class="profsfeed">
        <a href="webDeveloperPage.html" class="hr">
            <h2 class="profname2">Web-разработчик</h2>
            <p class="profdescr2">Специалист, который разрабатывает, тестирует, исправляет, обновляет,
                совершенствует сайты, веб-сервисы и мультимедийные приложения с помощью языков программирования.</p>
        </a>
    </div>

    <div class="profsfeed">
        <a href="gameDeveloper.html" class="hr">
            <h2 class="profname2">Разработчик игр</h2>
            <p class="profdescr2">Программист, который с помощью движков создаёт новые шутеры, квесты, стратегии, приключения и т.д.
                Он работает над виртуальным миром в виде компьютерных игр: продумывает концепцию и дизайн, создаёт персонажей,
                перемещающихся и взаимодействующих друг с другом.</p>
        </a>
    </div>

    <div class="profsfeed">
        <a href="devops.html" class="hr">
            <h2 class="profname2">DevOps</h2>
            <p class="profdescr2">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </a>
    </div>

</main>




<footer>
    <div style="width: 100%; height: 200px; background-color: #F1F3F4; display: flex; justify-content: center; /* Центрирует по горизонтали */align-items: center;" >
        <h3>Один гандон решил избить Лупу. Лупа пожаловался старшему брату Пупе и тот пошел на драку вместо него. В итоге Пупа получил за Лупу.</h3>
    </div>
</footer>
<script>
  fetch("siteheader.php")
          .then(response => response.text())
          .then(data => document.getElementById("header-container").innerHTML = data);
</script>
<script>
    const text = "Выбери подходящую профессию";
    let index = 0;

    function typeEffect() {
        if (index < text.length) {
            document.getElementById("typing").innerHTML += text[index];
            index++;
            setTimeout(typeEffect, 100); // Скорость печати (мс)
        }
    }

    typeEffect();
</script>
</body>
</html>