<?php
// 🚨 VERIFICA ESTOS PARÁMETROS 🚨
$host = "localhost";
$usuario = "root";
$contrasena = "joshuamanuel"; 
$nombre_bd = "dbo"; // Usamos el nombre que especificaste
$puerto = 3306; 

// Intento de conexión
$conexion = new mysqli($host, $usuario, $contrasena, $nombre_bd, $puerto);

if ($conexion->connect_error) {
    // Si falla, mostramos el error detallado de MySQL
    die("<h1>❌ ERROR CRÍTICO DE CONEXIÓN A MYSQL</h1>" . 
        "<p><strong>1. ¿Está MySQL corriendo?</strong> Verifique su panel de AppServ/XAMPP.</p>" . 
        "<p><strong>2. Credenciales:</strong> El nombre de la BD, usuario ('root') o contraseña ('joshuamanuel') son incorrectos.</p>" . 
        "<hr>" . 
        "<p><strong>Detalle del error de MySQL:</strong> (" . $conexion->connect_errno . ") " . $conexion->connect_error . "</p>"
    );
}

echo "<h1>✅ CONEXIÓN EXITOSA</h1>";
echo "<p>Conectado a la base de datos: <strong>$nombre_bd</strong></p>";
$conexion->close();
?>