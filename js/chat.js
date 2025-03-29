document.addEventListener("DOMContentLoaded", function () {
    const chatList = document.getElementById("chatList");
    const searchUser = document.getElementById("searchUser");
    const userSuggestions = document.getElementById("userSuggestions");
    const messagesDiv = document.getElementById("messages");
    const messageInput = document.getElementById("messageInput");
    const sendMessage = document.getElementById("sendMessage");
    const menuButton = document.getElementById("menuButton");
    const sidebar = document.getElementById("sidebar");
    const chatTitle = document.getElementById("chatTitle");

    let currentChat = null;

    function darLike(targetId) {
        fetch('../web/chats/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'target_id=' + targetId
        })
        .then(response => response.json())
        .then(data => {
            if (data.match) {
                alert("¡Es un match! Se ha abierto el chat.");
            } else {
                alert("Like registrado. Esperando match.");
            }
            loadNewProfile();
        })
        .catch(error => console.error('Error:', error));
    }

    /** 🔹 Cargar lista de chats */
    function loadChats() {
        fetch("obtener_chats.php")
            .then(res => res.json())
            .then(data => {
                chatList.innerHTML = data.length > 0 
                    ? data.map(chat => `<div class='chat-item' data-id='${chat.iduser}'>${chat.username}</div>`).join('') 
                    : "<p style='color: white; text-align: center;'>No hay chats disponibles</p>";
            })
            .catch(err => console.error("Error al cargar chats:", err));
    }

    /** 🔹 Cargar mensajes de un chat específico */
    function loadMessages(userId) {
        currentChat = userId;
        fetch(`obtener_mensajes.php?receptor=${userId}`)
            .then(res => res.json())
            .then(data => {
                messagesDiv.innerHTML = data.length > 0 
                    ? data.map(msg => `
                        <div class='message ${msg.position}'>
                            <strong>${msg.position === 'sent' ? 'Tú' : msg.username}:</strong> ${msg.mensaje}
                        </div>
                    `).join('') 
                    : "<p style='color: white; text-align: center;'>No hay mensajes</p>";
            })
            .catch(err => console.error("Error al cargar mensajes:", err));
    }

    /** 🔹 Enviar mensaje */
    function sendMessageToChat() {
        if (!currentChat || !messageInput.value.trim()) return;

        fetch("enviar_mensaje.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ receptor: currentChat, mensaje: messageInput.value })
        })
        .then(() => {
            messageInput.value = "";
            loadMessages(currentChat);
        })
        .catch(err => console.error("Error al enviar mensaje:", err));
    }

    /** 🔹 Buscar usuarios */
    searchUser.addEventListener("input", function () {
        fetch(`buscar_usuarios.php?q=${searchUser.value}`)
            .then(res => res.json())
            .then(data => {
                userSuggestions.innerHTML = data.length > 0 
                    ? data.map(user => `<div class='user-suggestion' data-id='${user.iduser}'>${user.username}</div>`).join('') 
                    : "<p style='color: white; text-align: center;'>No se encontraron usuarios</p>";
            })
            .catch(err => console.error("Error en la búsqueda de usuarios:", err));
    });
    

    
    /** 🔹 Evento de clic en chats */
    chatList.addEventListener("click", function (e) {
        if (e.target.classList.contains("chat-item")) {
            chatTitle.textContent = e.target.textContent; // Actualiza el título del chat
            loadMessages(e.target.dataset.id);
            sidebar.classList.remove("active"); // Cierra el menú en móvil
        }
    });

    /** 🔹 Evento de clic en sugerencias de usuario */
    userSuggestions.addEventListener("click", function (e) {
        if (e.target.classList.contains("user-suggestion")) {
            loadMessages(e.target.dataset.id);
        }
    });

    /** 🔹 Evento de clic en el botón de enviar */
    sendMessage.addEventListener("click", sendMessageToChat);

    /** 🔹 Enviar mensaje con Enter */
    messageInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") sendMessageToChat();
    });

    /** 🔹 Abrir y cerrar menú en móvil */
    menuButton.addEventListener("click", function () {
        sidebar.classList.toggle("active");
    });

    /** 🔹 Cargar los chats al iniciar */
    loadChats();
});
