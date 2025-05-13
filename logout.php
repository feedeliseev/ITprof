<?php
session_start();
session_destroy();
header("Location: login.php?message=" . urlencode("Вы вышли из системы"));
exit();
?>