<?php
session_start();
session_destroy();
header("Location: index.php?message=" . urlencode("Вы вышли из системы"));
exit();
?>