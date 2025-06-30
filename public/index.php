<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iluminara | Belleza Cósmica y Encantada</title>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <!-- Enlace a los estilos CSS principales. El parámetro v=... evita problemas de caché del navegador. -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>"> 
    <!-- Enlaces a Google Fonts para una tipografía estética -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700&family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Almendra+SC&display=swap" rel="stylesheet">
    <!-- Font Awesome para iconos visuales -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>


    <header class="main-header">

    
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="images/logo.png" alt="Logo Iluminara" class="logo-img">
                    <h1>Iluminara</h1>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="nosotros.php">Nosotros</a></li>
                    <li><a href="contactanos.php">Contacto</a></li>
                </ul>
            </nav>
            <div class="header-icons">

                <!-- Botón para Iniciar Sesión / Registrarse -->
                <a href="#" id="loginBtn" class="icon-btn" title="Iniciar Sesión / Registrarse">
                    <i class="fas fa-user"></i>
                </a>
                <!-- Contenedor del perfil de usuario (inicialmente oculto) -->
                <div id="userProfile" class="user-profile" style="display: none;">
                    <span id="displayUsername">Usuario</span>
                    <span class="dropdown-arrow">&#9660;</span>
                    <div id="userDropdownMenu" class="user-dropdown-menu">
                        <ul>
                            <li><a href="#" id="myAccountLink"><i class="fas fa-user-circle"></i> Mi Cuenta</a></li>
                            <li class="separator"></li>
                            <li><a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</li>
                        </ul>
                    </div>
                </div>

                <!-- Botón del carrito de compras -->
                <a href="#" id="cartBtn" class="icon-btn" title="Ver Carrito">
                    <i class="fas fa-shopping-cart"></i> <span class="cart-count">0</span>
                </a>
            </div>
        </div>
    </header>

    <section class="hero-section">
    <div class="corazones">
        <i class="fas fa-heart heart blue"></i>
        <i class="fas fa-heart heart pink"></i>
        <i class="fas fa-heart heart red"></i>
        <i class="fas fa-heart heart yellow"></i>
        <i class="fas fa-heart heart green"></i>
    </div>
        <div class="container">
            <div class="hero-content" data-aos="fade-up" data-aos-duration="1500">

                <h2>Descubre tu Brillo Interior</h2>
                <p>Cosméticos encantados y bisutería mística para realzar tu esencia única.</p>
                <a href="#productGrid" class="btn btn-primary">Explora la Magia</a>
            </div>
        </div>
        <script>
document.addEventListener('DOMContentLoaded', () => {
const container = document.querySelector('.corazones');

function createHeart() {
const heart = document.createElement('i');
heart.classList.add('fas', 'fa-heart', 'heart');

// Genera un color aleatorio para cada corazón
const colors = ['red', 'pink', 'blue', 'yellow', 'green'];
const randomColor = colors[Math.floor(Math.random() * colors.length)];
heart.classList.add(randomColor);

// Posición aleatoria a lo largo del ancho de la pantalla
heart.style.left = Math.random() * 100 + 'vw';

// Tamaño aleatorio para los corazones
heart.style.fontSize = Math.random() * 30 + 20 + 'px';

// Velocidad aleatoria
heart.style.animationDuration = Math.random() * 5 + 5 + 's';

container.appendChild(heart);

// Remover el corazón cuando la animación termine
setTimeout(() => {
heart.remove();
}, 10000); // 10 segundos

}

// Crear corazones continuamente cada 500ms
setInterval(createHeart, 500);
});

src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"


</script>

    </section>

    <section class="slider-container">
    <div class="slider-track">
            <div class="slider-slide" style="background: linear-gradient(pink, rgb(255, 122, 168));">
                <img class="primera" src="images/makeupgirl.png" alt="">
                <p id="primer">" Descubre el poder cósmico <br> de tu belleza con nuestro <br> maquillaje místico."</p>
            </div>
            <div class="slider-slide" style="background: linear-gradient(rgb(106, 106, 159), rgb(123, 136, 255));">
            <img class="segunda" src="images/joyasgirl.png" alt="">
                <p id="segunda">"Encuentra tu amuleto mágico. Nuestras joyas atraen la buena fortuna."</p>
            </div>
            <div class="slider-slide" style="background: linear-gradient(purple,rgb(65, 75, 117), rgb(29, 36, 67));">
                <p id="tercera">"Encuentra la fragancia perfecta. <br>Crea momentos inolvidables con nuestros perfumes."</p>
                <img class="tercera" src="images/perfumgirl.png" alt="">
            </div>
            <div class="slider-slide" style="background: linear-gradient(rgb(72, 64, 79),rgb(150, 100, 134), rgb(79, 64, 93));">
            <img class="cuarta" src="images/murcelago.png" alt="">
                <p id="cuarta">"El toque ideal. <br>Combina nuestros accesorios <br>con tus mejores outfits."</p>
            </div>
        </div>
        <button class="slider-button prev">&#10094;</button>
        <button class="slider-button next">&#10095;</button>

        <script>
            // script.js
