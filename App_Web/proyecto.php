<?php
// --- 1. Definir los parámetros de la base de datos ---
$host = "localhost";
$usuario = "root";
$contrasena = "joshuamanuel"; 
$nombre_bd = "dbo";
$puerto = 3306; // O 3307 si lo cambiaste


$conexion = mysqli_connect($host, $usuario, $contrasena, $nombre_bd, $puerto);


if (mysqli_connect_errno()) {
    die("Fallo en la conexión a MySQL: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");



