<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Obtener el género y la preferencia de atracción del usuario
$stmt = $pdo->prepare("SELECT gender, attracted_to FROM users WHERE iduser = ?");
$stmt->execute([$_SESSION['user_id']]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$userData) {
    die("Error: Usuario no encontrado.");
}

$gender = $userData['gender'];
$attracted_to = $userData['attracted_to'];

$sql = "SELECT iduser, username, age, bio, profileImage FROM users WHERE iduser != ?
        AND iduser NOT IN (
            SELECT usuario2 FROM likes WHERE usuario1 = ?
        )
        AND iduser NOT IN (
            SELECT usuario1 FROM likes WHERE usuario2 = ?
        )
        AND iduser NOT IN (
            SELECT usuario2 FROM dislikes WHERE usuario1 = ?
        )";

if ($attracted_to === 'M') {
    $sql .= " AND gender = 'M' AND attracted_to IN ('F', 'B')";
}
elseif ($attracted_to === 'F') {
    $sql .= " AND gender = 'F' AND attracted_to IN ('M', 'B')";
}
elseif ($attracted_to === 'B') {
    $sql .= " AND (
        (gender = 'M' AND attracted_to IN ('F', 'B')) 
        OR 
        (gender = 'F' AND attracted_to IN ('M', 'B'))
    )";
}

$sql .= " ORDER BY RAND() LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id'],$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$profileImageSrc = !empty($user['profileImage']) ? 'data:image/jpeg;base64,' . $user['profileImage'] : 'default.jpg';

$fotos = [];
$fotos[] = $profileImageSrc;

$stmtFotos = $pdo->prepare("SELECT imagen FROM publicaciones WHERE usuario_id = ?");
if ($user) {
    $stmtFotos->execute([$user['iduser']]);
}
while ($row = $stmtFotos->fetch(PDO::FETCH_ASSOC)) {
    $fotos[] = 'data:image/jpeg;base64,' . $row['imagen'];
}

if (!$user) {
    $noUsersMessage = "<h2 class='no-users-message'>No hay usuarios disponibles en este momento.</h2>";
} else {
    $noUsersMessage = "";
}

?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TUNO</title>
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="shortcut icon" href="../img/icono.ico" type="image/x-icon">
</head>
<body>
    <div class="top-logo fixed-top">
        <img src="../img/Logo.png" alt="Tuno Logo">
    </div>

    <div class="home-container">
        <?= $noUsersMessage ?>
        
        <?php if ($user): ?>
        <div class="profile-card" data-user-id="<?= htmlspecialchars($user['iduser']) ?>">
            <div class="image-container">
                <button class="prev-btn" onclick="changeImage(-1)">&#10094;</button>
                <img id="profile-image" class="profile-pic" src="<?= $fotos[0]; ?>" alt="Foto del usuario">
                <button class="next-btn" onclick="changeImage(1)">&#10095;</button>
            </div>

            <h2><?= htmlspecialchars($user['username']) ?>, <?= htmlspecialchars($user['age']) ?></h2>
            <p><?= htmlspecialchars($user['bio']) ?></p>

            <div class="action-buttons">
                <button class="dislike-btn" onclick="darDislike(<?= $user['iduser'] ?>)">❌</button>

                <button class="like-btn" onclick="darLike(<?= $user['iduser'] ?>)">💚</button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="bottom-menu">
        <div class="menu-item">
            <a href="chats/chat.php">
                <img src="../img/chat.png" alt="Missatges">
            </a>
        </div>
        <div class="menu-item">
            <a href="mis_fotos.php">
            <img src="../img/camara.webp" alt="Mis Fotos">
            </a>
        </div>

        <div class="menu-item">
            <a href="profile.php">
                <img src="../img/user.png" alt="Perfil">
            </a>
        </div>
    </div>  
</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script>
<script src="../js/home.js"></script>
<script src="../js/chat.js"></script>

<script>
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
let images = <?= json_encode($fotos); ?>;
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
</script>
