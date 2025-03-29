document.addEventListener("DOMContentLoaded", function () {
    const profileCard = document.querySelector(".profile-card");

    if (profileCard) {
        const hammer = new Hammer(profileCard);

        hammer.on("swipeleft", function () {
            profileCard.classList.add("swipe-left");
            setTimeout(loadNewProfile, 500);
        });

        hammer.on("swiperight", function () {
            profileCard.classList.add("swipe-right");
            setTimeout(() => {
                darLike(); // Llamar a la función de dar like
                loadNewProfile();
            }, 500);
        });
    }
});

function loadNewProfile() {
    location.reload();
}

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
function darDislike(targetId) {
    fetch('../web/chats/dislike.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'target_id=' + targetId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Usuario rechazado.");
        } else {
            alert("Error al registrar el rechazo.");
        }
        loadNewProfile();
    })
    .catch(error => console.error('Error:', error));
}
let images = json_encode($fotos);
let currentIndex = 0;

function changeImage(direction) {
    currentIndex += direction;
    if (currentIndex < 0) {
        currentIndex = images.length - 1;
    } else if (currentIndex >= images.length) {
        currentIndex = 0;
    }
    document.getElementById("profile-image").src = images[currentIndex];
}