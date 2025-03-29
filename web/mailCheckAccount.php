<?php
require 'db.php';

if (!isset($_GET['code']) || !isset($_GET['mail'])) {
    echo "<script>alert('Falten paràmetres d\'activació!'); window.location.href='../index.php';</script>";
    exit;
}

$activationCode = $_GET['code'];
$mail = $_GET['mail'];

try {
    $stmt = $pdo->prepare("SELECT iduser FROM users WHERE mail = ? AND activationCode = ? AND active = 0");
    $stmt->execute([$mail, $activationCode]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET active = 1, activationDate = NOW(), activationCode = NULL WHERE iduser = ?");
        $stmt->execute([$user['iduser']]);

        echo "<script>alert('El teu compte ha estat activat correctament!'); window.location.href='../index.php';</script>";

    } else {
        echo "<script>alert('Codi d\'activació invàlid o compte ja activat!'); window.location.href='../index.php';</script>";
    }
} catch (PDOException $e) {
    error_log("Error d'activació: " . $e->getMessage());
    echo "<script>alert('Error en activar el compte. Contacta amb el suport.'); window.location.href='../index.php';</script>";
}
?>
