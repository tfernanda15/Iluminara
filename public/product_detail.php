<?php
// public/product_detail.php
// Página de detalles de producto. Muestra información detallada de un producto específico.
ini_set('display_errors', 0); 
ini_set('display_startup_errors', 0); 
error_reporting(E_ALL); 

session_start();

require_once '../app/config/db_config.php';
require_once '../app/models/Product.php';

$product = null;
$product_id = null;
$error_message = '';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    $conn = connectDB(); 

    if ($conn === null) {
        $error_message = "No se pudo conectar a la base de datos para cargar los detalles del producto. Verifica tu configuración en 'db_config.php'.";
    } else {
        $product = Product::getProductById($conn, $product_id);
        
        if ($product === null) {
            $error_message = "El producto solicitado (ID: " . htmlspecialchars($product_id) . ") no fue encontrado o hubo un error en la consulta.";
        }
        $conn->close();
    }
} else {
    $error_message = "No se ha especificado un ID de producto válido en la URL.";
}

function formatPrice($price) {
    return '$ ' . number_format($price, 0, ',', '.') . ' COP';
}

function ucfirst_php($string) {
    if (empty($string)) return '';
    // Asegurarse de que el resto de la cadena esté en minúsculas para una capitalización consistente y manejo UTF-8
    return mb_strtoupper(mb_substr(mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1'), 0, 1)) . mb_substr(mb_convert_encoding(mb_strtolower($string), 'UTF-8', 'ISO-8859-1'), 1);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iluminara - <?php echo $product ? htmlspecialchars($product['name']) : 'Producto No Encontrado'; ?></title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>"> 
    <link rel="stylesheet" href="css/product_detail_style.css?v=<?php echo time(); ?>"> 

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700&family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div id="messageContainer" class="message-container"></div>

    <header class="minimal-header">
        <div class="container minimal-header-content">
            <div class="logo">
                <a href="index.php">
                    <img src="images/logo.png" alt="Logo Iluminara" class="logo-img">
                    <h1>Iluminara</h1>
                </a>
            </div>
            <nav class="minimal-nav">
                <a href="index.php#productGrid" class="btn btn-secondary back-to-products-btn">
                    <i class="fas fa-arrow-left"></i> Volver a Productos
                </a>
            </nav>
        </div>
    </header>

    <main class="product-detail-page-main">
        <div class="container product-detail-layout">
            <nav class="breadcrumbs">
                <a href="index.php">Inicio</a> &gt; <a href="index.php#productGrid">Productos</a> &gt; <span><?php echo $product ? htmlspecialchars($product['name']) : 'Detalles de Producto'; ?></span>
            </nav>

            <?php if ($product): ?>
                <div class="product-header">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                </div>

                <div class="product-content-area">
                    <div class="product-images">
                        <img id="mainProductImage" src="<?php echo htmlspecialchars($product['image_url'] ? $product['image_url'] : 'https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image'); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             class="main-detail-img-page"
                             onerror="this.onerror=null;this.src='https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';this.classList.add('error-image');">
                        <div class="image-thumbnails" id="imageThumbnails">
                            <img src="<?php echo htmlspecialchars($product['image_url'] ? $product['image_url'] : 'https://placehold.co/80x80/8A2BE2/5C2E7E?text=No+Image'); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?> - Miniatura" 
                                 class="thumbnail-img-page active"
                                 onerror="this.onerror=null;this.src='https://placehold.co/80x80/8A2BE2/5C2E7E?text=No+Image';this.classList.add('error-image');">
                            <!-- Miniaturas de ejemplo para probar, si no hay más imágenes reales -->
                        </div>
                    </div>

                    <div class="product-info-area">
                        <p class="product-price-page"><?php echo formatPrice($product['price']); ?></p>
                        
                        <div class="product-options-page">
                            <div class="quantity-selector-page">
                                <label for="productQuantity">Cantidad:</label>
                                <div class="quantity-controls-page">
                                    <button class="quantity-decrement-btn-page">-</button>
                                    <input type="number" id="productQuantity" value="1" min="1" max="<?php echo htmlspecialchars($product['stock'] > 0 ? $product['stock'] : 1); ?>" <?php echo $product['stock'] === 0 ? 'disabled' : ''; ?> class="quantity-input-page">
                                    <button class="quantity-increment-btn-page">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="product-actions-page">
                            <button class="btn btn-primary add-to-cart-btn-page"
                                data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
                                data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-product-price="<?php echo htmlspecialchars($product['price']); ?>"
                                data-product-image="<?php echo htmlspecialchars($product['image_url'] ?: 'https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image'); ?>"
                                data-product-stock="<?php echo htmlspecialchars($product['stock']); ?>"
                                <?php echo $product['stock'] === 0 ? 'disabled' : ''; ?>
                                >Añadir al Carrito</button>
                            <button class="btn btn-tertiary add-to-wishlist-btn-page"
                                data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
                                data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-product-price="<?php echo htmlspecialchars($product['price']); ?>"
                                data-product-image="<?php echo htmlspecialchars($product['image_url'] ?: 'https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image'); ?>"
                                >❤️ Añadir a Wishlist</button>
                        </div>

                        <div class="product-tabs-page">
                            <button class="tab-btn-page active" id="descriptionTabBtnPage">Descripción</button>
                            <button class="tab-btn-page" id="detailsTabBtnPage">Detalles</button>
                        </div>

                        <div class="product-tab-content-page">
                            <div id="descriptionPanelPage" class="tab-panel-page active">
                                <p><?php echo htmlspecialchars($product['description']); ?></p>
                            </div>
                            <div id="detailsPanelPage" class="tab-panel-page">
                                <h3>Especificaciones Técnicas:</h3>
                                <ul>
                                    <li>**Categoría:** <?php echo htmlspecialchars(ucfirst_php($product['category'] ?? 'N/A')); ?></li>
                                    <li>**Subcategoría:** <?php echo htmlspecialchars(ucfirst_php($product['subcategory'] ?? 'N/A')); ?></li>
                                    <li>**Color:** <?php echo htmlspecialchars(ucfirst_php($product['color'] ?? 'N/A')); ?></li>
                                    <li>**Material:** <?php echo htmlspecialchars(ucfirst_php($product['material'] ?? 'N/A')); ?></li>
                                    <li>**Stock Disponible:** <?php echo htmlspecialchars($product['stock']); ?> unidades</li>
                                    <li>**Dimensiones:** <?php echo htmlspecialchars($product['dimensions'] ?? 'No especificado'); ?></li>
                                    <li>**Peso:** <?php echo htmlspecialchars($product['weight'] ?? 'No especificado'); ?></li>
                                    <li>**En Oferta:** <?php echo ($product['is_on_sale'] ?? false) ? 'Sí' : 'No'; ?></li>
                                    <li>**Garantía:** 1 año contra defectos de fabricación</li>
                                </ul>
                                <p>Producto de calidad superior.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- SECCIÓN DE RESEÑAS FUERA DE LAS PESTAÑAS -->
                <section class="product-reviews-section">
                    <h3 class="section-title">Lo que dicen nuestros clientes</h3>
                    <!-- Contenedor para el slider de reseñas existentes -->
                    <div id="reviewsListContainer" class="reviews-slider">
                        <p class="placeholder-text">Cargando reseñas...</p>
                    </div>

                    <!-- ACORDEÓN PARA EL FORMULARIO DE RESEÑAS -->
                    <div class="review-form-accordion">
                        <button class="accordion-header" id="toggleReviewFormBtn">
                            <i class="fas fa-plus-circle accordion-icon"></i> Deja una Reseña
                        </button>
                        <div class="accordion-content" id="reviewFormAccordionContent">
                            <form id="reviewForm" class="review-form-layout">
                                <input type="hidden" id="reviewProductId" value="<?php echo htmlspecialchars($product['id'] ?? ''); ?>">
                                
                                <div class="form-group-flex">
                                    <label for="reviewUsername">Tu Nombre:</label>
                                    <input type="text" id="reviewUsername" required placeholder="Ej: Luna Mágica">
                                </div>
                                
                                <div class="form-group-flex rating-group-flex">
                                    <label for="reviewRating">Puntuación:</label>
                                    <div class="rating-stars" id="reviewRatingStars">
                                        <i class="far fa-star" data-rating="1"></i>
                                        <i class="far fa-star" data-rating="2"></i>
                                        <i class="far fa-star" data-rating="3"></i>
                                        <i class="far fa-star" data-rating="4"></i>
                                        <i class="far fa-star" data-rating="5"></i>
                                        <input type="hidden" id="reviewRating" value="0" required>
                                    </div>
                                </div>
                                
                                <div class="form-group-full-width">
                                    <label for="reviewComment">Comentario (opcional):</label>
                                    <textarea id="reviewComment" rows="4" maxlength="500" placeholder="¿Qué te pareció el producto?"></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary submit-review-btn">Enviar Reseña</button>
                            </form>
                        </div>
                    </div>
                </section>

            <?php else: ?>
                <div class="error-message-product-detail">
                    <h2>Oops...</h2>
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                    <p>Por favor, <a href="index.php" class="btn btn-primary">vuelve a la página principal</a> para explorar otros productos.</p>
                </div>
            <?php endif; ?>

            <section class="related-products-page">
                <h3 class="section-title">Productos que te podrían interesar</h3>
                <div class="product-grid-page" id="suggestedProductsGrid">
                    <p class="placeholder-text">Cargando sugerencias...</p>
                </div>
            </section>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 Iluminara. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- MODALES EXISTENTES -->
    <div id="confirmLogoutModal" class="modal">
        <div class="modal-content small-modal-content">
            <span class="close-button" id="closeConfirmLogoutModalBtn">&times;</span>
            <div class="modal-body">
                <h2>¿Cerrar Sesión?</h2>
                <p>¿Estás seguro de que deseas cerrar tu sesión?</p>
                <div class="modal-actions">
                    <button id="cancelLogoutBtn" class="btn btn-secondary cancel-logout-btn">Cancelar</button>
                    <button id="confirmLogoutBtn" class="btn btn-primary">Sí, Cerrar Sesión</button>
                </div>
            </div>
        </div>
    </div>

    <div id="authModal" class="modal">
        <div class="modal-content auth-modal-content">
            <span class="close-button" id="closeAuthModalBtn">&times;</span>
            <div class="modal-header auth-tabs">
                <button id="loginTabBtn" class="tab-btn active">Iniciar Sesión</button>
                <button id="registerTabBtn" class="tab-btn">Registrarse</button>
            </div>
            <div class="modal-body">
                <div id="loginPanel" class="auth-panel active scrollable-modal-body">
                    <h2>Inicia Sesión en Iluminara</h2>
                    <form id="loginForm">
                        <label for="login-email">Email:</label>
                        <input type="email" id="login-email" name="email" required>
                        <label for="login-password">Contraseña:</label>
                        <input type="password" id="login-password" name="password" required>
                        <button type="submit" class="btn btn-primary">Entrar</button>
                        <p class="switch-link">¿No tienes cuenta? <a href="#" id="showRegister">Regístrate aquí</a></p>
                    </form>
                </div>
                <div id="registerPanel" class="auth-panel scrollable-modal-body">
                    <h2>Crea tu Cuenta Mágica</h2>
                    <form id="registerForm">
                        <label for="register-name">Nombre de Usuario:</label>
                        <input type="text" id="register-name" name="username" required>
                        <label for="register-email">Email:</label>
                        <input type="email" id="register-email" name="email" required>
                        <label for="register-password">Contraseña:</label>
                        <input type="password" id="register-password" name="password" required>
                        <label for="register-confirm-password">Confirmar Contraseña:</label>
                        <input type="password" id="register-confirm-password" name="confirm_password" required>
                        <button type="submit" class="btn btn-primary">Registrarse</button>
                        <p class="switch-link">¿Ya tienes cuenta? <a href="#" id="showLogin">Inicia Sesión aquí</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="cartConfirmationModal" class="modal">
        <div class="modal-content small-modal-content">
            <span class="close-button" id="closeCartConfirmationModalBtn">&times;</span>
            <div class="modal-body">
                <h2 style="text-align: center; color: var(--color-main-dark);">¡Producto Añadido al Carrito!</h2>
                <div style="text-align: center; margin: 20px 0;">
                    <img id="addedProductImage" src="images/product-placeholder.jpg" alt="Producto Añadido" class="modal-product-img">
                    <h4 id="addedProductName" style="margin-bottom: 5px; color: var(--color-main-dark);">Nombre Producto Añadido</h4>
                    <p style="font-weight: bold; color: var(--color-accent-pink);">Precio: <span id="addedProductPrice"></span></p>
                    <p style="color: var(--color-text-dark);">Cantidad: <span id="addedProductQuantity">1</span></p>
                </div>
                <div class="modal-actions">
                    <button id="continueShoppingBtn" class="btn btn-secondary" style="margin-right: 10px;">Continuar Comprando</button>
                    <a href="#" id="viewCartBtnFromConfirmation" class="btn btn-primary">Ver Carrito</a> 
                </div>
            </div>
        </div>
    </div>

    <div id="cartModal" class="modal">
        <div class="modal-content large-modal-content">
            <span class="close-button" id="closeCartModalBtnCart">&times;</span>
            <div class="modal-body">
                <h2>Tu Carrito y Checkout</h2>
                <div class="checkout-steps">
                    <button class="step-btn active" id="cartStepBtn">1. Carrito</button>
                    <button class="step-btn" id="shippingStepBtn">2. Envío</button>
                    <button class="step-btn" id="paymentStepBtn">3. Pago</button>
                </div>

                <div id="cartPanel" class="checkout-panel active">
                    <h3>Productos en tu Carrito</h3>
                    <div id="cartItemsContainer" class="cart-items-container">
                        <p class="placeholder-text">Tu carrito está vacío.</p>
                    </div>
                    <div class="cart-summary">
                        <p>Subtotal: <span id="cartSubtotal">$ 0 COP</span></p>
                        <button id="proceedToShippingBtn" class="btn btn-primary" disabled>Continuar a Envío</button>
                    </div>
                </div>

                <div id="shippingPanel" class="checkout-panel" style="display: none;">
                    <h3>Información de Envío</h3>
                    <form id="shippingForm">
                        <label for="fullName">Nombre Completo:</label>
                        <input type="text" id="fullName" name="fullName" required>
                        <label for="address">Dirección:</label>
                        <input type="text" id="address" name="address" required>
                        <label for="city">Ciudad:</label>
                        <input type="text" id="city" name="city" required>
                        <label for="postalCode">Código Postal:</label>
                        <input type="text" id="postalCode" name="postalCode" required>
                        <label for="country">País:</label>
                        <input type="text" id="country" name="country" value="Colombia" required>
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary back-btn">Volver al Carrito</button>
                            <button type="submit" class="btn btn-primary">Continuar a Pago</button>
                        </div>
                    </form>
                </div>

                <div id="paymentPanel" class="checkout-panel" style="display: none;">
                    <h3>Información de Pago</h3>
                    <form id="paymentGatewayForm">
                        <p>¡Simulación de pago! Tu tarjeta no será cargada.</p>
                        <label for="cardNumber">Número de Tarjeta:</label>
                        <input type="text" id="cardNumber" placeholder="**** **** **** ****" required>
                        <label for="expiryDate">Fecha de Vencimiento (MM/AA):</label>
                        <input type="text" id="expiryDate" placeholder="MM/AA" required>
                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" placeholder="***" required>
                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary back-btn">Volver a Envío</button>
                            <button type="button" id="confirmPaymentBtn" class="btn btn-primary">Confirmar Pedido</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div id="myAccountModal" class="modal">
        <div class="modal-content large-modal-content">
            <span class="close-button" id="closeMyAccountModalBtn">&times;</span>
            <div class="modal-header auth-tabs">
                <button id="profileTabBtn" class="tab-btn active">Mi Perfil</button>
                <button id="addressesTabBtn" class="tab-btn">Mis Direcciones</button>
                <button id="ordersTabBtn" class="tab-btn">Mis Pedidos</button>
                <button id="wishlistTabBtn" class="tab-btn">Mi Wishlist</button>
            </div>
            <div class="modal-body scrollable-modal-body">
                <div id="profilePanel" class="account-panel active">
                    <h2>Detalles de Mi Perfil</h2>
                    <form id="profileForm">
                        <label for="profile-username">Nombre de Usuario:</label>
                        <input type="text" id="profile-username" name="username" readonly value="Nombre de Usuario">
                        <label for="profile-email">Email:</label>
                        <input type="email" id="profile-email" name="email" readonly value="usuario@ejemplo.com">
                        <label for="profile-phone">Teléfono:</label>
                        <input type="tel" id="profile-phone" name="phone" placeholder="Sin registrar" readonly value="">

                        <button type="button" id="editProfileBtn" class="btn btn-secondary">Editar Perfil</button>
                        <button type="submit" id="saveProfileBtn" class="btn btn-primary" style="display: none;">Guardar Cambios</button>
                        <button type="button" id="cancelEditProfileBtn" class="btn btn-secondary" style="display: none;">Cancelar</button>
                    </form>
                </div>

                <div id="addressesPanel" class="account-panel">
                    <h2>Mis Direcciones</h2>
                    <div id="addressList">
                        <p style="text-align: center; color: var(--color-gray-dark);">Aún no tienes direcciones registradas.</p>
                    </div>
                    <button id="addAddressBtn" class="btn btn-primary">Añadir Nueva Dirección</button>
                </div>

                <div id="ordersPanel" class="account-panel">
                    <h2>Historial de Pedidos</h2>
                    <div id="orderList">
                        <p style="text-align: center; color: var(--color-gray-dark);">Aún no has realizado ningún pedido.</p>
                    </div>
                </div>

                <div id="wishlistPanel" class="account-panel">
                    <h2>Mi Wishlist Mágica</h2>
                    <div class="wishlist-boards-section">
                        <h3>Mis Tableros</h3>
                        <div id="wishlistBoardsContainer" class="board-grid">
                            
                        </div>
                        <button id="createBoardBtn" class="btn btn-primary create-board-btn">&#x2795; Crear Nuevo Tablero</button>
                    </div>

                    <div class="wishlist-items-section" style="display: none;">
                        <h3 id="currentBoardName"></h3>
                        <button id="backToBoardsBtn" class="btn btn-secondary back-btn">&larr; Volver a Tableros</button>
                        <div id="currentBoardItems" class="product-grid">
                            <p class="placeholder-text">Selecciona un tablero para ver sus ítems.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="createBoardModal" class="modal">
        <div class="modal-content small-modal-content">
            <span class="close-button" id="closeCreateBoardModalBtn">&times;</span>
            <div class="modal-body">
                <h2>Crear Nuevo Tablero</h2>
                <form id="createBoardForm">
                    <label for="newBoardName">Nombre del Tablero:</label>
                    <input type="text" id="newBoardName" name="boardName" required maxlength="255">
                    <label for="newBoardDescription">Descripción (Opcional):</label>
                    <textarea id="newBoardDescription" name="boardDescription" rows="3" maxlength="500"></textarea>
                    <div class="modal-actions">
                        <button type="button" id="cancelCreateBoardBtn" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Tablero</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="addProductToWishlistModal" class="modal">
        <div class="modal-content small-modal-content">
            <span class="close-button" id="closeAddProductToWishlistModalBtn">&times;</span>
            <div class="modal-body">
                <h2>Añadir a Wishlist</h2>
                <img id="wishlistProductImage" src="images/product-placeholder.jpg" alt="Producto" class="modal-product-img">
                <h4 id="wishlistProductName"></h4>
                <p id="wishlistProductPrice"></p>

                <form id="addProductToWishlistForm">
                    <input type="hidden" id="wishlistProductId" name="productId" value="">
                    <input type="hidden" id="wishlistProductActualPrice" name="productPrice" value="">
                    <input type="hidden" id="wishlistProductActualImage" name="productImage" value="">

                    <label for="boardSelect">Selecciona un Tablero:</label>
                    <select id="boardSelect" name="boardId" required>
                        <option value="">Cargando tableros...</option>
                    </select>

                    <label for="itemNotes">Notas (opcional):</label>
                    <textarea id="itemNotes" name="notes" rows="3" maxlength="255" placeholder="Ej: 'Para mi cumpleaños', 'Regalo para X'"></textarea>

                    <div class="modal-actions">
                        <button type="button" id="cancelAddToWishlistBtn" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Añadir al Tablero</button>
                    </div>
                </form>
                <p class="create-board-prompt">¿No encuentras un tablero? <a href="#" id="openCreateBoardLink">Crea uno nuevo aquí</a>.</p>
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script src="js/main.js?v=<?php echo time(); ?>"></script> 
    <script src="js/auth.js?v=<?php echo time(); ?>"></script> 
    <script src="js/cart.js?v=<?php echo time(); ?>"></script> 
    <script src="js/wishlist.js?v=<?php echo time(); ?>"></script> 
    <script src="js/product_detail_page.js?v=<?php echo time(); ?>"></script>

    <?php if ($product): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mainProductId = <?php echo json_encode($product['id'] ?? null); ?>;
            const mainProductCategory = <?php echo json_encode($product['category'] ?? null); ?>;
            const mainProductSubcategory = <?php echo json_encode($product['subcategory'] ?? null); ?>;

            if (window.initProductDetailPage) {
                window.initProductDetailPage(mainProductId, mainProductCategory, mainProductSubcategory);
                // Cargar las reseñas tan pronto como la página cargue, ya que ahora están en una sección independiente.
                window.loadReviews(mainProductId); 
            } else {
                console.error("product_detail_page.js no cargado o initProductDetailPage no es global.");
                document.getElementById('suggestedProductsGrid').innerHTML = '<p class="placeholder-text" style="color: red;">Error: No se pueden cargar sugerencias (función JS no disponible).</p>';
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>







