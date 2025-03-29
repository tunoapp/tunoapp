<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener los datos actuales del usuario
$stmt = $pdo->prepare("SELECT bio, location, age, profileImage, bannerImage FROM users WHERE iduser = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'] ?? '';
    $location = $_POST['location'] ?? '';
    $age = $_POST['age'] ?? null;

    // Procesar la imagen de perfil en Base64
    if (!empty($_FILES['profileImage']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['profileImage']['tmp_name']);
        $profileImage = base64_encode($imageData);
    } else {
        $profileImage = $user['profileImage'];
    }

    // Procesar la imagen del banner en Base64
    if (!empty($_FILES['bannerImage']['tmp_name'])) {
        $bannerData = file_get_contents($_FILES['bannerImage']['tmp_name']);
        $bannerImage = base64_encode($bannerData);
    } else {
        $bannerImage = $user['bannerImage'];
    }

    // Actualizar los datos en la base de datos
    $updateStmt = $pdo->prepare("UPDATE users SET bio = ?, location = ?, age = ?, profileImage = ?, bannerImage = ? WHERE iduser = ?");
    $updateStmt->execute([$bio, $location, $age, $profileImage, $bannerImage, $user_id]);

    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../styles/editProfile.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="profile-container">
        <h1>Editar Perfil</h1>
        <form action="editProfile.php" method="post" enctype="multipart/form-data">
            <label for="bio">Biografía:</label>
            <input type="text" name="bio" id="bio" value="<?php echo htmlspecialchars($user['bio'] ?? ''); ?>">

            <label for="location">Ubicación:</label>
            <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">

            <label for="age">Edad:</label>
            <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($user['age'] ?? ''); ?>">

            <label for="profileImage">Foto de Perfil:</label>
            <input type="file" name="profileImage" id="profileImage" accept="image/*">

            <label for="bannerImage">Banner del Perfil:</label>
            <input type="file" name="bannerImage" id="bannerImage" accept="image/*">

            <button type="submit" class="btn">Guardar Cambios</button>
        </form>
        <a href="profile.php" class="btn">Cancelar</a>
    </div>
</body>
</html>
