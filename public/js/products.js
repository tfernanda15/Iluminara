// public/js/products.js
console.log('products.js cargado. (AJUSTADO: loadProducts global y nuevos filtros)');

// Hacemos algunas variables globales para que el script en index.php pueda interactuar con ellas
let productsData = []; 

// Helper para escapar HTML (se mantiene)
function htmlspecialchars(str) {
    if (typeof str !== 'string' && typeof str !== 'number' || str === null || str === undefined) return '';
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
}

// Helper para formatear precio a COP (se mantiene)
function formatPrice(price) {
    if (isNaN(price) || price === null || price === undefined) return '$ 0 COP';
    return '$ ' + new Intl.NumberFormat('es-CO').format(price) + ' COP';
}

/**
 * Carga y renderiza los productos.
 * MODIFICADO: AHORA ES GLOBAL (window.loadProducts) y acepta el objeto 'filters'.
 * @param {Object} filters - Objeto con filtros (searchTerm, categories, subcategories, minPrice, maxPrice, colors, materials, sortOrder).
 */
window.loadProducts = async (filters = {}) => { // ¡CAMBIO CLAVE: window.loadProducts!
    const productGrid = document.getElementById('productGrid');
    if (!productGrid) {
        console.error('products.js: productGrid NO ENCONTRADO. No se pueden cargar productos.');
        return;
    }

    productGrid.innerHTML = '<p class="placeholder-text">Cargando productos, por favor espera...</p>';
    try {
        let apiUrl = 'api/products.php?action=get_all';
        const params = new URLSearchParams();

        // 1. Término de búsqueda
        if (filters.searchTerm) {
            params.append('search_term', filters.searchTerm); // Enviamos como 'search_term'
        }
        
        // 2. Categorías (puede ser múltiple ahora)
        if (filters.categories && filters.categories.length > 0) {
            params.append('categories', filters.categories.join(','));
        }
        // 3. Subcategorías
        if (filters.subcategories && filters.subcategories.length > 0) {
            params.append('subcategories', filters.subcategories.join(','));
        }

        // 4. Precio máximo y mínimo
        if (filters.maxPrice !== undefined && filters.maxPrice !== null) {
            params.append('max_price', filters.maxPrice);
        }
        if (filters.minPrice !== undefined && filters.minPrice !== null) {
            params.append('min_price', filters.minPrice);
        } else {
            params.append('min_price', 0); // Default a 0 si no se especifica
        }

        // 5. Colores
        if (filters.colors && filters.colors.length > 0) {
            params.append('colors', filters.colors.join(','));
        }
        // 6. Materiales
        if (filters.materials && filters.materials.length > 0) {
            params.append('materials', filters.materials.join(','));
        }

        // 7. Ordenar por (tu original usaba 'sort_order', con nuevos valores)
        if (filters.sortOrder) { 
            params.append('sort_order', filters.sortOrder); 
        }

        if (params.toString()) {
            apiUrl += '&' + params.toString();
        }

        console.log('products.js: Fetching products from:', apiUrl);
        const response = await fetch(apiUrl);
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
        }
        const data = await response.json();
        console.log('products.js: API Response:', data);

        if (data.success) {
            productsData = data.products; // Almacenar los productos
            renderProducts(productsData); // LLAMADA A TU FUNCIÓN ORIGINAL renderProducts
        } else {
            productGrid.innerHTML = `<p class="placeholder-text" style="color: red;">Error al cargar productos: ${htmlspecialchars(data.message)}</p>`;
            console.error('products.js: Error fetching products from API:', data.message);
        }
    } catch (error) {
        console.error('products.js: Error al cargar los productos:', error);
        productGrid.innerHTML = '<p class="placeholder-text" style="color: red;">Error de conexión al servidor al cargar productos.</p>';
    }
};

/**
 * Renderiza los productos en la cuadrícula.
 * ESTA FUNCIÓN SE MANTIENE EXACTAMENTE COMO LA TUYA PARA PRESERVAR LA CARGA DE IMÁGENES.
 * @param {Array} products - Array de objetos de producto.
 */
