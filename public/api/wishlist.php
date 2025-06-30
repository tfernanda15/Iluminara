<?php
// public/api/wishlist.php
// API para manejar las operaciones de la Wishlist (tableros y productos).

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-User-ID"); 
header("Content-Type: application/json; charset=UTF-8");

// Incluir el archivo de conexión a la base de datos
require_once '../../app/config/db_config.php';
require_once '../../app/models/Wishlist.php'; 

// Manejar solicitudes OPTIONS (pre-vuelo CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$response = ['success' => false, 'message' => ''];
$conn = connectDB();

if ($conn === null) {
    $response['message'] = 'Error interno del servidor: No se pudo conectar a la base de datos.';
    error_log('Wishlist API Error: No se pudo conectar a la base de datos.');
    http_response_code(500);
    echo json_encode($response);
    exit();
}

// =========================================================================
// Obtener user_id como STRING, no forzar a int
$user_id = $_SERVER['HTTP_X_USER_ID'] ?? null; 
$data_raw = file_get_contents('php://input');
$data_parsed = json_decode($data_raw, true);

if (!$user_id && isset($data_parsed['user_id'])) {
    $user_id = $data_parsed['user_id'];
}
if (!$user_id && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
}

// Fallback final: si no se encontró un user_id, usa uno temporal de sesión.
if (!$user_id) {
    if (session_status() == PHP_SESSION_NONE) { 
        session_start();
    }
    if (!isset($_SESSION['user_id_temp_wishlist'])) {
        $_SESSION['user_id_temp_wishlist'] = 'anon_' . uniqid(); 
    }
    $user_id = $_SESSION['user_id_temp_wishlist'];
    error_log("wishlist.php: ADVERTENCIA: User ID no pudo ser determinado. Usando ID de fallback de sesión: " . $user_id);
} else {
    error_log("wishlist.php: User ID determinado: " . $user_id . " (desde " . (isset($_SERVER['HTTP_X_USER_ID']) ? 'HEADER' : (isset($data_parsed['user_id']) ? 'BODY' : 'GET_PARAM')) . ")");
}
// =========================================================================

$request_method = $_SERVER['REQUEST_METHOD'];
$data_received = []; 

if ($request_method === 'POST') {
    $data_received = $data_parsed; // Ya decodificado antes
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['message'] = 'Error al decodificar JSON de la solicitud POST.';
        error_log('Wishlist API Error: JSON Decode Error: ' . json_last_error_msg());
        http_response_code(400);
        echo json_encode($response);
        $conn->close();
        exit();
    }
} else if ($request_method === 'GET') {
    $data_received = $_GET; 
}

$action = $data_received['action'] ?? '';
error_log("Wishlist API: Request received. Action: {$action}, User ID: {$user_id}");


