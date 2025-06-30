<?php
// public/api/products.php

// Configurar encabezados CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Incluir el archivo de conexión a la base de datos
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
        if (isset($_GET['id'])) {
            // Obtener detalles de un solo producto
            $productId = intval($_GET['id']);
            getProductById($conn, $productId);
        } elseif (isset($_GET['action']) && $_GET['action'] === 'get_suggestions') {
            // NUEVO: Obtener productos sugeridos con lógica de prioridad
            $baseProductId = isset($_GET['base_product_id']) ? intval($_GET['base_product_id']) : 0;
            $category = $_GET['category'] ?? '';
            $subcategory = $_GET['subcategory'] ?? '';
            getSuggestedProducts($conn, $baseProductId, $category, $subcategory);
        } else {
            // Obtener todos los productos con filtros avanzados (como antes)
            $searchTerm = $_GET['search_term'] ?? ''; 
            $categories = $_GET['categories'] ?? ''; 
            $subcategories = $_GET['subcategories'] ?? ''; 
            $minPrice = $_GET['min_price'] ?? null; 
            $maxPrice = $_GET['max_price'] ?? null;
            $colors = $_GET['colors'] ?? ''; 
            $materials = $_GET['materials'] ?? ''; 
            $sortBy = $_GET['sort_order'] ?? 'recommended'; 

            getProducts($conn, $searchTerm, $categories, $subcategories, $minPrice, $maxPrice, $colors, $materials, $sortBy);
        }
        break;
    default:
        http_response_code(405); 
        echo json_encode(["success" => false, "message" => "Método no permitido."]);
        break;
}

$conn->close();

/**
 * Obtiene un producto por su ID.
 * @param mysqli $conn
 * @param int $id
 */
function getProductById($conn, $id) {
    // Selecciona todos los campos necesarios para la página de detalle
    $sql = "SELECT id, name, description, price, stock, image_url, dimensions, material, weight, color, category, subcategory, is_on_sale FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log("getProductById: Error al preparar la consulta: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Error al preparar la consulta: " . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        echo json_encode(["success" => true, "product" => $product]);
    } else {
        echo json_encode(["success" => false, "message" => "Producto no encontrado."]);
    }
    $stmt->close();
}

/**
 * NUEVA LÓGICA: Obtiene productos sugeridos de forma escalonada.
 * 1. Intenta por subcategoría y categoría.
 * 2. Si no hay suficientes, intenta solo por categoría.
 * 3. Si aún no hay suficientes, muestra productos populares generales.
 * @param mysqli $conn
 * @param int $baseProductId ID del producto actual que se está viendo.
 * @param string $category Categoría del producto base.
 * @param string $subcategory Subcategoría del producto base.
 */
