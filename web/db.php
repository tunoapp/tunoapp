<?php
$cadena_connexio = 'mysql:dbname=tunobd;host=localhost:3335';
$usuari = 'scoba';
$passwd = '1234';
try {
    $pdo = new PDO($cadena_connexio, $usuari, $passwd);
} catch (PDOException $e) {
    die("Error de connexió: " . $e->getMessage());
}
