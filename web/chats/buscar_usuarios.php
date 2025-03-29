<?php
require 'db.php';
$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT iduser, username FROM users WHERE username LIKE ? LIMIT 5");
$stmt->execute(["%$q%"]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>