function getSuggestedProducts($conn, $baseProductId, $category, $subcategory) {
    $products = [];
    $limit = 4; // Número deseado de sugerencias

    // --- Intento 1: Sugerencias por la misma SUB-CATEGORÍA y CATEGORÍA ---
    if (!empty($category) && !empty($subcategory)) {
        $sql1 = "SELECT id, name, description, price, stock, image_url, category, subcategory, color, material, is_on_sale FROM products WHERE category = ? AND subcategory = ? AND id != ? ORDER BY popularity_score DESC, RAND() LIMIT ?";
        $stmt1 = $conn->prepare($sql1);
        if ($stmt1) {
            $stmt1->bind_param("ssii", $category, $subcategory, $baseProductId, $limit);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
            while ($row = $result1->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt1->close();
            // Si obtuvimos suficientes productos específicos, los devolvemos.
            if (count($products) >= $limit) {
                echo json_encode(["success" => true, "products" => $products]);
                return;
            }
        } else {
            error_log("getSuggestedProducts: Error al preparar Intento 1 (subcat): " . $conn->error);
        }
    }

    // --- Intento 2: Sugerencias solo por la misma CATEGORÍA (si el Intento 1 no fue suficiente o no aplicaba) ---
    // Reiniciamos $products por si el Intento 1 devolvió menos de 4
    $products = []; 
    if (!empty($category)) {
        $sql2 = "SELECT id, name, description, price, stock, image_url, category, subcategory, color, material, is_on_sale FROM products WHERE category = ? AND id != ? ORDER BY popularity_score DESC, RAND() LIMIT ?";
        $stmt2 = $conn->prepare($sql2);
        if ($stmt2) {
            $stmt2->bind_param("sii", $category, $baseProductId, $limit);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            while ($row = $result2->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt2->close();
            // Si obtuvimos suficientes productos por categoría, los devolvemos.
            if (count($products) >= $limit) {
                echo json_encode(["success" => true, "products" => $products]);
                return;
            }
        } else {
            error_log("getSuggestedProducts: Error al preparar Intento 2 (cat): " . $conn->error);
        }
    }

    // --- Intento 3 (Fallback General): Sugerencias POPULARES/ALEATORIAS (si los intentos anteriores no fueron suficientes) ---
    // Reiniciamos $products
    $products = []; 
    $sqlFallback = "SELECT id, name, description, price, stock, image_url, category, subcategory, color, material, is_on_sale FROM products WHERE id != ? ORDER BY popularity_score DESC, RAND() LIMIT ?";
    $stmtFallback = $conn->prepare($sqlFallback);
    if ($stmtFallback) {
        $stmtFallback->bind_param("ii", $baseProductId, $limit);
        $stmtFallback->execute();
        $resultFallback = $stmtFallback->get_result();
        while ($row = $resultFallback->fetch_assoc()) {
            $products[] = $row;
        }
        $stmtFallback->close();
    } else {
        error_log("getSuggestedProducts: Error al preparar Intento 3 (fallback): " . $conn->error);
    }

    // Devolvemos los productos recolectados (serán los del intento más relevante que haya dado resultados,
    // o el fallback si los anteriores no dieron suficientes).
    echo json_encode(["success" => true, "products" => $products]);
}


/**
 * Obtiene todos los productos con opciones de filtro y ordenamiento.
 * @param mysqli $conn
 * @param string $searchTerm Término de búsqueda unificado.
 * @param string $categories String de categorías separadas por comas.
 * @param string $subcategories String de subcategorías separadas por comas.
 * @param float $minPrice Precio mínimo.
 * @param float $maxPrice Precio máximo.
 * @param string $colors String de colores separados por comas.
 * @param string $materials String de materiales separados por comas.
 * @param string $sortBy Criterio de ordenamiento.
 */
function getProducts($conn, $searchTerm, $categories, $subcategories, $minPrice, $maxPrice, $colors, $materials, $sortBy) {
    $products = [];
    $sql = "SELECT id, name, description, price, stock, image_url, category, subcategory, color, material, is_on_sale, created_at, popularity_score FROM products WHERE 1=1";
    $params = [];
    $types = "";
    
    $whereClauses = [];

    if (!empty($searchTerm)) {
        $searchTermSql = '%' . $conn->real_escape_string($searchTerm) . '%';
        $whereClauses[] = "(name LIKE ? OR description LIKE ? OR category LIKE ? OR subcategory LIKE ? OR color LIKE ? OR material LIKE ?)";
        $params[] = $searchTermSql;
        $params[] = $searchTermSql;
        $params[] = $searchTermSql;
        $params[] = $searchTermSql;
        $params[] = $searchTermSql;
        $params[] = $searchTermSql;
        $types .= "ssssss"; 
    }

    if (!empty($categories)) {
        $categoryArray = explode(',', $categories);
        $placeholders = implode(',', array_fill(0, count($categoryArray), '?'));
        $whereClauses[] = "category IN ($placeholders)";
        foreach ($categoryArray as $cat) {
            $params[] = trim($cat);
            $types .= "s";
        }
    }

    if (!empty($subcategories)) {
        $subcategoryArray = explode(',', $subcategories);
        $placeholders = implode(',', array_fill(0, count($subcategoryArray), '?'));
        $whereClauses[] = "subcategory IN ($placeholders)";
        foreach ($subcategoryArray as $subcat) {
            $params[] = trim($subcat);
            $types .= "s";
        }
    }

    if ($minPrice !== null && is_numeric($minPrice)) {
        $whereClauses[] = "price >= ?";
        $params[] = (float)$minPrice;
        $types .= "d";
    }
    if ($maxPrice !== null && is_numeric($maxPrice)) {
        $whereClauses[] = "price <= ?";
        $params[] = (float)$maxPrice;
        $types .= "d";
    }

    if (!empty($colors)) {
        $colorArray = explode(',', $colors);
        $placeholders = implode(',', array_fill(0, count($colorArray), '?'));
        $whereClauses[] = "color IN ($placeholders)";
        foreach ($colorArray as $color) {
            $params[] = trim($color);
            $types .= "s";
        }
    }

    if (!empty($materials)) {
        $materialArray = explode(',', $materials);
        $placeholders = implode(',', array_fill(0, count($materialArray), '?'));
        $whereClauses[] = "material IN ($placeholders)";
        foreach ($materialArray as $material) {
            $params[] = trim($material);
            $types .= "s";
        }
    }

    if (!empty($whereClauses)) {
        $sql .= " AND " . implode(" AND ", $whereClauses);
    }

    $orderByClause = "";

    switch ($sortBy) {
        case 'price_asc':
            $orderByClause = " ORDER BY price ASC";
            break;
        case 'price_desc':
            $orderByClause = " ORDER BY price DESC";
            break;
        case 'name_asc':
            $orderByClause = " ORDER BY name ASC";
            break;
        case 'name_desc':
            $orderByClause = " ORDER BY name DESC";
            break;
        case 'newest':
            $orderByClause = " ORDER BY created_at DESC"; 
            break;
        case 'on_sale':
            if (!in_array("is_on_sale = 1", $whereClauses)) { 
                 $sql .= " AND is_on_sale = 1"; 
            }
            $orderByClause = " ORDER BY price ASC"; 
            break;
        case 'popular':
            $orderByClause = " ORDER BY popularity_score DESC"; 
            break;
        case 'recommended':
        default:
            $orderByClause = " ORDER BY id DESC"; 
            break;
    }

    $sql .= $orderByClause; 

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("getProducts: Error al preparar la consulta: " . $conn->error . " SQL: " . $sql);
        echo json_encode(["success" => false, "message" => "Error al preparar la consulta de productos: " . $conn->error]);
        return;
    }

    if (!empty($params) && !empty($types)) { 
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode(["success" => true, "products" => $products]);
    $stmt->close();
}
?>



