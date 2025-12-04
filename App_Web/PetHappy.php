<?php
session_start();

// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    header("Location: Inicio.html");
    exit();
}

// Obtener el nombre del usuario de la sesión
$nombre_usuario = $_SESSION['nombre_completo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHappy - Clínica Veterinaria</title>
    <link rel="stylesheet" href="PetHappy.css">
    <link rel="stylesheet" href="perfil_menu.css">
</head>

<body>
    <div class="user-profile-header">
    <p class="welcome-message">Bienvenido/a, <span id="user-display">Invitado</span></p>
    <div class="profile-container">
        <div id="profile-picture-circle">
            👤
        </div>
        <span class="dropdown-icon"></span>
        <div class="dropdown">
    <button onclick="toggleMenu()" class="dropbtn">▼</button>
    
    <div id="admin-dropdown" class="dropdown-content">
    
    <a href="#" 
       onclick="activarModoAdministrador()" 
       id="acceso-veterinario-btn">Acceso: Veterinario</a>
    
    <a href="#" 
       onclick="activarModoAdministradorSuperior()" 
       id="acceso-superior-btn" 
       style="display: none;">Acceso: Administrador</a> 

    <a id="volver-admin-btn" 
       href="#" 
       onclick="volverAdminPanel()" 
       style="display: none;">Volver al Panel</a>
       
    <button id="cerrar-admin-btn" 
            onclick="cerrarModoAdministrador()" 
            style="display: none;">Cerrar Nivel</button>

    <a id="cerrar-sesion-btn" href="cerrar_sesion.php">Cerrar Sesión</a>
</div>
</div>
    </div>
    
</div>
    <div id="imagen">
        <img src="Img/ph_banner.png" alt="Fondo PetHappy - Mascotas Felices">
    </div>

    <div class="Contactanos">
        <hr>
        <h1>Nuestros Servicios Veterinarios</h1>
        <hr>

        <img src="Img/PetHappy.png" width="570" height="550" alt="Servicios Veterinarios">

        <ul>
            <li>Consulta General y Chequeo Preventivo</li>
            <li>Vacunación y Control de Parásitos</li>
            <li>Cirugías Mayores y Menores</li>
            <li>Odontología Veterinaria</li>
            <li>Análisis Clínicos y Laboratorio</li>
            <li>Hospitalización y Cuidados Intensivos</li>
            <li>Control de Cachorros y Geriatría</li>
            <li>Estética Canina y Felina (Grooming)</li>
            <li>Radiografía y Ecografía</li>
        </ul>
    </div>

    <div class="menu-btn">
        <button onclick="irA('Cortes.html')">Estetica</button>
        <button onclick="irA('Horarios.html')">Horarios</button>
        <button onclick="irA('Precios.html')">Precios</button>
        <button onclick="irA('Farmacia.html')">Farmacia</button>
        <button onclick="irA('Citas.html')">Agendar Citas</button>
    </div>
    <script src="TOTALSC.js"></script>
</body>
</html>
