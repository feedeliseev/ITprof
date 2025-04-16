<?php
$pdo = new PDO('mysql:host=localhost;dbname=feedelisee;charset=utf8mb4', 'root', '');
$user = intval($_GET['user']);
$profid = intval($_GET['profid']);
$stmt = $pdo->prepare("SELECT pvk.name FROM pvk JOIN pvk_prof ON pvk.id = pvk_prof.pvkid WHERE pvk_prof.userid = ? AND pvk_prof.profid = ?");
$stmt->execute([$user, $profid]);
echo implode(', ', $stmt->fetchAll(PDO::FETCH_COLUMN));