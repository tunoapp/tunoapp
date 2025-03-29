<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $foto = $_FILES['foto'];
    
    if ($foto['error'] === 0) {
        $imagenData = file_get_contents($foto['tmp_name']);
        $imagenBase64 = base64_encode($imagenData);

        $stmt = $pdo->prepare("INSERT INTO publicaciones (usuario_id, imagen, fecha) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $imagenBase64]);

        header("Location: mis_fotos.php");
        exit;
    }
}

if (isset($_GET['eliminar'])) {
    $id_foto = $_GET['eliminar'];
    
    $stmt = $pdo->prepare("DELETE FROM publicaciones WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id_foto, $user_id]);

    header("Location: mis_fotos.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, imagen FROM publicaciones WHERE usuario_id = ?");
$stmt->execute([$user_id]);
$fotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <br/>   
    <title>Mis Fotos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/mis_fotos.css">
</head>
<body>
    <h1>Mis Fotos</h1>

    <form action="mis_fotos.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="foto" accept="image/*" required>
        <button type="submit">Subir Foto</button>
    </form>

    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php foreach ($fotos as $foto): ?>
                <div class="swiper-slide">
                    <img src="data:image/jpeg;base64,<?= htmlspecialchars($foto['imagen']) ?>" alt="Foto subida">
                    <a href="mis_fotos.php?eliminar=<?= $foto['id'] ?>" class="eliminar-btn" onclick="return confirm('¿Eliminar esta foto?')">❌</a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        
        <div class="swiper-pagination"></div>
    </div>

    <a href="home.php" class = "back-button" >Volver al inicio</a>

    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper('.swiper-container', {
            loop: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>
</body>
</html>
