<?php
$host = "localhost:3335";
$dbname = "tunobd";
$username = "scoba";
$password = "1234";
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
