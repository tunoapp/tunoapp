<?php
require 'db.php';
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $code = $_POST['code'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        die("Passwords do not match.");
    }

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $stmt = $pdo->prepare("SELECT iduser FROM users WHERE mail = ? AND resetPassCode = ?");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("Invalid reset request.");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET passHash = ?, resetPassCode = NULL, resetPassExpiry = NULL WHERE mail = ?");
        $stmt->execute([$hashedPassword, $email]);

        // Enviar correu de confirmació
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tunooapp@gmail.com';
        $mail->Password = 'fuln luuj zgpt tjyn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tunooapp@gmail.com', 'TUNO APP');
        $mail->addAddress($email);
        $mail->Subject = 'Password Changed Successfully';
        $mail->Body = "Your password has been successfully updated.";

        $mail->send();
        echo "Password updated! Redirecting...";
        header("Location: ../index.php");
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
