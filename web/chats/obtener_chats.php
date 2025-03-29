<?php
require 'db.php';
session_start();
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT users.iduser, users.username 
                       FROM matches 
                       JOIN users ON (users.iduser = matches.usuario1 OR users.iduser = matches.usuario2) 
                       WHERE (matches.usuario1 = ? OR matches.usuario2 = ?) 
                       AND users.iduser != ?");
$stmt->execute([$user_id, $user_id, $user_id]);

$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($chats);
?>
