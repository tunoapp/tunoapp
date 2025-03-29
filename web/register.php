<?php
require 'db.php';
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $attractedTo = $_POST['attracted_to'] ?? '';

    if ($password !== $confirmPassword) {
        die("Les contrasenyes no coincideixen.");
    }

    $query = $pdo->prepare("SELECT * FROM users WHERE mail = ?");
    $query->execute([$email]);
    if ($query->fetch()) {
        die("Aquest correu ja està registrat.");
    }

    $activationCode = hash('sha256', random_bytes(32));
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare("INSERT INTO users (username, mail, passHash, userFirstName, userLastName, activationCode, active, gender, attracted_to) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)");
    $insert->execute([$username, $email, $hashedPassword, $firstName, $lastName, $activationCode, $gender, $attractedTo]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tunooapp@gmail.com';
        $mail->Password = 'fuln luuj zgpt tjyn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tunooapp@gmail.com', 'TUNO APP');
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = 'Activa el teu compte';
        $activationLink = "http://localhost/projectePHP2/web/mailCheckAccount.php?code=$activationCode&mail=$email";

        $profileImageUrl = "https://drive.google.com/u/0/drive-viewer/AKGpihZNfYyRUz3QHoDj80aBCzoyXI7WQyI9IS5FD5tDr17HU7sTr17iXELzCwAjjEWnJUG5T8sE8i509PtndnD8IUnllol3ouySG14=s2560"; // Ajusta la URL según donde almacenes las imágenes

        $mail->Body = "<div style='max-width: 600px; margin: auto; padding: 20px; font-family: Arial, sans-serif; background-color: #f8f9fa; text-align: center; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);'>
            <h2 style='color: #333;'>Benvingut a TUNO APP, $username!</h2>
            <img src='$profileImageUrl' style='width: 150px; margin-bottom: 20px;'>
            <p style='color: #555; font-size: 16px;'>Per començar a utilitzar la nostra aplicació, activa el teu compte fent clic al botó següent.</p>
            <a href='$activationLink' style='display: inline-block; padding: 12px 24px; font-size: 18px; color: white; background-color: #ff4081; text-decoration: none; border-radius: 5px; margin-top: 15px;'>Activar el meu compte</a>
            <p style='color: #777; font-size: 14px; margin-top: 20px;'>Si no has creat aquest compte, ignora aquest missatge.</p>
        </div>";


        $mail->send();
        echo "Registre complet! Revisa el teu correu per activar el compte.";
    } catch (Exception $e) {
        echo "Error en enviar el correu: " . $mail->ErrorInfo;
    }
}
?>

    <!DOCTYPE html>
    <html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registre</title>
        <link rel="shortcut icon" href="../img/icono.ico" type="image/x-icon">
        <link rel="stylesheet" href="../styles/index.css">
        <link rel="stylesheet" href="../styles/styles.css">

    </head>
    <body class="align">
        <div class="grid">
        <h1 class="text--center">Registra't</h1>

        <?php if (isset($error)): ?>
            <p class="error text--center" style="color: red; font-size: 0.9rem;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" class="form login">
            <div class="form__field">
                <label for="username"><svg class="icon">
                    <use xlink:href="#icon-user"></use>
                </svg><span class="hidden">Nom d'usuari</span></label>
                <input autocomplete="username" id="username" type="text" name="username" class="form__input" placeholder="Nom d'usuari" required>
            </div>

            <div class="form__field">
                <label for="email"><svg class="icon">
                    <use xlink:href="#icon-user"></use>
                </svg><span class="hidden">Correu electronic</span></label>
                <input id="email" type="email" name="email" class="form__input" placeholder=" Correu electronic" required>
            </div>

            <div class="form__field">
                <label for="password"><svg class="icon">
                    <use xlink:href="#icon-lock"></use>
                </svg><span class="hidden">Contrasenya</span></label>
                <input id="password" type="password" name="password" class="form__input" placeholder="Contrasenya" required>
            </div>

            <div class="form__field">
                <label for="confirm_password"><svg class="icon">
                    <use xlink:href="#icon-lock"></use>
                </svg><span class="hidden">Confirma la contrasenya</span></label>
                <input id="confirm_password" type="password" name="confirm_password" class="form__input" placeholder="Confirma la contrasenya" required>
            </div>
                
            <div class="form__field">
                    <input type="text" name="first_name" class="form__input" placeholder="Nom (opcional)">
                </div>

                <div class="form__field">
                    <input type="text" name="last_name" class="form__input" placeholder="Cognom (opcional)">
                </div>
            <div class="form__field">
                <label for="gender">El teu sexe</label>
                <select name="gender" id="gender" required>
                    <option value="M">Home</option>
                    <option value="F">Dona</option>
                </select>
            </div>

            <div class="form__field">
                <label for="attracted_to">Sexe que t'atrau</label>
                <select name="attracted_to" id="attracted_to" required>
                    <option value="M">Homes</option>
                    <option value="F">Dones</option>
                    <option value="B">Ambdós</option>
                </select>
            </div>
            <div class = "form_field"> 
                <label for="profileImage">Foto de Perfil:</label>
                    <input type="file" name="profileImage" id="profileImage" accept="image/*">
            </div>    
                <div class="form__field">
                    <input type="submit" value="Registrar">
                </div>
        </form>

        <p class="text--center">Ja tens un compte? <a href="../index.php">Inicia sessió</a> <svg class="icon">
            <use xlink:href="#icon-arrow-right"></use>
            </svg></p>

        </div>

        <svg xmlns="http://www.w3.org/2000/svg" class="icons" style="display: none;">
        <symbol id="icon-arrow-right" viewBox="0 0 1792 1792">
            <path d="M1600 960q0 54-37 91l-651 651q-39 37-91 37-51 0-90-37l-75-75q-38-38-38-91t38-91l293-293H245q-52 0-84.5-37.5T128 1024V896q0-53 32.5-90.5T245 768h704L656 474q-38-36-38-90t38-90l75-75q38-38 90-38 53 0 91 38l651 651q37 35 37 90z" />
        </symbol>
        <symbol id="icon-lock" viewBox="0 0 1792 1792">
            <path d="M640 768h512V576q0-106-75-181t-181-75-181 75-75 181v192zm832 96v576q0 40-28 68t-68 28H416q-40 0-68-28t-28-68V864q0-40 28-68t68-28h32V576q0-184 132-316t316-132 316 132 132 316v192h32q40 0 68 28t28 68z" />
        </symbol>
        <symbol id="icon-user" viewBox="0 0 1792 1792">
            <path d="M1600 1405q0 120-73 189.5t-194 69.5H459q-121 0-194-69.5T192 1405q0-53 3.5-103.5t14-109T236 1084t43-97.5 62-81 85.5-53.5T538 832q9 0 42 21.5t74.5 48 108 48T896 971t133.5-21.5 108-48 74.5-48 42-21.5q61 0 111.5 20t85.5 53.5 62 81 43 97.5 26.5 108.5 14 109 3.5 103.5zm-320-893q0 159-112.5 271.5T896 896 624.5 783.5 512 512t112.5-271.5T896 128t271.5 112.5T1280 512z" />
        </symbol>
        </svg>

    </body>
    </html>
