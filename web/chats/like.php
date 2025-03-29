<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['target_id'])) {
    echo json_encode(["error" => "Datos insuficientes"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$target_id = $_POST['target_id'];

$menor = min($user_id, $target_id);
$mayor = max($user_id, $target_id);

$stmt = $pdo->prepare("SELECT * FROM likes WHERE usuario1 = ? AND usuario2 = ?");
$stmt->execute([$target_id, $user_id]);
$likeExists = $stmt->fetch();

if ($likeExists) {
    $stmt = $pdo->prepare("INSERT INTO matches (usuario1, usuario2, match_key, fecha) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$menor, $mayor, "$menor-$mayor"]);

    echo json_encode(["match" => true, "message" => "¡Es un match! Se ha abierto el chat."]);
} else {
    $stmt = $pdo->prepare("INSERT INTO likes (usuario1, usuario2, fecha, menor, mayor) VALUES (?, ?, NOW(), ?, ?)");
    $stmt->execute([$user_id, $target_id, $menor, $mayor]);

    echo json_encode(["match" => false, "message" => "Like registrado. Esperando match."]);
}
?>
