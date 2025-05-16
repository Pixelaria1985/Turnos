<?php
$servidor = "localhost";
$usuario = "root";  // Cambia esto si usas otro usuario
$contrasena = "";   // Cambia esto si usas una contraseña
$base_de_datos = "consultorio";

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$base_de_datos", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
