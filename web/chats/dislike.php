<?php
session_start();
require_once '../../web/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['target_id'])) {
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}

$usuario1 = $_SESSION['user_id'];
$usuario2 = $_POST['target_id'];

$stmt = $pdo->prepare("INSERT INTO dislikes (usuario1, usuario2) VALUES (?, ?)");
$stmt->execute([$usuario1, $usuario2]);

echo json_encode(["success" => true]);
?>