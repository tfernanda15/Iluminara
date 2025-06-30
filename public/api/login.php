<?php
// public/api/login.php

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

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validar datos de entrada
if (empty($email) || empty($password)) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Email y contraseña son obligatorios."]);
    $conn->close();
    exit();
}

// Buscar el usuario por email
$stmt = $conn->prepare("SELECT id, username, email, password_hash FROM users WHERE email = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al preparar la consulta de login: " . $conn->error]);
    $conn->close();
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password_hash'])) {
    http_response_code(200); // OK
    echo json_encode([
        "success" => true,
        "message" => "Inicio de sesión exitoso.",
        "user" => [
            "id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email']
        ]
    ]);
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "message" => "Credenciales inválidas. Por favor, verifica tu email y contraseña."]);
}

$stmt->close();
$conn->close();
?>

