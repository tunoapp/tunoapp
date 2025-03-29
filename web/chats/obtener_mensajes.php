<?php
require 'db.php';
session_start();
$user_id = $_SESSION['user_id'];
$receptor = $_GET['receptor'];
$stmt = $pdo->prepare("SELECT mensajes.*, users.username 
                       FROM mensajes 
                       JOIN users ON mensajes.emisor = users.iduser
                       WHERE (emisor = ? AND receptor = ?) OR (emisor = ? AND receptor = ?) 
                       ORDER BY fecha ASC");
$stmt->execute([$user_id, $receptor, $receptor, $user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as &$msg) {
    $msg['position'] = ($msg['emisor'] == $user_id) ? 'sent' : 'received';
}

echo json_encode($messages);

?>
