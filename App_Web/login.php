<?php
//iniciar sesión al inicio del login.php
session_start();
// =================================================================================
// ⭐️ API ENDPOINT: LOGIN DE USUARIO (Validación de credenciales)
// =================================================================================

// --- 1. CONFIGURACIÓN DE SEGURIDAD (CORS) ---
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// --- 2. CONFIGURACIÓN DE CONEXIÓN ---
$host = "localhost";
$usuario = "root";
$contrasena = "joshuamanuel"; 
$nombre_bd = "dbo";
$puerto = 3306; 

// --- 3. CONEXIÓN A LA BASE DE DATOS ---
$conexion = new mysqli($host, $usuario, $contrasena, $nombre_bd, $puerto);

if ($conexion->connect_error) {
    http_response_code(500); 
    echo json_encode(["success" => false, "message" => "Error de conexión a MySQL."]);
    exit;
}
mysqli_set_charset($conexion, "utf8mb4");

// --- 4. RECIBIR Y DECODIFICAR LOS DATOS DEL FRONT-END ---
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null; 

if (empty($email) || empty($password)) {
    http_response_code(400); 
    echo json_encode(["success" => false, "message" => "Faltan correo o contraseña."]);
    exit;
}

// --- 5. CONSULTA SQL Y VALIDACIÓN ---
$sql = "SELECT password, nombre, apellido_paterno, cve_personas FROM usuario WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();
$usuarioDB = $resultado->fetch_assoc();

if ($usuarioDB) {
    // 6. VERIFICACIÓN DE CONTRASEÑA (sin hash, directo)
    $contrasena_correcta = ($password === $usuarioDB['password']); 
    
    if ($contrasena_correcta) {

        //Guardar sesión
        $_SESSION['usuario_logueado'] = true;
        $_SESSION['cve_personas'] = $usuarioDB['cve_personas'];
        $_SESSION['nombre_completo'] = $usuarioDB['nombre'] . " " . $usuarioDB['apellido_paterno'];
        $_SESSION['email'] = $email;

        // Éxito: Devolvemos los datos del usuario
        echo json_encode([
            "success" => true,
            "message" => "Login exitoso.",
            "nombre" => $usuarioDB['nombre'] . " " . $usuarioDB['apellido_paterno'],
            "cve_personas" => $usuarioDB['cve_personas']
        ]);
    } else {
        // Error: Contraseña incorrecta
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Contraseña incorrecta."]);
    }
} else {
    // Error: Usuario no encontrado
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "El correo no está registrado."]);
}

$stmt->close();
$conexion->close();
?>