<?php
// ====================================================================
// SCRIPT: PROCESAR CITA (ASUMIENDO INICIO DE SESIÓN)
// ====================================================================

// **Paso clave:** Iniciar la sesión para acceder a las variables de usuario logueado
session_start();

// Incluimos el script que contiene la conexión a la base de datos
require_once 'proyecto.php'; 

// === 1. VERIFICAR SESIÓN DEL DUEÑO ===
// Asumimos que la clave de la persona está guardada en $_SESSION['cve_personas']
if (!isset($_SESSION['cve_personas'])) {
    // Si no hay sesión, el usuario no está logueado
    die("Error de Acceso: Debes iniciar sesión para agendar una cita.");
    // Lo ideal sería redirigir a la página de login: header('Location: login.php');
}

// Obtener la clave del dueño de la sesión
$cve_personas = $_SESSION['cve_personas']; 


// === 2. CAPTURAR Y SANEAR LOS DATOS DEL FORMULARIO ===
// Los campos del dueño ya NO se capturan.
$nombre_mascota = mysqli_real_escape_string($conexion, $_POST['nombre_mascota']);
$especie = mysqli_real_escape_string($conexion, $_POST['especie']);
$raza = mysqli_real_escape_string($conexion, $_POST['raza']);        // Campo capturado
$edad = (int)mysqli_real_escape_string($conexion, $_POST['edad']);    // Campo capturado
$tipo_servicio = mysqli_real_escape_string($conexion, $_POST['tipo_servicio']);
$fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
$hora = mysqli_real_escape_string($conexion, $_POST['hora']);
$notas = mysqli_real_escape_string($conexion, $_POST['notas']);


// === 3. REGISTRAR/OBTENER LA cve_mascotas (MASCOTA) ===
$cve_mascotas = 0;
// Buscar si la mascota ya existe para ESTE dueño ($cve_personas)
$sql_mascota_check = "SELECT cve_mascotas FROM mascotas WHERE nombre = '$nombre_mascota' AND cve_personas = $cve_personas";
$resultado_mascota = mysqli_query($conexion, $sql_mascota_check);

if ($resultado_mascota === false) {
    die("Error de consulta al buscar mascota: " . mysqli_error($conexion));
}

if (mysqli_num_rows($resultado_mascota) > 0) {
    // Mascota existente: obtenemos su clave
    $fila_mascota = mysqli_fetch_assoc($resultado_mascota);
    $cve_mascotas = $fila_mascota['cve_mascotas'];
} else {
    // Mascota nueva: la registramos, usando los nuevos campos
    $sql_mascota_insert = "INSERT INTO mascotas (nombre, especie, raza, edad, cve_personas) 
                           VALUES ('$nombre_mascota', '$especie', '$raza', $edad, $cve_personas)";

    if (mysqli_query($conexion, $sql_mascota_insert)) {
        $cve_mascotas = mysqli_insert_id($conexion); // Obtener el ID de la nueva mascota
    } else {
        die("Error al registrar la mascota: " . mysqli_error($conexion));
    }
}
if ($cve_mascotas > 0) {
    // 4a. Verificar si la MISMA MASCOTA ya tiene una cita a ESA hora/fecha
    $sql_check_mascota = "SELECT COUNT(*) FROM citas 
                          WHERE cve_mascotas = $cve_mascotas 
                          AND fecha = '$fecha' 
                          AND hora = '$hora'";
    $res_mascota = mysqli_query($conexion, $sql_check_mascota);
    $count_mascota = mysqli_fetch_array($res_mascota)[0];

    if ($count_mascota > 0) {
        die("
            <!DOCTYPE html>
            <html lang='es'><head><meta charset='UTF-8'><title>Error</title></head>
            <body style='font-family: sans-serif; text-align: center; padding-top: 50px;'>
                <h2 style='color: #dc3545;'>❌ Error de Cita</h2>
                <p>Tu mascota **$nombre_mascota** ya tiene una cita registrada para la fecha **$fecha** a las **$hora**.</p>
                <p>Por favor, selecciona otra hora o revisa tus citas existentes.</p>
                <p><a href='Citas.html'>Volver a agendar cita.</a></p>
            </body>
            </html>
        ");
    }

    // 4b. Verificar si el tipo de servicio es 'cirugia' o 'urgencia' y ya hay otra cita del MISMO TIPO a ESA hora.
    // Esta validación asume que solo puede haber 1 cirugía/urgencia por hora en la clínica.
    if ($tipo_servicio == 'cirugia' || $tipo_servicio == 'urgencia') {
        $sql_check_especialidad = "SELECT COUNT(*) FROM citas 
                                   WHERE tipo_servicio = '$tipo_servicio' 
                                   AND fecha = '$fecha' 
                                   AND hora = '$hora'";
        $res_especialidad = mysqli_query($conexion, $sql_check_especialidad);
        $count_especialidad = mysqli_fetch_array($res_especialidad)[0];

        if ($count_especialidad > 0) {
            die("
                <!DOCTYPE html>
                <html lang='es'><head><meta charset='UTF-8'><title>Error</title></head>
                <body style='font-family: sans-serif; text-align: center; padding-top: 50px;'>
                    <h2 style='color: #dc3545;'>❌ Servicio No Disponible</h2>
                    <p>Ya existe otra cita de **$tipo_servicio** programada para la fecha **$fecha** a las **$hora**.</p>
                    <p>Por favor, selecciona otro horario o contacta a la clínica.</p>
                    <p><a href='Citas.html'>Volver a agendar cita.</a></p>
                </body>
                </html>
            ");
        }
    }
}


// === 4. REGISTRAR LA CITA ===
if ($cve_mascotas > 0) {
    $sql_cita = "INSERT INTO citas (cve_personas, cve_mascotas, tipo_servicio, fecha, hora, notas, estado) 
                 VALUES ($cve_personas, $cve_mascotas, '$tipo_servicio', '$fecha', '$hora', '$notas', 'pendiente')";

    if (mysqli_query($conexion, $sql_cita)) {
        // 5. Éxito: Mostrar mensaje de confirmación y redirigir
        echo "
            <!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <title>Cita Registrada</title>
                <style>
                    body { font-family: sans-serif; text-align: center; padding-top: 50px; background-color: #f4f4f9; }
                    .confirm-box { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; margin: auto; }
                    h2 { color: #28a745; }
                    a { color: #007bff; text-decoration: none; }
                </style>
            </head>
            <body>
                <div class='confirm-box'>
                    <h2>✅ ¡Cita Solicitada y Registrada con Éxito!</h2>
                    <p>Tu cita para **$nombre_mascota** para el servicio de **$tipo_servicio** el **$fecha** a las **$hora** ha sido guardada.</p>
                    <p>Serás redirigido a la página de citas en 5 segundos...</p>
                    <p><a href='Citas.html'>O haz clic aquí para volver inmediatamente.</a></p>
                    <script>setTimeout(function(){ window.location.href = 'Citas.html'; }, 5000);</script>
                </div>
            </body>
            </html>
        ";
    } else {
        echo "Error al registrar la cita: " . mysqli_error($conexion);
    }
} else {
     echo "Error fatal: No se pudo obtener el ID de la mascota. Revisa la lógica de registro.";
}

// Cierra la conexión a la base de datos
mysqli_close($conexion);
?>