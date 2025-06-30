<?php
// app/models/Product.php
// Este archivo contiene la clase Product, que representa el modelo de datos para los productos.
// Se encarga de la interacción con la tabla `products` en la base de datos.

class Product {
    private $conn; // Propiedad para almacenar la conexión a la base de datos

    // Constructor: Se llama automáticamente cuando se crea una nueva instancia de la clase Product.
    // Recibe el objeto de conexión a la base de datos.
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Obtiene todos los productos de la tabla `products`.
     * Este es un método estático, se puede llamar directamente con `Product::getAllProducts($conn)`.
     * @param mysqli $db_connection Objeto de conexión a la base de datos.
     * @return array|null Un array de objetos (productos) si la consulta es exitosa, o null si hay un error.
     */
    public static function getAllProducts($db_connection) {
        $products = []; // Inicializa un array vacío para almacenar los productos.
        
        // Consulta SQL para seleccionar todos los campos de la tabla 'products'.
        $sql = "SELECT id, name, description, price, category, image_url, stock FROM products ORDER BY id ASC";
        $result = $db_connection->query($sql); // Ejecuta la consulta.

        if ($result) {
            // Si la consulta fue exitosa y hay filas devueltas
            if ($result->num_rows > 0) {
                // Itera sobre cada fila de resultados y la añade al array de productos.
                while($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }
            $result->free(); // Libera la memoria asociada al conjunto de resultados.
            return $products; // Retorna el array de productos.
        } else {
            // Si hubo un error en la consulta SQL, registra el error y retorna null.
            error_log("Error al obtener todos los productos: " . $db_connection->error);
            return null;
        }
    }

    /**
     * Obtiene un producto específico por su ID.
     * Este es un método estático, se puede llamar directamente con `Product::getProductById($conn, $id)`.
     * @param mysqli $db_connection Objeto de conexión a la base de datos.
     * @param int $id El ID numérico del producto a buscar.
     * @return array|null Un array asociativo con los datos del producto si se encuentra, o null si no se encuentra o hay un error.
     */
    public static function getProductById($db_connection, $id) {
        // Prepara la consulta SQL para evitar inyección SQL (usando sentencias preparadas).
        $sql = "SELECT id, name, description, price, category, image_url, stock FROM products WHERE id = ?";
        $stmt = $db_connection->prepare($sql); // Prepara la sentencia.

        // Verifica si la preparación de la sentencia falló.
        if ($stmt === false) {
            error_log("Error al preparar la consulta getProductById: " . $db_connection->error);
            return null;
        }

        // Vincula el parámetro ID a la sentencia preparada. "i" indica que $id es un entero.
        $stmt->bind_param("i", $id);

        // Ejecuta la sentencia preparada.
        $stmt->execute();
        
        // Obtiene el resultado de la consulta.
        $result = $stmt->get_result();

        // Si se encontraron filas
        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc(); // Obtiene la primera (y única) fila como un array asociativo.
            $result->free(); // Libera el conjunto de resultados.
            $stmt->close(); // Cierra la sentencia preparada.
            return $product; // Retorna los datos del producto.
        } else {
            // Si no se encontró el producto o hubo un error durante la ejecución.
            // Registra el error para depuración.
            error_log("Producto con ID $id no encontrado o error en la ejecución de la consulta: " . $stmt->error);
            $stmt->close(); // Cierra la sentencia.
            return null;
        }
    }

    // Aquí se podrían añadir más métodos para interactuar con productos (añadir, actualizar, eliminar, etc.)
}
?>

