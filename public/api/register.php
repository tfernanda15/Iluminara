<?php
// public/api/register.php

// Configurar encabezados CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Manejar solicitudes OPTIONS (pre-vuelo CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir el archivo de conexión a la base de datos
require_once '../../app/config/db_config.php';

$conn = connectDB();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error interno del servidor: No se pudo conectar a la base de datos."]);
    exit();
}

// Decodificar el cuerpo de la petición JSON
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validar datos de entrada
if (empty($username) || empty($email) || empty($password)) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios (nombre de usuario, email, contraseña)."]);
    $conn->close();
    exit();
}

// Verificar si el email ya existe
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al preparar la consulta de email: " . $conn->error]);
    $conn->close();
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(["success" => false, "message" => "El correo electrónico ya está registrado."]);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// Hashear la contraseña
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Insertar nuevo usuario
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al preparar la inserción de usuario: " . $conn->error]);
    $conn->close();
    exit();
}
$stmt->bind_param("sss", $username, $email, $passwordHash);

if ($stmt->execute()) {
    http_response_code(201); // Created
    echo json_encode(["success" => true, "message" => "Registro exitoso. Ahora puedes iniciar sesión."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al registrar usuario: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