try {
    switch ($action) {
        case 'get_boards':
            error_log("Wishlist API: get_boards - Procesando solicitud.");
            $boards = Wishlist::getWishlistBoards($conn, $user_id); 
            if ($boards !== false) {
                $response['success'] = true;
                $response['message'] = 'Tableros cargados exitosamente.';
                $response['boards'] = $boards;
                error_log("Wishlist API: get_boards success. Found " . count($boards) . " boards for user {$user_id}.");
            } else {
                $response['message'] = 'No se pudieron cargar los tableros.';
                error_log("Wishlist API: get_boards failed for user {$user_id}.");
            }
            break;

        case 'create_board':
            $name = $data_received['name'] ?? null;
            $description = $data_received['description'] ?? null;
            error_log("Wishlist API: create_board - Received name: " . ($name ?? 'NULL') . ", description: " . ($description ?? 'NULL'));

            if ($name && $user_id) {
                $board_id = Wishlist::createWishlistBoard($conn, $user_id, $name, $description); 
                if ($board_id) {
                    $response['success'] = true;
                    $response['message'] = 'Tablero creado exitosamente.';
                    $response['board_id'] = $board_id;
                    error_log("Wishlist API: create_board success. Board ID: {$board_id} for user {$user_id}.");
                } else {
                    $response['message'] = 'No se pudo crear el tablero. Puede que el usuario no exista o haya un error en DB.';
                    error_log("Wishlist API: create_board failed for user {$user_id}. Name: {$name}. DB Error: " . $conn->error);
                }
            } else {
                $response['message'] = 'Nombre de tablero o ID de usuario no proporcionado.';
                http_response_code(400);
                error_log("Wishlist API: create_board bad request. Name: " . ($name ?? 'null') . ", User ID: " . ($user_id ?? 'null') . ".");
            }
            break;

        case 'delete_board':
            $board_id = $data_received['board_id'] ?? null;
            error_log("Wishlist API: delete_board - Received board_id: " . ($board_id ?? 'NULL'));

            if ($board_id && $user_id) {
                if (Wishlist::deleteWishlistBoard($conn, intval($board_id), $user_id)) { 
                    $response['success'] = true;
                    $response['message'] = 'Tablero y sus ítems eliminados exitosamente.';
                    error_log("Wishlist API: delete_board success. Board ID: {$board_id} for user {$user_id}.");
                } else {
                    $response['message'] = 'No se pudo eliminar el tablero o no tienes permisos.';
                    error_log("Wishlist API: delete_board failed. Board ID: {$board_id} for user {$user_id}. DB Error: " . $conn->error);
                }
            } else {
                $response['message'] = 'ID de tablero o ID de usuario no proporcionado.';
                http_response_code(400);
                error_log("Wishlist API: delete_board bad request. Board ID: " . ($board_id ?? 'null') . ", User ID: " . ($user_id ?? 'null') . ".");
            }
            break;

        case 'add_item':
            $board_id = $data_received['board_id'] ?? null; 
            $product_id = $data_received['product_id'] ?? null; 
            $notes = $data_received['notes'] ?? null;
            $product_name = $data_received['product_name'] ?? null; 
            $product_price = $data_received['product_price'] ?? null;
            $product_image = $data_received['product_image'] ?? null;

            error_log("Wishlist API: add_item - Received: board_id=" . ($board_id ?? 'NULL') . ", product_id=" . ($product_id ?? 'NULL') . ", notes=" . ($notes ?? 'NULL') . ", product_name=" . ($product_name ?? 'NULL') . ", product_price=" . ($product_price ?? 'NULL') . ", product_image=" . ($product_image ?? 'NULL') . ", user_id=" . ($user_id ?? 'NULL'));

            if (!$board_id || !$product_id || !$user_id || !$product_name || !is_numeric($product_price) || !$product_image) {
                $response['message'] = 'Datos incompletos o inválidos para añadir el producto a la wishlist. (board_id, product_id, user_id, product_name, product_price, product_image son requeridos)';
                http_response_code(400);
                error_log("Wishlist API: add_item bad request. Missing data. Board ID: " . ($board_id ?? 'null') . ", Product ID: " . ($product_id ?? 'null') . ", User ID: " . ($user_id ?? 'null') . ", Product Name: " . ($product_name ?? 'null') . ", Product Price: " . ($product_price ?? 'null') . ", Product Image: " . ($product_image ?? 'null') . ".");
                echo json_encode($response);
                $conn->close();
                exit();
            }

            $board_id_int = intval($board_id); 
            $product_id_int = intval($product_id);

            // Validar que product_price sea un número antes de pasarlo, y que product_image no esté vacío
            if (!is_numeric($product_price)) {
                $response['message'] = 'El precio del producto no es válido.';
                http_response_code(400);
                error_log("Wishlist API: add_item bad request. Invalid product_price: " . ($product_price ?? 'null'));
                echo json_encode($response);
                $conn->close();
                exit();
            }

            $item_id = Wishlist::addWishlistItem(
                $conn, $board_id_int, $product_id_int, $user_id, $notes,
                $product_name, floatval($product_price), $product_image 
            );
            if ($item_id) {
                $response['success'] = true;
                $response['message'] = 'Producto añadido a la wishlist.';
                $response['item_id'] = $item_id;
                error_log("Wishlist API: add_item success. Item ID: {$item_id} for board {$board_id}, product {$product_id}, user {$user_id}.");
            } else {
                $response['message'] = 'No se pudo añadir el producto a la wishlist. Puede que ya exista en este tablero o que el tablero no te pertenezca.';
                error_log("Wishlist API: add_item failed for product {$product_id} to board {$board_id}, user {$user_id}. DB Error: " . $conn->error);
            }
            break;

        case 'get_board_items':
            $board_id = $data_received['board_id'] ?? null;
            error_log("Wishlist API: get_board_items - Received board_id: " . ($board_id ?? 'NULL') . ", user_id: " . ($user_id ?? 'NULL')); // Nuevo log CRÍTICO

            if (!$board_id || !$user_id) {
                $response['message'] = 'ID de tablero o ID de usuario no proporcionado.';
                http_response_code(400);
                error_log("Wishlist API: get_board_items bad request. Board ID: " . ($board_id ?? 'null') . ", User ID: " . ($user_id ?? 'null') . ".");
                echo json_encode($response);
                $conn->close();
                exit();
            }

            $board_id_int = intval($board_id);
            $items = Wishlist::getWishlistItemsByBoard($conn, $board_id_int, $user_id); 
            if ($items !== false) {
                $response['success'] = true;
                $response['message'] = 'Ítems del tablero cargados exitosamente.';
                $response['items'] = $items;
                error_log("Wishlist API: get_board_items success. Found " . count($items) . " items for board {$board_id}, user {$user_id}.");
            } else {
                $response['message'] = 'No se pudieron cargar los ítems del tablero o el tablero no te pertenece.';
                error_log("Wishlist API: get_board_items failed for board {$board_id}, user {$user_id}. DB Error: " . $conn->error);
            }
            break;

        case 'remove_item': 
            $board_id = $data_received['board_id'] ?? null;
            $product_id = $data_received['product_id'] ?? null;
            error_log("Wishlist API: remove_item - Received board_id: " . ($board_id ?? 'NULL') . ", product_id: " . ($product_id ?? 'NULL') . ", user_id: " . ($user_id ?? 'NULL'));

            if ($board_id && $product_id && $user_id) { 
                $board_id_int = intval($board_id);
                $product_id_int = intval($product_id);
                if (Wishlist::removeWishlistItem($conn, $board_id_int, $product_id_int, $user_id)) { 
                    $response['success'] = true;
                    $response['message'] = 'Producto eliminado de la wishlist.';
                    error_log("Wishlist API: remove_item success. Product {$product_id} from board {$board_id} for user {$user_id}.");
                } else {
                    $response['message'] = 'No se pudo eliminar el producto de la wishlist o no fue encontrado.';
                    error_log("Wishlist API: remove_item failed. Product {$product_id} from board {$board_id} for user {$user_id}. DB Error: " . $conn->error);
                }
            } else {
                $response['message'] = 'ID de tablero o ID de producto no proporcionado.';
                http_response_code(400);
                error_log("Wishlist API: remove_item bad request. Data: " . json_encode($data_received));
            }
            break;

        default:
            $response['message'] = 'Acción de wishlist no válida.';
            http_response_code(400);
            error_log("Wishlist API: Invalid action: {$action}.");
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Excepción en el servidor: ' . $e->getMessage();
    http_response_code(500);
    error_log('Wishlist API Exception: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
} finally {
    $conn->close();
}

echo json_encode($response);
exit();

