<?php
// ====================================================================
// SCRIPT: VER CITAS (PANEL ADMINISTRADOR)
// OBJETIVO: Mostrar todas las citas o filtrar por tipo_servicio.
// ====================================================================

// Incluimos la conexión a la base de datos.
require_once 'proyecto.php'; 

// --- 1. Definir la consulta base y el título ---
$sql_base = "
    SELECT 
        c.fecha, 
        c.hora, 
        c.tipo_servicio, 
        c.notas,
        c.cve_citas,
        c.estado, 
        m.nombre AS nombre_mascota, 
        m.especie,
        m.raza,
        u.nombre AS nombre_dueno,
        u.telefono AS tel_dueno,
        u.email AS email_dueno
    FROM citas c
    JOIN mascotas m ON c.cve_mascotas = m.cve_mascotas
    JOIN usuario u ON c.cve_personas = u.cve_personas
";

$where_clause = "";
$titulo = "📋 Todas las Citas Registradas";

// --- 2. Lógica de Filtrado por GET (si se seleccionó un filtro) ---
if (isset($_GET['tipo']) && !empty($_GET['tipo'])) {
    $tipo_servicio = mysqli_real_escape_string($conexion, $_GET['tipo']);
    
    // Solo permitimos los tipos definidos en Administrador.html para seguridad básica
    $tipos_validos = ['consulta', 'vacunacion', 'estetica', 'cirugia', 'urgencia'];

    if (in_array($tipo_servicio, $tipos_validos)) {
        $where_clause = " WHERE c.tipo_servicio = '$tipo_servicio' ";
        
        // Define un título más amigable
        $nombres_servicios = [
            'consulta' => 'Consultas Generales',
            'vacunacion' => 'Vacunación y Desparasitación',
            'estetica' => 'Estética y Grooming',
            'cirugia' => 'Revisiones Post-Cirugía',
            'urgencia' => 'Urgencias'
        ];
        $titulo = "🩺 Citas Filtradas: " . ($nombres_servicios[$tipo_servicio] ?? $tipo_servicio);
    }
}

// --- 3. Construir y ejecutar la consulta final ---
$sql_final = $sql_base . $where_clause . " ORDER BY c.fecha ASC, c.hora ASC";
$resultado_citas = mysqli_query($conexion, $sql_final);

if (!$resultado_citas) {
    die("Error al ejecutar la consulta: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> | PetHappy Panel</title>
    <style>
        /* CSS Básico para la Tabla */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background-color: #f8f9fa; }
        h1 { color: #007bff; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background-color: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; border: 1px solid #dee2e6; text-align: left; }
        th { background-color: #28a745; color: white; font-weight: bold; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e9ecef; }
        .back-btn { padding: 10px 20px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <a href="Administrador.html" class="back-btn">← Volver al Panel de Citas</a>
    <h1><?php echo $titulo; ?></h1>

    <?php if (mysqli_num_rows($resultado_citas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Servicio</th>
                    <th>Mascota (Especie/Raza)</th>
                    <th>Dueño</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Notas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cita = mysqli_fetch_assoc($resultado_citas)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cita['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($cita['hora']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($cita['tipo_servicio'])); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($cita['nombre_mascota']); ?></strong> 
                        (<?php echo htmlspecialchars($cita['especie'] . '/' . $cita['raza']); ?>)
                    </td>
                    <td><?php echo htmlspecialchars($cita['nombre_dueno']); ?></td>
                    <td><?php echo htmlspecialchars($cita['tel_dueno']); ?></td>
                    <td><?php echo htmlspecialchars($cita['email_dueno']); ?></td>
                    <td><?php echo htmlspecialchars($cita['notas']); ?></td>

                    <td>
                        <?php if ($cita['estado'] === 'pendiente'): ?>
                            
                            <form action="gestionar_cita.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="cve_citas" value="<?php echo $cita['cve_citas']; ?>">
                                <input type="hidden" name="accion" value="aceptar">
                                <button type="submit" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;">Aceptar</button>
                            </form>

                            <form action="gestionar_cita.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="cve_citas" value="<?php echo $cita['cve_citas']; ?>">
                                <input type="hidden" name="accion" value="cancelar">
                                <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px;" onclick="return confirm('¿Estás seguro de cancelar esta cita?');">Cancelar</button>
                            </form>

                        <?php else: ?>
                            <span style="font-weight: bold; color: <?php echo ($cita['estado'] === 'cancelada' ? 'red' : 'blue'); ?>;"><?php echo ucfirst($cita['estado']); ?></span>
                        <?php endif; ?>
                    </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #6c757d; font-size: 1.2em;">✅ No hay citas registradas para este filtro.</p>
    <?php endif; ?>

    <?php 
    // Cerrar la conexión
    mysqli_close($conexion);
    ?>
</body>
</html>