<?php
require_once 'proyecto.php'; 

// --- 1. Recibir datos POST ---
$cve_citas = isset($_POST['cve_citas']) ? (int)$_POST['cve_citas'] : 0;
// Sanitizar la acción para evitar inyecciones
$accion = isset($_POST['accion']) ? mysqli_real_escape_string($conexion, $_POST['accion']) : '';
$nuevo_estado = '';
$simulacion_email = '';
$mensaje_confirmacion = '';

// --- 2. Validar y definir el nuevo estado ---
if ($cve_citas > 0) {
    if ($accion === 'aceptar') {
        $nuevo_estado = 'aceptada';
        $simulacion_email = "Simulación: Email de confirmación de cita aceptada enviado al dueño.";
        $mensaje_confirmacion = "Cita ACEPTADA con éxito.";
    } elseif ($accion === 'cancelar') {
        $nuevo_estado = 'cancelada';
        $simulacion_email = "Simulación: Email de cancelación enviado al dueño.";
        $mensaje_confirmacion = "Cita CANCELADA con éxito.";
    }
}

// --- 3. Ejecutar la actualización en la BD ---
if (!empty($nuevo_estado)) {
    // Consulta para actualizar el estado
    $sql_update = "UPDATE citas SET estado = '$nuevo_estado' WHERE cve_citas = $cve_citas";
    
    if (mysqli_query($conexion, $sql_update)) {
        $exito = true;
    } else {
        $exito = false;
        $mensaje_confirmacion = "Error de base de datos al actualizar el estado: " . mysqli_error($conexion);
    }
} else {
    $exito = false;
    $mensaje_confirmacion = "Error: La clave de cita o la acción son inválidas.";
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas</title>
    <style>
        /* Estilos sencillos para mostrar la retroalimentación */
        body { font-family: sans-serif; text-align: center; padding-top: 50px; background-color: #f8f9fa; }
        .alert { 
            background-color: #fff; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            max-width: 400px; 
            margin: auto; 
            margin-top: 50px;
        }
        .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; }
        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="alert <?php echo $exito ? 'success' : 'error'; ?>">
        <h3><?php echo $exito ? 'Operación Exitosa' : 'Operación Fallida'; ?></h3>
        <p><?php echo $mensaje_confirmacion; ?></p>
        <?php if ($exito && !empty($simulacion_email)): ?>
            <p style="font-style: italic; color: #007bff; margin-top: 10px;"><?php echo $simulacion_email; ?></p>
        <?php endif; ?>
        
        <a href="Administrador.html" style="display: block; margin-top: 15px;">Volver al Panel de Administrador</a>
        
        <?php if ($exito): ?>
            <script>
                setTimeout(function(){ 
                    // Redirige al script ver_citas.php para que el administrador vea los cambios en la tabla
                    window.location.href = 'ver_citas.php';
                }, 3000); 
            </script>
        <?php endif; ?>
    </div>
</body>
</html>