function renderProducts(products) {
    const productGrid = document.getElementById('productGrid');
    if (!productGrid) return; 
    productGrid.innerHTML = ''; 

    if (products.length === 0) {
        productGrid.innerHTML = '<p class="placeholder-text">No se encontraron productos con los filtros aplicados.</p>';
        return;
    }

    products.forEach(product => {
        // MUY IMPORTANTE: Usar product.image_url como en tu código original
        const imageUrl = (product.image_url && typeof product.image_url === 'string' && product.image_url.trim() !== '' && product.image_url.toLowerCase() !== 'null')
                         ? product.image_url
                         : 'https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';

        const productCard = document.createElement('div');
        productCard.classList.add('product-card');
        productCard.innerHTML = `
            <a href="product_detail.php?id=${htmlspecialchars(product.id)}" class="product-link-wrapper">
                <img src="${htmlspecialchars(imageUrl)}" alt="${htmlspecialchars(product.name)}" class="product-img" onerror="this.src='https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';this.classList.add('error-image');">
                <div class="product-content">
                    <h3 class="product-name">${htmlspecialchars(product.name)}</h3>
                </div>
            </a>
            <p class="product-price">${formatPrice(product.price)}</p>
            <div class="product-actions">
                <button class="btn btn-primary add-to-cart-btn"
                    data-product-id="${htmlspecialchars(product.id)}"
                    data-product-name="${htmlspecialchars(product.name)}"
                    data-product-price="${htmlspecialchars(product.price)}"
                    data-product-image="${htmlspecialchars(imageUrl)}">Añadir al Carrito</button>
                <button class="btn btn-tertiary add-to-wishlist-btn"
                    data-product-id="${htmlspecialchars(product.id)}"
                    data-product-name="${htmlspecialchars(product.name)}"
                    data-product-price="${htmlspecialchars(product.price)}"
                    data-product-image="${htmlspecialchars(imageUrl)}">Añadir a Wishlist</button>
                <a href="product_detail.php?id=${htmlspecialchars(product.id)}" class="btn btn-secondary view-detail-btn">Ver Detalle</a>
            </div>
        `;
        productGrid.appendChild(productCard);
    });

    // Una vez que los productos se han renderizado, adjuntar los listeners
    attachProductActionListeners();
}

/**
 * Adjunta los listeners a los botones de acción de cada producto.
 * ESTA FUNCIÓN SE MANTIENE EXACTAMENTE COMO LA TUYA.
 */
function attachProductActionListeners() {
    // Listeners para los botones "Añadir al Carrito"
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.removeEventListener('click', handleAddToCartClick); // Evitar duplicados
        button.addEventListener('click', handleAddToCartClick);
    });

    // Llamar a la función global para adjuntar listeners de wishlist,
    // que AHORA ESTÁ EN wishlist.js (prepareAddToWishlistModal o attachAddToWishlistListeners)
    if (window.attachAddToWishlistListeners) { // Esta es la función global de wishlist.js
        window.attachAddToWishlistListeners();
    } else {
        console.warn("products.js: window.attachAddToWishlistListeners no está definido. Los botones 'Añadir a Wishlist' pueden no funcionar.");
    }
}

/**
 * Manejador de clic para los botones "Añadir al Carrito".
 * ESTA FUNCIÓN SE MANTIENE EXACTAMENTE COMO LA TUYA.
 */
function handleAddToCartClick() {
    const productId = this.dataset.productId;
    const productName = this.dataset.productName;
    const productPrice = parseFloat(this.dataset.productPrice);
    const productImage = this.dataset.productImage;
    const quantity = 1; // Siempre añadir 1 por defecto

    const product = {
        id: productId,
        name: productName,
        price: productPrice,
        imageUrl: productImage 
    };

    if (window.addToCart) {
        window.addToCart(product, quantity);
    } else {
        console.error("products.js: addToCart no está definido en el objeto window. Asegúrate de que cart.js se carga correctamente.");
        window.showMessage("Error: No se pudo añadir el producto al carrito. (Función no encontrada)", 'error');
    }
}

// ===============================================
// ELIMINADAS: La función `applyFilters` y sus listeners originales
// dentro de este `DOMContentLoaded` porque ahora serán manejados
// por el script en línea en index.php.
// ===============================================
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar el rango de precios display (si products.js lo manejaba antes)
    const priceRange = document.getElementById('priceRange');
    const maxPriceDisplay = document.getElementById('maxPriceDisplay');
    if (priceRange && maxPriceDisplay) {
        maxPriceDisplay.textContent = formatPrice(priceRange.value); 
    }

    // La carga inicial de productos ahora se invoca desde el script en línea en index.php
    // a través de `window.collectAndApplyFilters()`. Por lo tanto, no necesitamos
    // una llamada directa a `loadProducts()` aquí.
    // El script en index.php se asegura de que loadProducts() se llame al cargar la página
    // con los filtros por defecto.
});

