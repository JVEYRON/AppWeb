<?php
// --- 1. Incluir o definir la conexión a la base de datos ---
$host = "localhost";
$usuario = "root";
$contrasena = "joshuamanuel"; 
$nombre_bd = "dbo";
$puerto = 3306;

$conexion = mysqli_connect($host, $usuario, $contrasena, $nombre_bd, $puerto);

// Verificar la conexión
if (mysqli_connect_errno()) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");

// --- 2. Procesar el formulario cuando se envía ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre'])) {
    
    // Recibir y limpiar los datos del formulario
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $apellido_paterno = mysqli_real_escape_string($conexion, trim($_POST['apellido_paterno']));
    $apellido_materno = mysqli_real_escape_string($conexion, trim($_POST['apellido_materno']));
    $telefono = mysqli_real_escape_string($conexion, trim($_POST['telefono']));
    $email = mysqli_real_escape_string($conexion, trim($_POST['email']));
    $password = mysqli_real_escape_string($conexion, trim($_POST['password']));
    
    // --- 3. Validaciones ---
    $errores = [];
    
    // Validar que el email sea válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido";
    }
    
    // Validar que el teléfono tenga solo números
    if (!is_numeric($telefono)) {
        $errores[] = "El teléfono debe contener solo números";
    }
    $query_check_email = "SELECT email FROM usuario WHERE email = ?";
$stmt_check = mysqli_prepare($conexion, $query_check_email);

if ($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        // El correo ya existe
        $errores[] = "El correo electrónico ya está registrado. Por favor, usa otro.";
    }

    mysqli_stmt_close($stmt_check);
} else {
    $errores[] = "Error al preparar la consulta de verificación: " . mysqli_error($conexion);
}
    
    // --- 4. Si no hay errores, proceder con el registro ---
    if (empty($errores)) {
        
        // El campo activo se establece en 1 por defecto (usuario activo)
        $activo = 1;
        
        // Insertar el nuevo usuario en la tabla usuario
        $query_insert = "INSERT INTO usuario (nombre, apellido_paterno, apellido_materno, telefono, email, password, activo) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_insert = mysqli_prepare($conexion, $query_insert);
        
        if ($stmt_insert) {
            mysqli_stmt_bind_param($stmt_insert, "ssssssi", $nombre, $apellido_paterno, $apellido_materno, $telefono, $email, $password, $activo);
            
            if (mysqli_stmt_execute($stmt_insert)) {
                // Registro exitoso
                echo "<script>
                        alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
                        window.location.href = 'Inicio.html';
                      </script>";
                exit();
            } else {
                $errores[] = "Error al registrar usuario: " . mysqli_error($conexion);
            }
            
            mysqli_stmt_close($stmt_insert);
        } else {
            $errores[] = "Error al preparar la consulta: " . mysqli_error($conexion);
        }
    }
    
    // --- 5. Mostrar errores si los hay ---
    if (!empty($errores)) {
        echo "<script>alert('Errores:\\n";
        foreach ($errores as $error) {
            echo "- " . addslashes($error) . "\\n";
        }
        echo "');</script>";
        echo "<script>window.history.back();</script>";
    }
}

mysqli_close($conexion);
?>