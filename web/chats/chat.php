<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="shortcut icon" href="../../img/icono.ico" type="image/x-icon">
    <link rel="stylesheet" href="chat.css">
    <link rel="stylesheet" href="../../styles/styles.css">
</head>
<body>
<div class="chat-container">
        <!-- Sidebar con menú hamburguesa -->
        <div class="sidebar" id="sidebar">
            <button id="menuButton" class="hamburger">&#9776;</button>
            <button id="homeButton" onclick="window.location.href='../home.php'">Volver al Home</button>

            <input type="text" id="searchUser" placeholder="Buscar usuarios...">
            <div id="userSuggestions"></div>
            <h3>Chats</h3>
            <div id="chatList"></div>
        </div>

        <div class="chat-box">
            <div class="chat-header">
                <span id="chatTitle">Selecciona un chat</span>
            </div>
            <div id="messages" class="messages"></div>
            <input type="text" id="messageInput" placeholder="Escribe un mensaje..." onkeypress="handleKeyPress(event)">
            <button id="sendMessage">Enviar</button>
        </div>
    </div>
    <script src="../../js/chat.js"></script>
</body>
</html>
