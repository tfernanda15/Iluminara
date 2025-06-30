<?php
// public/api/reviews.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Permitir GET y POST
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Para manejar las solicitudes OPTIONS preflight (necesarias para POST con CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../app/config/db_config.php';

$conn = connectDB();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error interno del servidor: No se pudo conectar a la base de datos."]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Obtener reseñas por product_id
        if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
            $productId = intval($_GET['product_id']);
            getReviewsByProductId($conn, $productId);
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "ID de producto no proporcionado o inválido."]);
        }
        break;

    case 'POST':
        // Añadir una nueva reseña
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->product_id) || !isset($data->username) || !isset($data->rating) || empty($data->username) || !is_numeric($data->rating) || $data->rating < 1 || $data->rating > 5) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Datos de reseña incompletos o inválidos (product_id, username, rating son requeridos y rating debe ser entre 1 y 5)."]);
            exit();
        }

        $productId = intval($data->product_id);
        $username = trim($conn->real_escape_string($data->username));
        $rating = intval($data->rating);
        // Asegurarse de que el comentario sea null si está vacío, para que la BD lo guarde así si la columna lo permite.
        $comment = isset($data->comment) && trim($data->comment) !== '' ? trim($conn->real_escape_string($data->comment)) : null;
        
        $userId = null; 
        // Si tienes la autenticación implementada, descomenta la siguiente línea:
        // if (isset($_SESSION['user_id'])) {
        //     $userId = intval($_SESSION['user_id']);
        // }

        addReview($conn, $productId, $userId, $username, $rating, $comment);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        break;
}

$conn->close();

/**
 * Obtiene todas las reseñas para un producto específico.
 * Utiliza COALESCE para asegurar que username y comment no sean NULL,
 * lo que podría causar problemas de visualización en el frontend.
 * @param mysqli $conn La conexión a la base de datos.
 * @param int $productId El ID del producto.
 */
function getReviewsByProductId($conn, $productId) {
    $reviews = [];
    // Uso de COALESCE(columna, '') para asegurar que NULL se devuelva como cadena vacía
    $sql = "SELECT id, product_id, user_id, 
                   COALESCE(username, '') AS username, 
                   rating, 
                   COALESCE(comment, '') AS comment, 
                   created_at 
            FROM reviews WHERE product_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("getReviewsByProductId: Error al preparar la consulta: " . $conn->error);
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error interno al preparar la consulta de reseñas."]);
        return;
    }

    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    echo json_encode(["success" => true, "reviews" => $reviews]);
    $stmt->close();
}

/**
 * Añade una nueva reseña a la base de datos.
 * @param mysqli $conn La conexión a la base de datos.
 * @param int $productId El ID del producto.
 * @param int|null $userId El ID del usuario (NULL si es invitado).
 * @param string $username El nombre de usuario.
 * @param int $rating La puntuación de 1 a 5.
 * @param string|null $comment El comentario de la reseña.
 */
function addReview($conn, $productId, $userId, $username, $rating, $comment) {
    $sql = "INSERT INTO reviews (product_id, user_id, username, rating, comment) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("addReview: Error al preparar la consulta: " . $conn->error);
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error interno al preparar la consulta para añadir reseña."]);
        return;
    }
    
    // ¡LA CORRECCIÓN CRÍTICA! Tipos de datos correctos para bind_param:
    // i: product_id (int)
    // i: user_id (int o null, handled by mysqli)
    // s: username (string)
    // i: rating (int)
    // s: comment (string o null)
    $stmt->bind_param("iisss", $productId, $userId, $username, $rating, $comment);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Reseña añadida con éxito."]);
    } else {
        error_log("addReview: Error al ejecutar la consulta: " . $stmt->error);
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error al guardar la reseña: " . $stmt->error]);
    }
    $stmt->close();
}

