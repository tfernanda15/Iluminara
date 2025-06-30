<?php
// public/api/cart.php

// Configurar encabezados CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Incluir el archivo de conexión a la base de datos (ruta corregida)
require_once '../../app/config/db_config.php'; 

// Manejar solicitudes OPTIONS (pre-vuelo CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$conn = connectDB();

// En una aplicación real, el user_id se obtendría de una sesión o token de autenticación.
// Por ahora, lo leeremos del frontend o de un valor por defecto para pruebas.
// Asumo que el user_id se enviará en el cuerpo de la solicitud para POST/PUT/DELETE
// y como parámetro de consulta para GET.
// Ojo: Esto es una simplificación. ¡Implementar autenticación real es crucial!

switch ($method) {
    case 'GET':
        // api/cart.php?user_id=1
        $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        if ($userId > 0) {
            getCartItems($conn, $userId);
        } else {
            echo json_encode(["success" => false, "message" => "ID de usuario no especificado."]);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['action']) && isset($data['user_id'])) {
            switch ($data['action']) {
                case 'add':
                    addCartItem($conn, $data);
                    break;
                case 'remove':
                    removeCartItem($conn, $data);
                    break;
                case 'update_quantity':
                    updateCartItemQuantity($conn, $data);
                    break;
                case 'clear':
                    clearCart($conn, $data['user_id']);
                    break;
                default:
                    echo json_encode(["success" => false, "message" => "Acción de carrito no válida."]);
                    break;
            }
        } else {
            echo json_encode(["success" => false, "message" => "Datos incompletos para acción de carrito."]);
        }
        break;
    default:
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        break;
}

$conn->close();

/**
 * Obtiene los ítems del carrito de un usuario.
 * @param mysqli $conn
 * @param int $userId
 */
function getCartItems($conn, $userId) {
    $cartItems = [];
    $stmt = $conn->prepare("
        SELECT ci.product_id, ci.quantity, p.name, p.price, pi.image_url
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id
        WHERE ci.user_id = ?
        GROUP BY ci.product_id -- Agrupar para asegurar una imagen por producto
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cartItems[] = [
            'id' => $row['product_id'], // Usamos 'id' para consistencia con el frontend
            'name' => $row['name'],
            'price' => (float)$row['price'],
            'quantity' => (int)$row['quantity'],
            'imageUrl' => $row['image_url'] ? $row['image_url'] : 'https://placehold.co/692x620/8a2be2/5C2E7E?text=No+Image'
        ];
    }

    echo json_encode(["success" => true, "cartItems" => $cartItems]);
    $stmt->close();
}

/**
 * Añade un producto al carrito o actualiza su cantidad.
 * @param mysqli $conn
 * @param array $data Contiene 'user_id', 'product_id', 'quantity' (opcional, default 1).
 */
function addCartItem($conn, $data) {
    $userId = $conn->real_escape_string($data['user_id']);
    $productId = $conn->real_escape_string($data['product_id']);
    $quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;

    // Verificar si el producto ya existe en el carrito del usuario
    $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("is", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizar cantidad
        $existingItem = $result->fetch_assoc();
        $newQuantity = $existingItem['quantity'] + $quantity;
        $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $newQuantity, $existingItem['id']);
        if ($updateStmt->execute()) {
            echo json_encode(["success" => true, "message" => "Cantidad de producto actualizada en el carrito.", "quantity" => $newQuantity]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar cantidad: " . $updateStmt->error]);
        }
        $updateStmt->close();
    } else {
        // Añadir nuevo producto
        $insertStmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->bind_param("isi", $userId, $productId, $quantity);
        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "message" => "Producto añadido al carrito."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al añadir producto: " . $insertStmt->error]);
        }
        $insertStmt->close();
    }
    $stmt->close();
}

/**
 * Elimina un producto del carrito.
 * @param mysqli $conn
 * @param array $data Contiene 'user_id', 'product_id'.
 */
function removeCartItem($conn, $data) {
    $userId = $conn->real_escape_string($data['user_id']);
    $productId = $conn->real_escape_string($data['product_id']);

    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("is", $userId, $productId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Producto eliminado del carrito."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al eliminar producto: " . $stmt->error]);
    }
    $stmt->close();
}

/**
 * Actualiza la cantidad de un producto específico en el carrito.
 * @param mysqli $conn
 * @param array $data Contiene 'user_id', 'product_id', 'new_quantity'.
 */
function updateCartItemQuantity($conn, $data) {
    $userId = $conn->real_escape_string($data['user_id']);
    $productId = $conn->real_escape_string($data['product_id']);
    $newQuantity = intval($data['new_quantity']);

    if ($newQuantity <= 0) {
        // Si la nueva cantidad es 0 o menos, eliminar el ítem
        removeCartItem($conn, ['user_id' => $userId, 'product_id' => $productId]);
        return;
    }

    $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iis", $newQuantity, $userId, $productId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Cantidad actualizada."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar cantidad: " . $stmt->error]);
    }
    $stmt->close();
}

/**
 * Vacía el carrito de un usuario.
 * @param mysqli $conn
 * @param int $userId
 */
function clearCart($conn, $userId) {
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Carrito vaciado."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al vaciar carrito: " . $stmt->error]);
    }
    $stmt->close();
}

?>