document.addEventListener('DOMContentLoaded', () => {
    const sliderTrack = document.querySelector('.slider-track');
    const slides = document.querySelectorAll('.slider-slide');
    const prevButton = document.querySelector('.slider-button.prev');
    const nextButton = document.querySelector('.slider-button.next');

    let currentIndex = 0;
    const slideWidth = slides[0].clientWidth; // Ancho de un solo slide (100vw)

    // Función para actualizar la posición del slider
    function updateSliderPosition() {
        sliderTrack.style.transform = `translateX(${-currentIndex * slideWidth}px)`;
    }

    // Evento para el botón "Anterior"
    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : slides.length - 1;
        updateSliderPosition();
    });

    // Evento para el botón "Siguiente"
    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
        updateSliderPosition();
    });

    // Opcional: Desplazamiento automático (descomentar para activar)
    /*
    setInterval(() => {
        currentIndex = (currentIndex < slides.length - 1) ? currentIndex + 1 : 0;
        updateSliderPosition();
    }, 5000); // Cambia de slide cada 5 segundos
    */
});
        </script>
    </section>


    <main class="main-content-area">
        <div class="container main-grid">
            <!-- Barra lateral para filtros de productos -->
            <aside class="sidebar-filters">
                <div class="filter-section">
                    <h3><i class="fas fa-filter"></i> Filtrar Productos</h3>
                    <!-- REINCLUIDO: Buscador en el sidebar -->
                    <div class="search-filter-in-sidebar">
                        <input type="text" id="sidebarSearchInput" placeholder="Buscar en filtros...">
                        <button id="sidebarSearchButton" class="icon-btn" title="Buscar"><i class="fas fa-search"></i></button>
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Categoría</h4>
                    <div class="category-filter-group">
                        <!-- Cosméticos -->
                        <div class="category-toggle" data-category="cosmeticos">
                            <label><input type="checkbox" name="category" value="cosmeticos" class="filter-checkbox category-main-checkbox" id="filterCosmeticos"> Cosméticos</label>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="subcategory-group" id="subcategoriesCosmeticos">
                            <label><input type="checkbox" name="subcategory" value="maquillaje" class="filter-checkbox subcategory-checkbox"> Maquillaje</label>
                            <label><input type="checkbox" name="subcategory" value="limpieza_facial" class="filter-checkbox subcategory-checkbox"> Limpieza Facial</label>
                            <label><input type="checkbox" name="subcategory" value="cuidado_capilar" class="filter-checkbox subcategory-checkbox"> Cuidado Capilar</label>
                        </div>
                        
                        <!-- Bisutería -->
                        <div class="category-toggle" data-category="bisuteria">
                            <label><input type="checkbox" name="category" value="bisuteria" class="filter-checkbox category-main-checkbox" id="filterBisuteria"> Bisutería</label>
                            <i class="fas fa-chevron-down toggle-icon"></i>
                        </div>
                        <div class="subcategory-group" id="subcategoriesBisuteria">
                            <label><input type="checkbox" name="subcategory" value="collares" class="filter-checkbox subcategory-checkbox"> Collares</label>
                            <label><input type="checkbox" name="subcategory" value="pulseras" class="filter-checkbox subcategory-checkbox"> Pulseras</label>
                            <label><input type="checkbox" name="subcategory" value="anillos" class="filter-checkbox subcategory-checkbox"> Anillos</label>
                            <label><input type="checkbox" name="subcategory" value="aretes" class="filter-checkbox subcategory-checkbox"> Aretes</label>
                        </div>
                        <!-- Puedes añadir más categorías principales aquí con su respectivo subcategory-group -->
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Precio</h4>
                    <input type="range" id="priceRange" min="0" max="200000" value="200000">
                    <p>Rango: $<span id="minPriceDisplay">0</span> - $<span id="maxPriceDisplay">200.000</span> COP</p>
                </div>

                <!-- NUEVOS FILTROS -->

                <div class="filter-section">
                    <h4>Color</h4>
                    <div class="color-options-group">
                        <!-- Cada label ahora tiene un span para el nombre del color -->
                        <label class="color-circle-label" title="Rojo">
                            <input type="checkbox" name="color" value="red" class="filter-checkbox color-option" style="--color-value: #ff0000;">
                            <span class="color-name-display">Rojo</span>
                        </label>
                        <label class="color-circle-label" title="Azul">
                            <input type="checkbox" name="color" value="blue" class="filter-checkbox color-option" style="--color-value: #0000ff;">
                            <span class="color-name-display">Azul</span>
                        </label>
                        <label class="color-circle-label" title="Verde">
                            <input type="checkbox" name="color" value="green" class="filter-checkbox color-option" style="--color-value: #008000;">
                            <span class="color-name-display">Verde</span>
                        </label>
                        <label class="color-circle-label" title="Blanco">
                            <input type="checkbox" name="color" value="white" class="filter-checkbox color-option" style="--color-value: #ffffff; --color-border: #ccc;">
                            <span class="color-name-display">Blanco</span>
                        </label>
                        <label class="color-circle-label" title="Negro">
                            <input type="checkbox" name="color" value="black" class="filter-checkbox color-option" style="--color-value: #000000;">
                            <span class="color-name-display">Negro</span>
                        </label>
                        <label class="color-circle-label" title="Oro">
                            <input type="checkbox" name="color" value="gold" class="filter-checkbox color-option" style="--color-value: #FFD700;">
                            <span class="color-name-display">Oro</span>
                        </label>
                        <label class="color-circle-label" title="Plata">
                            <input type="checkbox" name="color" value="silver" class="filter-checkbox color-option" style="--color-value: #C0C0C0;">
                            <span class="color-name-display">Plata</span>
                        </label>
                        <label class="color-circle-label" title="Morado">
                            <input type="checkbox" name="color" value="purple" class="filter-checkbox color-option" style="--color-value: #800080;">
                            <span class="color-name-display">Morado</span>
                        </label>
                        <!-- Agrega más colores según tus productos, usando HEX o RGB -->
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Material</h4>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="material" value="metal" class="filter-checkbox"> Metal</label>
                        <label><input type="checkbox" name="material" value="piedra" class="filter-checkbox"> Piedra Natural</label>
                        <label><input type="checkbox" name="material" value="plastico" class="filter-checkbox"> Plástico</label>
                        <label><input type="checkbox" name="material" value="cristal" class="filter-checkbox"> Cristal</label>
                        <!-- Agrega más materiales -->
                    </div>
                </div>
                
                <div class="filter-section">
                    <h4>Ordenar por</h4>
                    <select id="sortOrder">
                        <option value="recommended">Recomendados</option>
                        <option value="price_asc">Precio: Menor a Mayor</option>
                        <option value="price_desc">Precio: Mayor a Menor</option>
                        <option value="name_asc">Nombre: A-Z</option>
                        <option value="name_desc">Nombre: Z-A</option> 
                        <option value="newest">Más Nuevos</option> 
                        <option value="on_sale">En Oferta</option> 
                        <option value="popular">Más Populares</option> 
                    </select>
                </div>

                <div class="filter-actions">
                    <button class="btn btn-primary" id="applyFiltersBtn">Aplicar Filtros</button>
                    <button class="btn btn-tertiary" id="clearFiltersBtn">Limpiar Filtros</button>
                </div>
            </aside>

            <!-- Sección principal para la cuadrícula de productos -->
            <section class="products-section">
                <h3>Productos Destacados</h3>
                <div id="productGrid" class="product-grid">
                    <!-- Los productos se cargarán aquí dinámicamente con JavaScript -->
                    <p class="placeholder-text">Cargando productos, por favor espera...</p>
                </div>
            </section>
        </div>
    </main>

    <!-- =============================================== -->
        <!-- 3. NUEVA SECCIÓN: FRASE DEL DÍA (GATO Y CALDERO) -->
        <!-- =============================================== -->
        <section class="phrase-of-day-section" data-aos="fade-up" data-aos-delay="600">
            <h2>Mensaje del Oráculo Felino</h2>
            <p class="instruction-text">Toca el caldero para que el Gato Místico revele tu frase del día.</p>
            <div class="cauldron-container" id="cauldronContainer">
                <img src="https://placehold.co/300x300/00FFFF/FFFFFF?text=Cauldron" alt="Caldero Mágico" class="cauldron-img" id="cauldronImg">
                <img src="https://placehold.co/150x150/FF00FF/FFFFFF?text=Cat" alt="Gato Místico" class="cat-img" id="catImg">
                <div class="phrase-bubble" id="phraseBubble">
                    <p id="dailyPhrase">...</p>
                </div>
            </div>
            <div id="oracleClickFeedback" class="loading-oracle-text">
                <i class="fas fa-cat"></i> El Gato Místico prepara tu mensaje...
            </div>
        </section>


    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2024 Iluminara. Todos los derechos reservados.</p>
            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Instagram</a>
            </div>
        </div>
    </footer>

    <!-- ============================================== -->
    <!-- MODALES (Ocultos por defecto, visibles con JavaScript) -->
    <!-- Es CRÍTICO que todos estos modales estén en el HTML con sus IDs correctos -->
    <!-- ============================================== -->

    <!-- Modal de Confirmación de Cerrar Sesión -->
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

    <!-- Modal de Autenticación (Login/Registro) -->
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
                    <h4 id="addedProductName" style="margin-bottom: 5px; color: var(--color-main-dark);"></h4>
                    <p id="addedProductPrice" style="font-weight: bold; color: var(--color-accent-pink);"></p>
                    <p style="color: var(--color-text-dark);">Cantidad: <span id="addedProductQuantity"></span></p>
                </div>
                <div class="modal-actions">
                    <button id="continueShoppingBtn" class="btn btn-secondary" style="margin-right: 10px;">Continuar Comprando</button>
                    <a href="#" id="viewCartBtnFromConfirmation" class="btn btn-primary">Ver Carrito</a> 
                </div>
            </div>
        </div>
    </div>

   <!-- Modal del Carrito / Checkout -->
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
                        <!-- Items del carrito se cargarán aquí dinámicamente -->
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
                        <h3>Selecciona tu Método de Pago</h3>
                        
                        <div class="payment-options-grid">
                            <!-- Opción de Tarjeta (seleccionada por defecto) -->
                            <div class="payment-option-card active" data-payment-method="card">
                                <i class="fas fa-credit-card payment-icon"></i>
                                <h4>Pagar con Tarjeta</h4>
                                <p>Débito o Crédito</p>
                            </div>
                            
                            <!-- Opción de Pago Virtual -->
                            <div class="payment-option-card" data-payment-method="virtual">
                                <i class="fas fa-globe payment-icon"></i>
                                <h4>Pago Virtual</h4>
                                <p>PSE, Bancolombia, Nequi, Daviplata</p>
                            </div>

                            <!-- Opción de Pago Físico -->
                            <div class="payment-option-card" data-payment-method="physical">
                                <i class="fas fa-store payment-icon"></i>
                                <h4>Pago Físico</h4>
                                <p>En tienda o Contra Entrega</p>
                            </div>
                        </div>

                        <!-- Contenedores para el contenido de cada método de pago -->
                        <div id="paymentCardDetails" class="payment-details-content active">
                            <div class="card-and-form-container">
                                <!-- Tarjeta Virtual Interactiva -->
                                <div class="credit-card-display">
                                    <div class="card-type-icon">
                                        <!-- Aquí se mostrarán dinámicamente los logos de Visa/Mastercard, etc. -->
                                        <!-- Por ahora, placeholders o se llenará con JS -->
                                        <img src="images/card-visa.png" alt="Visa" class="card-logo visa-logo" style="display: none;">
                                        <img src="images/card-mastercard.png" alt="Mastercard" class="card-logo mastercard-logo" style="display: none;">
                                        <!-- Puedes añadir más logos de tarjetas aquí (ej: American Express) -->
                                    </div>
                                    <img src="images/chip.png" alt="Chip de Tarjeta" class="card-chip-img">                                    <div class="card-number-display" id="displayedCardNumber">#### #### #### ####</div>
                                    <div class="card-holder-info">
                                        <span class="card-label">Titular de la Tarjeta</span>
                                        <span class="card-value" id="displayedCardHolderName">JOHN DOE</span>
                                    </div>
                                    <div class="card-expiry-info">
                                        <span class="card-label">Expira</span>
                                        <span class="card-value" id="displayedExpiryDate">MM/AA</span>
                                    </div>
                                    <!-- CVV no se muestra en la parte frontal de una tarjeta real -->
                                </div>

                                <!-- Formulario de Pago con Tarjeta -->
                                <form id="cardPaymentForm" class="payment-form">
                                    <p class="payment-note">¡Simulación de pago! Tu tarjeta no será cargada.</p>
                                    <label for="cardHolderName">Nombre del Titular:</label>
                                    <input type="text" id="cardHolderName" placeholder="JOHN DOE" required>
                                    <label for="cardNumber">Número de Tarjeta:</label>
                                    <input type="text" id="cardNumber" placeholder="**** **** **** ****" required maxlength="19">
                                    <div class="form-row-compact">
                                        <div class="form-group-compact">
                                            <label for="expiryDate">Fecha de Vencimiento:</label>
                                            <input type="text" id="expiryDate" placeholder="MM/AA" required maxlength="5">
                                        </div>
                                        <div class="form-group-compact">
                                            <label for="cvv">CVV:</label>
                                            <input type="text" id="cvv" placeholder="***" required maxlength="4">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="paymentVirtualDetails" class="payment-details-content">
                            <p class="payment-note">Serás redirigido a nuestra pasarela de pago segura para completar la transacción.</p>
                            
                            <div class="virtual-payment-cosmic-bg">
                                <div class="stars-container"></div> <!-- Contenedor para estrellas animadas -->

                                <div class="payment-method-cards-grid">
                                    <div class="method-card" data-method-id="pse">
                                        <div class="card-header">
                                            <!-- Usa <img> para logos si los tienes, o <i> para Font Awesome -->
                                            <img src="images/logo-pse.png" alt="PSE Logo" class="method-logo" onerror="this.onerror=null; this.src='https://placehold.co/40x40/5D3FD3/FFFFFF?text=PSE';">
                                            <h4>PSE</h4>
                                        </div>
                                        <p class="card-phrase">Conexión mágica</p>
                                    </div>
                                    <div class="method-card" data-method-id="bancolombia">
                                        <div class="card-header">
                                            <img src="images/logo-bancolombia.png" alt="Bancolombia Logo" class="method-logo" onerror="this.onerror=null; this.src='https://placehold.co/40x40/F5DA2A/333333?text=BC';">
                                            <h4>Bancolombia</h4>
                                        </div>
                                        <p class="card-phrase">Transacción estelar</p>
                                    </div>
                                    <div class="method-card" data-method-id="nequi">
                                        <div class="card-header">
                                            <img src="images/logo-nequi.png" alt="Nequi Logo" class="method-logo" onerror="this.onerror=null; this.src='https://placehold.co/40x40/FF5500/FFFFFF?text=NQ';">
                                            <h4>Nequi</h4>
                                        </div>
                                        <p class="card-phrase">Viaje instantáneo</p>
                                    </div>
                                    <div class="method-card" data-method-id="daviplata">
                                        <div class="card-header">
                                            <img src="images/logo-daviplata.png" alt="Daviplata Logo" class="method-logo" onerror="this.onerror=null; this.src='https://placehold.co/40x40/ED1C24/FFFFFF?text=DP';">
                                            <h4>Daviplata</h4>
                                        </div>
                                        <p class="card-phrase">Aterrizaje seguro</p>
                                    </div>
                                </div>
                                
                                <!-- Rocket para la animación dentro del modal -->
                                <img src="images/rocket.png" alt="Cohete despegando" class="modal-rocket" id="modalRocket" onerror="this.onerror=null; this.src='https://placehold.co/80x80/6D28D9/FFFFFF?text=🚀';">

                            </div>

                            <button type="button" id="initiateVirtualPaymentBtn" class="btn btn-primary large-action-btn">Iniciar Viaje Virtual</button>
                        </div>


                        <div id="paymentPhysicalDetails" class="payment-details-content">
                            <p class="payment-note">Puedes pagar tu pedido en efectivo a través de nuestros aliados.</p>
                            
                            <div class="physical-payment-info-card">
                                <h3>Tu Código de Pago</h3>
                                <div class="payment-code-display" id="paymentCodeDisplay">
                                    <span>Genera tu código</span>
                                    <i class="fas fa-barcode code-icon"></i> <!-- Icono de código de barras -->
                                </div>
                                <button type="button" id="generatePaymentCodeBtn" class="btn btn-secondary generate-code-btn">Generar Código de Pago</button>

                                <div class="payment-steps">
                                    <h4>Pasos para Pagar:</h4>
                                    <ul>
                                        <li><i class="fas fa-check-circle"></i> 1. Haz clic en "Generar Código de Pago".</li>
                                        <li><i class="fas fa-print"></i> 2. Anota o toma captura del código.</li>
                                        <li><i class="fas fa-map-marker-alt"></i> 3. Dirígete a cualquier punto de los aliados:</li>
                                        <div class="allies-grid">
                                            <div class="ally-item">
                                                <i class="fas fa-handshake"></i>
                                                <span>Corresponsal Bancario</span>
                                            </div>
                                            <div class="ally-item">
                                                <i class="fas fa-receipt"></i>
                                                <span>Efecty</span>
                                            </div>
                                            <div class="ally-item">
                                                <i class="fas fa-store-alt"></i>
                                                <span>Baloto</span>
                                            </div>
                                            <!-- Puedes añadir más aquí: <div class="ally-item"><i class="fas fa-wallet"></i><span>SuperGIROS</span></div> -->
                                        </div>
                                        <li><i class="fas fa-money-bill-wave"></i> 4. Presenta el código y realiza tu pago.</li>
                                        <li><i class="fas fa-paper-plane"></i> 5. Recibirás una confirmación automática.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>


                        <div class="modal-actions">
                            <button type="button" class="btn btn-secondary back-btn">Volver a Envío</button>
                            <button type="button" id="confirmPaymentBtn" class="btn btn-primary">Confirmar Pedido</button>
                        </div>
                    </div>

            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('openCart') === 'true') {
            history.replaceState(null, '', window.location.pathname); // Limpia el parámetro de la URL

            if (window.openModal && window.loadCurrentCart) {
                console.log('index.php: openCart=true detectado. Abriendo modal del carrito.');
                window.openModal('cartModal'); // Abre el modal del carrito
                window.loadCurrentCart(); // Carga y renderiza los ítems en el modal
                // showCheckoutPanel está definido en cart.js. Llamamos a una función auxiliar
                // que exista para asegurarse de que el panel correcto se muestra.
                // Esta llamada asume que cart.js ya se cargó y showCheckoutPanel es global o se llama dentro de cart.js
                const cartPanelBtn = document.getElementById('cartStepBtn');
                const cartPanel = document.getElementById('cartPanel');
                // IMPORTANTE: showCheckoutPanel está en el scope de DOMContentLoaded en cart.js,
                // por lo que no es global. El propio cart.js maneja la apertura del panel inicial.
                // Aquí, solo abrimos el modal y loadCurrentCart lo renderizará.
            } else {
                console.warn('index.php: No se pudo abrir el modal del carrito al cargar la página. Funciones openModal/loadCurrentCart no disponibles.');
            }
        }
    });
    </script>
    
    <!-- Modal "Mi Cuenta" -->
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
                <!-- Panel Mi Perfil -->
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

                <!-- Panel Mis Direcciones (estático por ahora) -->
                <div id="addressesPanel" class="account-panel">
                    <h2>Mis Direcciones</h2>
                    <div id="addressList">
                        <p style="text-align: center; color: var(--color-gray-dark);">Aún no tienes direcciones registradas.</p>
                        <!-- Ejemplo de dirección estática si quieres una -->
                    </div>
                    <button id="addAddressBtn" class="btn btn-primary">Añadir Nueva Dirección</button>
                </div>

                <!-- Panel Mis Pedidos (estático por ahora) -->
                <div id="ordersPanel" class="account-panel">
                    <h2>Historial de Pedidos</h2>
                    <div id="orderList">
                        <p style="text-align: center; color: var(--color-gray-dark);">Aún no has realizado ningún pedido.</p>
                        <!-- Ejemplo de pedido estático si quieres uno -->
                    </div>
                </div>

                <!-- Panel Mi Wishlist (estático por ahora) -->
                <div id="wishlistPanel" class="account-panel">
                    <h2>Mi Wishlist Mágica</h2>
                    <div class="wishlist-boards-section">
                        <h3>Mis Tableros</h3>
                        <div id="wishlistBoardsContainer" class="board-grid">
                            <!-- Ejemplo de tablero estático -->
                            
                        </div>
                        <button id="createBoardBtn" class="btn btn-primary create-board-btn">&#x2795; Crear Nuevo Tablero</button>
                    </div>

                    <div class="wishlist-items-section" style="display: none;">
                        <h3 id="currentBoardName"></h3>
                        <button id="backToBoardsBtn" class="btn btn-secondary back-btn">&larr; Volver a Tableros</button>
                        <div id="currentBoardItems" class="product-grid">
                            <!-- Los ítems del tablero actual se cargarán aquí -->
                            <p class="placeholder-text">Selecciona un tablero para ver sus ítems.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear Nuevo Tablero -->
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

    <!-- Modal para Añadir Producto a Wishlist -->
    <div id="addProductToWishlistModal" class="modal">

        <!-- Dentro de addProductToWishlistModal -->
