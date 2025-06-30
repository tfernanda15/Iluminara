<?php
// public/test_db.php
// Script temporal para probar la conexión a la base de datos y la obtención de un producto específico.
// Esto ayuda a diagnosticar problemas de conexión o de datos.

// Habilita la visualización de todos los errores PHP.
// ¡MUY ÚTIL para depuración, pero desactívalo en un entorno de producción por seguridad!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye el archivo de configuración de la base de datos.
// La ruta es relativa desde 'public/test_db.php' hasta 'app/config/db_config.php'.
require_once '../app/config/db_config.php';
// Incluye el modelo Product.
require_once '../app/models/Product.php';

echo "<h1>Probando conexión a la base de datos y obtención de productos...</h1>";

// Intenta conectar a la base de datos.
$conn = connectDB();

// Verifica si la conexión fue exitosa.
if ($conn) {
    echo "<p style='color: green; font-weight: bold;'>¡Conexión a la base de datos exitosa!</p>";

    // --- Parte 1: Intentar obtener UN producto de ejemplo (ej. ID 1) ---
    $productId = 1; // ID del producto que intentamos buscar. Debería ser el primer producto insertado.
    echo "<h2>Probando obtención de producto con ID: " . htmlspecialchars($productId) . "</h2>";
    
    // Llama al método estático `getProductById` del modelo `Product`.
    $product = Product::getProductById($conn, $productId);

    if ($product) {
        echo "<p style='color: blue;'>Producto con ID " . htmlspecialchars($productId) . " encontrado:</p>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . htmlspecialchars($product['id']) . "</li>";
        echo "<li><strong>Nombre:</strong> " . htmlspecialchars($product['name']) . "</li>";
        echo "<li><strong>Precio:</strong> " . htmlspecialchars(number_format($product['price'], 2, ',', '.')) . " COP</li>";
        echo "<li><strong>Categoría:</strong> " . htmlspecialchars($product['category']) . "</li>";
        echo "<li><strong>Stock:</strong> " . htmlspecialchars($product['stock']) . "</li>";
        echo "<li><strong>Descripción (fragmento):</strong> " . htmlspecialchars(substr($product['description'], 0, 100)) . "...</li>";
        echo "<li><strong>URL de Imagen:</strong> " . htmlspecialchars($product['image_url']) . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>No se encontró un producto con ID " . htmlspecialchars($productId) . " o hubo un error en la consulta.</p>";
        echo "<p>Esto podría indicar que:</p>";
        echo "<ul>";
        echo "<li>No hay un producto con ese ID en la tabla `products`.</li>";
        echo "<li>La tabla `products` está vacía.</li>";
        echo "<li>Hay un problema con la columna `id` (por ejemplo, no es INT o AUTO_INCREMENT).</li>";
        echo "</ul>";
    }

    echo "<hr>";

    // --- Parte 2: Intentar obtener TODOS los productos ---
    echo "<h2>Probando obtención de TODOS los productos</h2>";
    $allProducts = Product::getAllProducts($conn);

    if ($allProducts !== null && count($allProducts) > 0) {
        echo "<p style='color: blue;'>Se encontraron " . count($allProducts) . " productos:</p>";
        echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
        echo "<thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Categoría</th><th>Stock</th></tr></thead>";
        echo "<tbody>";
        foreach ($allProducts as $p) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($p['id']) . "</td>";
            echo "<td>" . htmlspecialchars($p['name']) . "</td>";
            echo "<td>" . htmlspecialchars(number_format($p['price'], 2, ',', '.')) . "</td>";
            echo "<td>" . htmlspecialchars($p['category']) . "</td>";
            echo "<td>" . htmlspecialchars($p['stock']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } elseif ($allProducts !== null && count($allProducts) === 0) {
        echo "<p style='color: orange;'>La tabla `products` está vacía. No se encontraron productos.</p>";
    } else {
        echo "<p style='color: red;'>Error al obtener todos los productos desde el modelo.</p>";
    }

    // Cierra la conexión a la base de datos al finalizar.
    $conn->close();
} else {
    // Si la conexión a la base de datos falló, muestra un mensaje de error claro.
    echo "<p style='color: red; font-weight: bold;'>Error al conectar a la base de datos.</p>";
    echo "<p>Por favor, revisa los valores en <code>app/config/db_config.php</code>:</p>";
    echo "<ul>";
    echo "<li><code>DB_SERVER</code> (ej. 'localhost')</li>";
    echo "<li><code>DB_USERNAME</code> (ej. 'root')</li>";
    echo "<li><code>DB_PASSWORD</code> (tu contraseña de MySQL, a menudo vacía si usas XAMPP/WAMP por defecto)</li>";
    echo "<li><code>DB_NAME</code> (el nombre exacto de la base de datos que creaste, ej. 'iluminara_db')</li>";
    echo "</ul>";
    echo "<p>Asegúrate también de que tu servidor MySQL (y Apache/PHP) está en funcionamiento.</p>";
}
?>
