<?php
require 'db.php';
session_start();
$user_id = $_SESSION['user_id'];
$receptor = $_POST['receptor'];
$mensaje = $_POST['mensaje'];
$stmt = $pdo->prepare("INSERT INTO mensajes (emisor, receptor, mensaje, fecha) VALUES (?, ?, ?, NOW())");
$stmt->execute([$user_id, $receptor, $mensaje]);
echo json_encode(["success" => true]);
?>