<?php
// app/models/Wishlist.php
// Este archivo define la clase Wishlist, que encapsula la lógica de acceso a datos para los tableros y ítems de la wishlist.

class Wishlist {

    /**
     * Obtiene todos los tableros de wishlist de un usuario.
     * @param mysqli $conn La conexión a la base de datos.
     * @param string $user_id El ID del usuario (puede ser un string 'anon_xyz').
     * @return array|false Un array de tableros o false en caso de error.
     */
    public static function getWishlistBoards($conn, $user_id) {
        error_log("Wishlist Model: getWishlistBoards para user_id: " . $user_id);
        $boards = [];
        $stmt = $conn->prepare("
            SELECT wb.id, wb.name, wb.description, COUNT(wi.id) AS item_count
            FROM wishlist_boards wb
            LEFT JOIN wishlist_items wi ON wb.id = wi.board_id AND wb.user_id = wi.user_id 
            WHERE wb.user_id = ?
            GROUP BY wb.id, wb.name, wb.description
            ORDER BY wb.created_at DESC
        ");
        
        if ($stmt === false) {
            error_log("Wishlist Model: Error al preparar getWishlistBoards: " . $conn->error);
            return false;
        }
        $stmt->bind_param("s", $user_id); // user_id es STRING
        
        if (!$stmt->execute()) {
            error_log("Wishlist Model: Error al ejecutar getWishlistBoards: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $boards[] = $row;
        }
        $stmt->close();
        error_log("Wishlist Model: getWishlistBoards encontró " . count($boards) . " tableros.");
        return $boards;
    }

    /**
     * Crea un nuevo tablero de wishlist para un usuario.
     * @param mysqli $conn La conexión a la base de datos.
     * @param string $user_id El ID del usuario.
     * @param string $name El nombre del tablero.
     * @param string|null $description La descripción del tablero (opcional).
     * @return int|false El ID del nuevo tablero o false en caso de error.
     */
    public static function createWishlistBoard($conn, $user_id, $name, $description) {
        error_log("Wishlist Model: createWishlistBoard para user_id: " . $user_id . ", name: " . $name);
        $stmt = $conn->prepare("INSERT INTO wishlist_boards (user_id, name, description) VALUES (?, ?, ?)");
        
        if ($stmt === false) {
            error_log("Wishlist Model: Error al preparar createWishlistBoard: " . $conn->error);
            return false;
        }
        $stmt->bind_param("sss", $user_id, $name, $description); // user_id es STRING
        
        if ($stmt->execute()) {
            $board_id = $conn->insert_id;
            $stmt->close();
            error_log("Wishlist Model: Tablero creado con ID: " . $board_id);
            return $board_id;
        } else {
            error_log("Wishlist Model: Error al ejecutar createWishlistBoard: " . $stmt->error . " (Código: " . $stmt->errno . ")");
            $stmt->close();
            return false;
        }
    }

    /**
     * Elimina un tablero de wishlist y todos sus ítems asociados.
     * @param mysqli $conn La conexión a la base de datos.
     * @param int $board_id El ID del tablero a eliminar.
     * @param string $user_id El ID del usuario para verificar la propiedad.
     * @return bool True si se eliminó, false en caso contrario.
     */
    public static function deleteWishlistBoard($conn, $board_id, $user_id) {
        error_log("Wishlist Model: deleteWishlistBoard para board_id: " . $board_id . ", user_id: " . $user_id);
        
        // Primero, verificar que el tablero pertenece al usuario
        $stmt_check_ownership = $conn->prepare("SELECT id FROM wishlist_boards WHERE id = ? AND user_id = ?");
        if ($stmt_check_ownership === false) {
            error_log("Wishlist Model: Error al preparar verificación de propiedad del tablero: " . $conn->error);
            return false;
        }
        $stmt_check_ownership->bind_param("is", $board_id, $user_id);
        $stmt_check_ownership->execute();
        $stmt_check_ownership->store_result();
        if ($stmt_check_ownership->num_rows === 0) {
            error_log("Wishlist Model: deleteWishlistBoard - Acceso denegado o tablero no encontrado para user_id: " . $user_id);
            $stmt_check_ownership->close();
            return false;
        }
        $stmt_check_ownership->close();

        // Iniciar transacción para asegurar atomicidad
        $conn->begin_transaction();
        try {
            // Eliminar ítems del tablero
            $stmt_items = $conn->prepare("DELETE FROM wishlist_items WHERE board_id = ? AND user_id = ?");
            if ($stmt_items === false) {
                throw new Exception("Error al preparar la eliminación de ítems de wishlist: " . $conn->error);
            }
            $stmt_items->bind_param("is", $board_id, $user_id);
            $stmt_items->execute();
            $stmt_items->close();

            // Eliminar el tablero
            $stmt_board = $conn->prepare("DELETE FROM wishlist_boards WHERE id = ? AND user_id = ?");
            if ($stmt_board === false) {
                throw new Exception("Error al preparar la eliminación del tablero de wishlist: " . $conn->error);
            }
            $stmt_board->bind_param("is", $board_id, $user_id);
            $success = $stmt_board->execute();
            $stmt_board->close();

            if ($success) {
                $conn->commit();
                error_log("Wishlist Model: Tablero " . $board_id . " y sus ítems eliminados exitosamente.");
                return true;
            } else {
                $conn->rollback();
                error_log("Wishlist Model: Fallo al eliminar el tablero " . $board_id . ".");
                return false;
            }
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Wishlist Model: Excepción durante deleteWishlistBoard: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Añade un ítem (producto) a un tablero de wishlist.
     * @param mysqli $conn La conexión a la base de datos.
     * @param int $board_id El ID del tablero.
     * @param int $product_id El ID del producto.
     * @param string $user_id El ID del usuario para verificación.
     * @param string|null $notes Notas adicionales para el ítem.
     * @param string $product_name El nombre del producto.
     * @param float $product_price El precio del producto.
     * @param string $product_image_url La URL de la imagen del producto.
     * @return int|false El ID del ítem añadido o false en caso de error.
     */
    public static function addWishlistItem($conn, $board_id, $product_id, $user_id, $notes, $product_name, $product_price, $product_image_url) {
        error_log("Wishlist Model: addWishlistItem - board: {$board_id}, product: {$product_id}, user: {$user_id}, name: {$product_name}, price: {$product_price}, image: {$product_image_url}");
        
        // Verificar que el tablero pertenece al usuario
        if (!self::checkBoardOwnership($conn, $board_id, $user_id)) {
            error_log("Wishlist Model: addWishlistItem - Acceso denegado. El tablero " . $board_id . " no pertenece al user_id " . $user_id);
            return false;
        }

        // Verificar si el ítem ya existe en este tablero para evitar duplicados
        if (self::itemExistsInBoard($conn, $board_id, $product_id, $user_id)) {
            error_log("Wishlist Model: addWishlistItem - Producto " . $product_id . " ya existe en el tablero " . $board_id . " para el usuario " . $user_id);
            return false; // Indicar que ya existe
        }

        $stmt = $conn->prepare("INSERT INTO wishlist_items (board_id, product_id, user_id, notes, product_name, product_price, product_image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            error_log("Wishlist Model: Error al preparar addWishlistItem: " . $conn->error);
            return false;
        }
        // Tipos: i (int), i (int), s (string), s (string), s (string), d (double), s (string)
        $stmt->bind_param("iisssss", $board_id, $product_id, $user_id, $notes, $product_name, $product_price, $product_image_url);
        
        if ($stmt->execute()) {
            $item_id = $conn->insert_id;
            $stmt->close();
            error_log("Wishlist Model: Ítem de wishlist añadido con ID: " . $item_id);
            return $item_id;
        } else {
            error_log("Wishlist Model: Error al ejecutar addWishlistItem: " . $stmt->error . " (Código: " . $stmt->errno . ")");
            $stmt->close();
            return false;
        }
    }

    /**
     * Obtiene los ítems de un tablero de wishlist específico.
     * @param mysqli $conn La conexión a la base de datos.
     * @param int $board_id El ID del tablero.
     * @param string $user_id El ID del usuario para verificar la propiedad.
     * @return array|false Un array de ítems o false en caso de error.
     */
    public static function getWishlistItemsByBoard($conn, $board_id, $user_id) {
        error_log("Wishlist Model: getWishlistItemsByBoard para board_id: " . $board_id . ", user_id: " . $user_id);

        // Primero, verificar que el tablero pertenece al usuario
        if (!self::checkBoardOwnership($conn, $board_id, $user_id)) {
            error_log("Wishlist Model: getWishlistItemsByBoard - Acceso denegado. El tablero " . $board_id . " no pertenece al user_id " . $user_id);
            return false;
        }

        $items = [];
        // Se seleccionan las columnas directamente de wishlist_items
        $stmt = $conn->prepare("
            SELECT wi.id, wi.product_id, wi.notes, wi.product_name, wi.product_price, wi.product_image_url 
            FROM wishlist_items wi
            WHERE wi.board_id = ? AND wi.user_id = ?
            ORDER BY wi.created_at DESC
        ");
        
        if ($stmt === false) {
            error_log("Wishlist Model: Error al preparar getWishlistItemsByBoard: " . $conn->error);
            return false;
        }
        $stmt->bind_param("is", $board_id, $user_id); // board_id es INT, user_id es STRING
        
        if (!$stmt->execute()) {
            error_log("Wishlist Model: Error al ejecutar getWishlistItemsByBoard: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'id' => $row['id'], 
                'notes' => $row['notes'],
                'product' => [
                    'id' => $row['product_id'],
                    'name' => $row['product_name'],
                    'price' => (float)$row['product_price'],
                    'imageUrl' => $row['product_image_url'] ? $row['product_image_url'] : 'https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image', // Fallback
                ]
            ];
        }
        $stmt->close();
        error_log("Wishlist Model: getWishlistItemsByBoard encontró " . count($items) . " ítems.");
        return $items;
    }

    /**
     * Elimina un ítem de un tablero de wishlist.
     * @param mysqli $conn La conexión a la base de datos.
     * @param int $board_id El ID del tablero.
     * @param int $product_id El ID del producto a eliminar.
     * @param string $user_id El ID del usuario para verificar la propiedad del tablero.
     * @return bool True si se eliminó, false en caso contrario.
     */
    public static function removeWishlistItem($conn, $board_id, $product_id, $user_id) {
        error_log("Wishlist Model: removeWishlistItem - board: {$board_id}, product: {$product_id}, user: {$user_id}");
        
        // Verificar que el tablero pertenece al usuario
        if (!self::checkBoardOwnership($conn, $board_id, $user_id)) {
            error_log("Wishlist Model: removeWishlistItem - Acceso denegado. El tablero " . $board_id . " no pertenece al user_id " . $user_id);
            return false;
        }

        $stmt = $conn->prepare("DELETE FROM wishlist_items WHERE board_id = ? AND product_id = ? AND user_id = ?");
        
        if ($stmt === false) {
            error_log("Wishlist Model: Error al preparar removeWishlistItem: " . $conn->error);
            return false;
        }
        $stmt->bind_param("iis", $board_id, $product_id, $user_id);
        
        if ($stmt->execute()) {
            $success = ($stmt->affected_rows > 0);
            $stmt->close();
            if ($success) {
                error_log("Wishlist Model: Ítem de wishlist eliminado (product: {$product_id} from board: {$board_id}).");
            } else {
                error_log("Wishlist Model: Ítem de wishlist no encontrado para eliminar.");
            }
            return $success;
        } else {
            error_log("Wishlist Model: Error al ejecutar removeWishlistItem: " . $stmt->error . " (Código: " . $stmt->errno . ")");
            $stmt->close();
            return false;
        }
    }

    /**
     * Verifica si un ítem (producto) ya existe en un tablero específico para un usuario.
     * @param mysqli $conn La conexión a la base de datos.
     * @param int $board_id El ID del tablero.
     * @param int $product_id El ID del producto.
     * @param string $user_id El ID del usuario.
     * @return bool True si el ítem existe, false en caso contrario.
     */
    public static function itemExistsInBoard($conn, $board_id, $product_id, $user_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM wishlist_items WHERE board_id = ? AND product_id = ? AND user_id = ?");
        if ($stmt === false) {
            error_log("Wishlist Model: Error al preparar itemExistsInBoard: " . $conn->error);
            return false;
        }
        $stmt->bind_param("iis", $board_id, $product_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    /**
     * Función auxiliar para verificar la propiedad de un tablero.
     * @param mysqli $conn La conexión a la base de datos.
     * @param int $board_id El ID del tablero.
     * @param string $user_id El ID del usuario.
     * @return bool True si el usuario es propietario del tablero, false en caso contrario.
     */
    private static function checkBoardOwnership($conn, $board_id, $user_id) {
        $stmt = $conn->prepare("SELECT id FROM wishlist_boards WHERE id = ? AND user_id = ?");
        if ($stmt === false) {
            error_log("Wishlist Model: Error al preparar checkBoardOwnership: " . $conn->error);
            return false;
        }
        $stmt->bind_param("is", $board_id, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $is_owner = ($stmt->num_rows > 0);
        $stmt->close();
        return $is_owner;
    }
}
?>

    

