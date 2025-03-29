<?php
require 'db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username_email'])) {
    $userInput = $_POST['username_email'];

    try {
        $stmt = $pdo->prepare("SELECT iduser, mail FROM users WHERE username = ? OR mail = ?");
        $stmt->execute([$userInput, $userInput]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("No s'ha trobat cap compte associat.");
        }

        $resetCode = hash('sha256', random_bytes(32));
        $expiryTime = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $updateStmt = $pdo->prepare("UPDATE users SET resetPassCode = ?, resetPassExpiry = ? WHERE iduser = ?");
        $updateStmt->execute([$resetCode, $expiryTime, $user['iduser']]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'tunooapp@gmail.com';
            $mail->Password = 'tnhe xwqc vhrx qqbg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('tunooapp@gmail.com', 'TUNO APP');
            $mail->addAddress($user['mail']);

            $mail->isHTML(true);
            $mail->Subject = 'Reset de contrasenya';
            $resetLink = "http://localhost/projectePHP2/web/resetPassword.php?code=$resetCode&mail=" . urlencode($user['mail']);
            $mail->Body = "<h1>Reset de contrasenya</h1><p>Fes clic aquí per canviar la teva contrasenya: <a href='$resetLink'>Reset Password</a></p>";

            $mail->send();
            echo "Email enviat! Revisa la teva safata d'entrada.";
        } catch (Exception $e) {
            echo "Error en enviar el correu: " . $mail->ErrorInfo;
        }
    } catch (PDOException $e) {
        die("Error de connexió: " . $e->getMessage());
    }
} else {
    die("Dades no vàlides.");
}
?>
