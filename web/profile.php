<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT username, age, profileImage, bio FROM users WHERE iduser = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Error: Usuario no encontrado.");
}

$profileImageSrc = !empty($user['profileImage']) ? 'data:image/jpeg;base64,' . $user['profileImage'] : 'default.jpg';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="../styles/profile.css">
    <link rel="stylesheet" href="../styles/styles.css">

</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img class="profile-pic" src="<?php echo $profileImageSrc; ?>" alt="Foto de perfil">
            <h1><?php echo htmlspecialchars($user['username']); ?>, <?php echo htmlspecialchars($user['age']); ?></h1>
            <p class="bio"><?php echo htmlspecialchars($user['bio'] ?? 'Aún no has agregado una biografía.'); ?></p>
        </div>
        <div class="profile-actions">
            <a href="editProfile.php" class="btn">Editar perfil</a>
            <a href="home.php" class="btn home-btn">Volver al Home</a>
            <a href="logout.php" class="btn logout-btn">Tanca Sessió</a>

        </div>
    </div>
</body>
</html>
