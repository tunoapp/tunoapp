<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code']) && isset($_GET['mail'])) {
    $code = $_GET['code'];
    $mail = $_GET['mail'];

    try {
        $stmt = $pdo->prepare("SELECT iduser FROM users WHERE mail = ? AND resetPassCode = ? AND resetPassExpiry > NOW()");
        $stmt->execute([$mail, $code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Restablir Contrasenya</title>
                <link rel="stylesheet" href="../styles/resetPass.css">
                <link rel="stylesheet" href="../styles/styles.css">

            </head>
            <body>
                <div class="reset-container">
                    <form method="POST" action="resetPassword.php">
                        <input type="hidden" name="code" value="<?php echo htmlspecialchars($code); ?>">
                        <input type="hidden" name="mail" value="<?php echo htmlspecialchars($mail); ?>">

                        <label for="new_password">Nova Contrasenya:</label>
                        <input type="password" id="new_password" name="new_password" required>

                        <label for="confirm_password">Confirmar Contrasenya:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>

                        <button type="submit">Restablir</button>
                    </form>
                </div>
            </body>
            </html>
            <?php
        } else {
            die("El codi és invàlid o ha expirat.");
        }
    } catch (PDOException $e) {
        die("Error de connexió: " . $e->getMessage());
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $mail = $_POST['mail'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirmPassword) {
        die("Les contrasenyes no coincideixen.");
    }

    try {
        $stmt = $pdo->prepare("SELECT iduser FROM users WHERE mail = ? AND resetPassCode = ? AND resetPassExpiry > NOW()");
        $stmt->execute([$mail, $code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET passHash = ?, resetPassCode = NULL, resetPassExpiry = NULL WHERE iduser = ?");
            $stmt->execute([$hashedPassword, $user['iduser']]);

            echo "<script>alert('Contrasenya canviada amb èxit!'); window.location.href='../index.php';</script>";
        } else {
            die("Error: codi de reset invàlid o expirat.");
        }
    } catch (PDOException $e) {
        die("Error de connexió: " . $e->getMessage());
    }
} else {
    die("Petició invàlida.");
}
?>