<input type="hidden" id="wishlistProductActualImage" name="productImage" value="">

        <div class="modal-content small-modal-content">
            <span class="close-button" id="closeAddProductToWishlistModalBtn">&times;</span>
            <div class="modal-body">
                <h2>Añadir a Wishlist</h2>
                <img id="wishlistProductImage" src="images/product-placeholder.jpg" alt="Producto" class="modal-product-img">
                <h4 id="wishlistProductName">Nombre Producto Ejemplo</h4>
                <p id="wishlistProductPrice">$ 99.000 COP</p>

                <form id="addProductToWishlistForm">
                    <input type="hidden" id="wishlistProductId" name="productId" value="PROD001">
                    <input type="hidden" id="wishlistProductActualPrice" name="productPrice" value="99000">

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

    <!-- Modal de Confirmación de Carrito -->
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

    <!-- ============================================== -->
    <!-- SCRIPTS JAVASCRIPT - ORDEN CRÍTICO -->
    <!-- El ?v=<?php echo time(); ?> es para evitar problemas de caché del navegador. -->
    <!-- Es importante que products.js se cargue ANTES del script en línea que lo utiliza. -->
    <!-- ============================================== -->
    <script src="js/main.js?v=<?php echo time(); ?>"></script> 
    <script src="js/auth.js?v=<?php echo time(); ?>"></script> 
    <script src="js/cart.js?v=<?php echo time(); ?>"></script> 
    <script src="js/wishlist.js?v=<?php echo time(); ?>"></script> 
    <script src="js/products.js?v=<?php echo time(); ?>"></script> 
    <script src="js/product_detail_page.js?v=<?php echo time(); ?>"></script>


    <!-- Script en línea para la conexión del sidebar y búsqueda a products.js -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Obtenemos las referencias a los elementos del sidebar
        // ELIMINADO: searchInput y searchButton del header
        const sidebarSearchInput = document.getElementById('sidebarSearchInput'); // Buscador del sidebar
        const sidebarSearchButton = document.getElementById('sidebarSearchButton'); // Botón del sidebar

        const priceRange = document.getElementById('priceRange');
        const minPriceDisplay = document.getElementById('minPriceDisplay');
        const maxPriceDisplay = document.getElementById('maxPriceDisplay');
        const sortOrderSelect = document.getElementById('sortOrder');
        const applyFiltersBtn = document.getElementById('applyFiltersBtn');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');

        // Elementos de filtros de categoría y subcategoría
        const categoryCheckboxes = document.querySelectorAll('.filter-checkbox[name="category"]');
        const subcategoryCheckboxes = document.querySelectorAll('.filter-checkbox[name="subcategory"]');
        const categoryToggles = document.querySelectorAll('.category-toggle');

        // Nuevos elementos de filtros (Color, Material)
        const colorCheckboxes = document.querySelectorAll('.filter-checkbox[name="color"]');
        const materialCheckboxes = document.querySelectorAll('.filter-checkbox[name="material"]');

        // Helper para formatear precio a COP (ya está en products.js y main.js, pero para seguridad)
        function formatPrice(price) {
            if (isNaN(price) || price === null || price === undefined) return '$ 0 COP';
            return '$ ' + new Intl.NumberFormat('es-CO').format(price) + ' COP';
        }

        // Inicializar el display del rango de precios al cargar
        if (priceRange && maxPriceDisplay) {
            if (minPriceDisplay) minPriceDisplay.textContent = formatPrice(0); 
            maxPriceDisplay.textContent = formatPrice(parseFloat(priceRange.value));
        }

        // Función para recolectar los filtros y llamar a loadProducts de products.js
        window.collectAndApplyFilters = () => {
            console.log('index.php (inline script): Recolectando filtros para products.js...');
            const filters = {};

            // 1. Término de búsqueda: Ahora solo del sidebar
            filters.searchTerm = sidebarSearchInput ? sidebarSearchInput.value.trim() : ''; 

            // 2. Categorías
            filters.categories = Array.from(categoryCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            // 3. Subcategorías
            filters.subcategories = Array.from(subcategoryCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            // 4. Rango de Precio
            filters.minPrice = 0; 
            filters.maxPrice = priceRange ? parseFloat(priceRange.value) : parseFloat(priceRange.max);
            
            // 5. Colores
            filters.colors = Array.from(colorCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            // 6. Materiales
            filters.materials = Array.from(materialCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            // 7. Ordenamiento
            filters.sortOrder = sortOrderSelect ? sortOrderSelect.value : 'recommended';

            // Llamar a la función loadProducts que está en products.js
            if (window.loadProducts) {
                window.loadProducts(filters);
            } else {
                console.error('index.php (inline script): window.loadProducts no está definida. products.js podría no haberse cargado o la función no es global.');
                const productGrid = document.getElementById('productGrid');
                if(productGrid) productGrid.innerHTML = '<p class="placeholder-text" style="color: red;">Error al cargar productos: Script de productos no disponible.</p>';
            }
        };

        // Función para limpiar todos los filtros
        window.clearAndApplyFilters = () => {
            console.log('index.php (inline script): Limpiando filtros...');
            // Limpiar input de búsqueda
            if (sidebarSearchInput) sidebarSearchInput.value = ''; 

            // Desmarcar categorías y subcategorías
            categoryCheckboxes.forEach(cb => cb.checked = false);
            subcategoryCheckboxes.forEach(cb => { 
                if (cb) cb.checked = false; 
            });
            
            // Ocultar subcategorías
            document.querySelectorAll('.subcategory-group').forEach(group => {
                group.style.display = 'none';
            });
            document.querySelectorAll('.category-toggle .toggle-icon').forEach(icon => {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            });

            // Resetear rango de precio
            if (priceRange) {
                priceRange.value = priceRange.max;
                if (maxPriceDisplay) maxPriceDisplay.textContent = formatPrice(parseFloat(priceRange.max));
                if (minPriceDisplay) minPriceDisplay.textContent = formatPrice(0); 
            }

            // Desmarcar nuevos filtros (Color, Material)
            colorCheckboxes.forEach(cb => cb.checked = false);
            materialCheckboxes.forEach(cb => cb.checked = false);

            // Resetear ordenamiento
            if (sortOrderSelect) sortOrderSelect.value = 'recommended';

            // Volver a aplicar los filtros (ahora limpios)
            window.collectAndApplyFilters();
            console.log('index.php (inline script): Filtros limpiados y productos recargados.');
        };

        // ===============================================
        // Listeners de Eventos para el Sidebar y Búsqueda
        // ===============================================

        // ELIMINADO: Listeners del buscador principal del header
        /*
        if (searchButton) {
            searchButton.addEventListener('click', window.collectAndApplyFilters);
        }
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    window.collectAndApplyFilters();
                }
            });
        }
        */

        // Buscador del sidebar (AHORA EL ÚNICO)
        if (sidebarSearchButton) {
            sidebarSearchButton.addEventListener('click', window.collectAndApplyFilters);
        }
        if (sidebarSearchInput) {
            sidebarSearchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    window.collectAndApplyFilters();
                }
            });
        }

        // Rango de Precio Slider
        if (priceRange) {
            priceRange.addEventListener('input', () => {
                if (maxPriceDisplay) maxPriceDisplay.textContent = formatPrice(parseFloat(priceRange.value));
            });
            priceRange.addEventListener('change', window.collectAndApplyFilters); 
        }

        // Ordenamiento
        if (sortOrderSelect) {
            sortOrderSelect.addEventListener('change', window.collectAndApplyFilters);
        }

        // Botones de acción de filtros
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', window.collectAndApplyFilters);
        }
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', window.clearAndApplyFilters);
        }

        // Listeners para checkboxes de Categorías y Subcategorías
        // Toggle para mostrar/ocultar subcategorías
        categoryToggles.forEach(toggle => {
            toggle.addEventListener('click', (event) => {
                if (event.target.tagName === 'INPUT' || event.target.tagName === 'LABEL') {
                    return; 
                }

                const category = toggle.dataset.category;
                const subcategoryGroup = document.getElementById(`subcategories${category.charAt(0).toUpperCase() + category.slice(1)}`);
                const toggleIcon = toggle.querySelector('.toggle-icon');

                if (subcategoryGroup) {
                    if (subcategoryGroup.style.display === 'block') {
                        subcategoryGroup.style.display = 'none';
                        if (toggleIcon) {
                            toggleIcon.classList.remove('fa-chevron-up');
                            toggleIcon.classList.add('fa-chevron-down');
                        }
                    } else {
                        subcategoryGroup.style.display = 'block';
                        if (toggleIcon) {
                            toggleIcon.classList.remove('fa-chevron-down');
                            toggleIcon.classList.add('fa-chevron-up');
                        }
                    }
                }
            });
        });

        // Listen for changes on all filter checkboxes (Category, Subcategory, Color, Material)
        document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (event) => {
                // If a main category is unchecked, uncheck its subcategories
                if (event.target.classList.contains('category-main-checkbox') && !event.target.checked) {
                    const category = event.target.value;
                    const subcategoryGroup = document.getElementById(`subcategories${category.charAt(0).toUpperCase() + category.slice(1)}`);
                    if (subcategoryGroup) {
                        subcategoryGroup.querySelectorAll('.subcategory-checkbox').forEach(subCb => {
                            if (subCb) subCb.checked = false; 
                        });
                    }
                }
                // If a subcategory is checked, make sure its main category is also checked
                else if (event.target.classList.contains('subcategory-checkbox') && event.target.checked) {
                    const parentCategoryDiv = event.target.closest('.subcategory-group').previousElementSibling;
                    if (parentCategoryDiv && parentCategoryDiv.classList.contains('category-toggle')) {
                        const parentCheckbox = parentCategoryDiv.querySelector('.category-main-checkbox');
                        if (parentCheckbox) {
                            parentCheckbox.checked = true;
                        }
                    }
                }
                window.collectAndApplyFilters(); // Apply filters when any checkbox changes
            });
        });

        // Carga inicial de productos al cargar la página
        window.collectAndApplyFilters(); 
    });


        // ===============================================
        // JavaScript para la sección "Frase del Día" (Gato y Caldero)
        // ===============================================
        const cauldronContainer = document.getElementById('cauldronContainer');
        const cauldronImg = document.getElementById('cauldronImg');
        const catImg = document.getElementById('catImg');
        const phraseBubble = document.getElementById('phraseBubble');
        const dailyPhraseElement = document.getElementById('dailyPhrase');
        const oracleClickFeedback = document.getElementById('oracleClickFeedback');

        const mysticalPhrases = [
            "Tu brillo interior es el verdadero tesoro. Deja que ilumine cada paso.",
            "En el silencio del cosmos, tu corazón resuena con la sabiduría ancestral.",
            "La magia no se busca, se crea. Eres el arquitecto de tu propia realidad.",
            "Confía en el proceso. El universo conspira a tu favor.",
            "Cada amanecer es una invitación a florecer. ¡Despliega tus pétalos!",
            "La luz que buscas, ya reside en ti. Solo necesitas reconocerla.",
            "Abre tus sentidos a la sinfonía del cosmos. Todo es música.",
            "Tu intuición es tu oráculo personal. Escúchala con atención.",
            "Siembra intenciones claras y el universo te responderá con abundancia.",
            "El camino está en constante revelación. Disfruta el viaje."
        ];

        let lastPhraseIndex = -1; // Para evitar repetir la misma frase justo después

        cauldronContainer.addEventListener('click', () => {
            // Evitar clics múltiples mientras está en animación
            if (catImg.classList.contains('tapping')) {
                return;
            }

            // Ocultar frase anterior y feedback
            phraseBubble.classList.remove('show');
            oracleClickFeedback.style.display = 'none';
            dailyPhraseElement.textContent = '...'; // Resetear texto

            // Mostrar feedback de "cargando"
            oracleClickFeedback.style.display = 'flex';

            // Animación del gato y caldero
            catImg.classList.add('tapping');
            cauldronImg.classList.add('active-glow');

            // Retardo para simular la "preparación" del oráculo
            setTimeout(() => {
                // Quitar animaciones
                catImg.classList.remove('tapping');
                cauldronImg.classList.remove('active-glow');
                oracleClickFeedback.style.display = 'none';

                // Seleccionar una frase aleatoria
                let randomIndex;
                do {
                    randomIndex = Math.floor(Math.random() * mysticalPhrases.length);
                } while (randomIndex === lastPhraseIndex && mysticalPhrases.length > 1); // Evitar repetición inmediata
                lastPhraseIndex = randomIndex;
                
                dailyPhraseElement.textContent = mysticalPhrases[randomIndex];
                phraseBubble.classList.add('show'); // Mostrar la nueva frase
            }, 1500); // Duración de la animación + espera
        });


        

    </script>

        <!-- ============================================== -->
    <!-- OVERLAY DE VIAJE ASTRAL (Añadir justo antes de </body>) -->
    <!-- ============================================== -->
    <div id="astralJourneyOverlay" class="astral-journey-overlay">
        <canvas id="astralTunnelCanvas" class="astral-tunnel-canvas"></canvas> <!-- Canvas para el túnel -->
        <div class="astral-message-container">
            <h2 class="astral-title">Despegando hacia el cosmos de tu transacción...</h2>
            <p class="astral-subtitle">Un momento, la magia de Iluminara está en camino.</p>
            <div class="loading-spinner"></div>
        </div>
    </div>

</body>
</html>
