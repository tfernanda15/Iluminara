<?php
// public/api/auth.php

// Configurar encabezados CORS para permitir solicitudes desde tu frontend
header("Access-Control-Allow-Origin: *"); // Permite cualquier origen. En producción, especifica tu dominio.
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Incluir el archivo de conexión a la base de datos (ruta corregida)
require_once '../../app/config/db_config.php'; 

// Obtener el método de la solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['action'])) {
            switch ($data['action']) {
                case 'register':
                    registerUser($data);
                    break;
                case 'login':
                    loginUser($data);
                    break;
                case 'update_profile':
                    updateUserProfile($data);
                    break;
                default:
                    echo json_encode(["success" => false, "message" => "Acción no válida."]);
                    break;
            }
        } else {
            echo json_encode(["success" => false, "message" => "Acción no especificada."]);
        }
        break;
    default:
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        break;
}

/**
 * Registra un nuevo usuario.
 * @param array $data Contiene 'username', 'email', 'password'.
 */
function registerUser($data) {
    $conn = connectDB();
    $username = $conn->real_escape_string($data['username']);
    $email = $conn->real_escape_string($data['email']);
    $password = password_hash($conn->real_escape_string($data['password']), PASSWORD_DEFAULT); // Hash de la contraseña

    // Verificar si el email ya existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El email ya está registrado."]);
        $stmt->close();
        $conn->close();
        return;
    }
    $stmt->close();

    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registro exitoso."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}

/**
 * Inicia sesión para un usuario.
 * @param array $data Contiene 'email', 'password'.
 */
function loginUser($data) {
    $conn = connectDB();
    $email = $conn->real_escape_string($data['email']);
    $inputPassword = $conn->real_escape_string($data['password']);

    $stmt = $conn->prepare("SELECT id, username, email, password, phone FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($inputPassword, $user['password'])) {
            // Eliminar la contraseña del objeto de usuario antes de enviarlo al frontend
            unset($user['password']);
            echo json_encode(["success" => true, "message" => "Inicio de sesión exitoso.", "user" => $user]);
        } else {
            echo json_encode(["success" => false, "message" => "Email o contraseña incorrectos."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Email o contraseña incorrectos."]);
    }

    $stmt->close();
    $conn->close();
}

/**
 * Actualiza el perfil de un usuario.
 * @param array $data Contiene 'id', 'username', 'phone'.
 */
function updateUserProfile($data) {
    $conn = connectDB();
    $userId = $conn->real_escape_string($data['id']);
    $username = $conn->real_escape_string($data['username']);
    $phone = isset($data['phone']) ? $conn->real_escape_string($data['phone']) : null;

    $stmt = $conn->prepare("UPDATE users SET username = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $phone, $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Recuperar el usuario actualizado para enviar al frontend
            $selectStmt = $conn->prepare("SELECT id, username, email, phone FROM users WHERE id = ?");
            $selectStmt->bind_param("i", $userId);
            $selectStmt->execute();
            $result = $selectStmt->get_result();
            $updatedUser = $result->fetch_assoc();
            $selectStmt->close();

            echo json_encode(["success" => true, "message" => "Perfil actualizado con éxito.", "user" => $updatedUser]);
        } else {
            echo json_encode(["success" => false, "message" => "No se realizaron cambios o usuario no encontrado."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar perfil: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>

