// public/js/product_detail_page.js
console.log('product_detail_page.js cargado.');

// ===============================================
// Funciones Auxiliares Globales (compartidas)
// ===============================================

function htmlspecialchars(str) {
    if (typeof str !== 'string' && typeof str !== 'number' || str === null || str === undefined) return '';
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
}

function formatPrice(price) {
    if (isNaN(price) || price === null || price === undefined) return '$ 0 COP';
    return '$ ' + new Intl.NumberFormat('es-CO').format(price) + ' COP';
}

function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Handler global para el botón "Añadir al Carrito" (usado en productos sugeridos)
window.handleAddToCartClick = function(event) {
    const button = event.currentTarget;
    const productId = button.dataset.productId;
    const productName = button.dataset.productName;
    const productPrice = parseFloat(button.dataset.productPrice);
    const productImage = button.dataset.productImage;
    const quantity = 1; 

    const product = { id: productId, name: productName, price: productPrice, imageUrl: productImage };

    console.log('handleAddToCartClick (Global): Clic en Añadir al Carrito. Datos:', product, 'Cantidad:', quantity);

    if (window.addToCart) {
        window.addToCart(product, quantity);
    } else {
        console.error("handleAddToCartClick: window.addToCart no está definido. Asegúrate de que cart.js se carga correctamente.");
        window.showMessage("Error: No se pudo añadir el producto al carrito. (Función no encontrada)", 'error');
    }
};

// ===============================================
// Lógica para Productos Sugeridos
// ===============================================

window.initProductDetailPage = async (mainProductId, mainProductCategory, mainProductSubcategory) => {
    console.log(`initProductDetailPage: Iniciando carga de sugerencias para ID: ${mainProductId}, Categoría: ${mainProductCategory}, Subcategoría: ${mainProductSubcategory}`);
    const suggestedProductsGrid = document.getElementById('suggestedProductsGrid');

    if (!suggestedProductsGrid) {
        console.error("initProductDetailPage: Contenedor 'suggestedProductsGrid' no encontrado. No se pueden cargar sugerencias.");
        return;
    }

    suggestedProductsGrid.innerHTML = '<p class="placeholder-text">Cargando sugerencias...</p>';

    await loadSuggestedProducts(mainProductId, mainProductCategory, mainProductSubcategory, suggestedProductsGrid);
};

async function loadSuggestedProducts(baseProductId, category, subcategory, containerElement) {
    console.log(`loadSuggestedProducts: Realizando fetch para ID ${baseProductId}, Cat ${category}, Subcat ${subcategory}`);
    try {
        const response = await fetch(`api/products.php?action=get_suggestions&base_product_id=${baseProductId}&category=${category}&subcategory=${subcategory}`);
        
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
        }
        
        const data = await response.json();

        if (data.success && data.products && data.products.length > 0) {
            console.log("loadSuggestedProducts: Sugerencias cargadas:", data.products);
            renderProductsGrid(data.products, containerElement);
        } else {
            containerElement.innerHTML = '<p class="placeholder-text">No hay sugerencias disponibles en este momento.</p>';
            console.warn("loadSuggestedProducts: No se encontraron productos sugeridos o error:", data.message || "Respuesta API inválida o sin productos.");
        }
    } catch (error) {
        containerElement.innerHTML = '<p class="placeholder-text" style="color: red;">Error al cargar las sugerencias.</p>';
        console.error("loadSuggestedProducts: Error de fetch al cargar sugerencias:", error);
    }
}

function renderProductsGrid(products, container) {
    if (!container) {
        console.error("renderProductsGrid: Contenedor no proporcionado.");
        return;
    }
    container.innerHTML = '';

    if (products.length === 0) {
        container.innerHTML = '<p class="placeholder-text">No se encontraron productos sugeridos.</p>';
        return;
    }

    products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.classList.add('product-card');

        const formattedPrice = formatPrice(product.price);
        const saleBadge = product.is_on_sale ? '<span class="sale-badge">¡Oferta!</span>' : '';
        const imageUrl = (product.image_url && typeof product.image_url === 'string' && product.image_url.trim() !== '' && product.image_url.toLowerCase() !== 'null')
                             ? product.image_url
                             : 'https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';

        productCard.innerHTML = `
            <a href="product_detail.php?id=${htmlspecialchars(product.id)}" class="product-link-wrapper">
                <img src="${htmlspecialchars(imageUrl)}" alt="${htmlspecialchars(product.name)}" class="product-img" onerror="this.onerror=null;this.src='https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';this.classList.add('error-image');">
                <div class="product-content">
                    <h3 class="product-name">${htmlspecialchars(product.name)}</h3>
                </div>
            </a>
            <p class="product-price">${formattedPrice}</p>
            ${saleBadge}
            <div class="product-actions">
                <button class="btn btn-primary add-to-cart-btn"
                    data-product-id="${htmlspecialchars(product.id)}"
                    data-product-name="${htmlspecialchars(product.name)}"
                    data-product-price="${htmlspecialchars(product.price)}"
                    data-product-image="${htmlspecialchars(imageUrl)}"
                    ${product.stock === 0 ? 'disabled' : ''}>Añadir al Carrito</button>
                <button class="btn btn-tertiary add-to-wishlist-btn"
                    data-product-id="${htmlspecialchars(product.id)}"
                    data-product-name="${htmlspecialchars(product.name)}"
                    data-product-price="${htmlspecialchars(product.price)}"
                    data-product-image="${htmlspecialchars(imageUrl)}">Añadir a Wishlist</button>
                <a href="product_detail.php?id=${htmlspecialchars(product.id)}" class="btn btn-secondary view-detail-btn">Ver Detalle</a>
            </div>
        `;
        container.appendChild(productCard);
    });

    container.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.removeEventListener('click', window.handleAddToCartClick); 
        button.addEventListener('click', window.handleAddToCartClick);
    });

    if (window.attachAddToWishlistListeners) {
        window.attachAddToWishlistListeners();
    } else {
        console.warn("product_detail_page.js: window.attachAddToWishlistListeners no está definido. Los botones 'Añadir a Wishlist' en sugerencias pueden no funcionar.");
    }
}


// ===============================================
// Lógica para Reseñas de Productos (Sección Independiente)
// ===============================================

/**
 * Carga y renderiza las reseñas existentes para un producto.
 * @param {string} productId - El ID del producto para el cual cargar las reseñas.
 */
window.loadReviews = async (productId) => {
    console.log(`loadReviews: Cargando reseñas para producto ID: ${productId}`);
    const reviewsListContainer = document.getElementById('reviewsListContainer');
    if (!reviewsListContainer) {
        console.error("loadReviews: Contenedor 'reviewsListContainer' no encontrado. No se cargarán las reseñas.");
        return;
    }
    reviewsListContainer.innerHTML = '<p class="placeholder-text">Cargando reseñas...</p>';

    try {
        const response = await fetch(`api/reviews.php?product_id=${productId}`);
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
        }
        const data = await response.json();

        if (data.success && data.reviews && data.reviews.length > 0) {
            renderReviews(data.reviews, reviewsListContainer);
        } else {
            reviewsListContainer.innerHTML = '<p class="placeholder-text">Aún no hay reseñas para este producto. ¡Sé el primero en opinar!</p>';
        }
    } catch (error) {
        reviewsListContainer.innerHTML = '<p class="placeholder-text" style="color: red;">Error al cargar las reseñas.</p>';
        console.error("loadReviews: Error de fetch al cargar reseñas:", error);
        window.showMessage("Error al cargar las reseñas. Por favor, inténtalo de nuevo más tarde.", 'error');
    }
};

/**
 * Renderiza la lista de reseñas en el contenedor.
 * @param {Array<Object>} reviews - Array de objetos de reseña.
 * @param {HTMLElement} container - El elemento DOM donde se renderizarán las reseñas.
 */
function renderReviews(reviews, container) {
    container.innerHTML = ''; // Limpiar contenido anterior
    reviews.forEach(review => {
        const reviewElement = document.createElement('div');
        reviewElement.classList.add('review-item');
        
        const reviewDate = new Date(review.created_at).toLocaleDateString('es-CO', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        reviewElement.innerHTML = `
            <div class="review-header">
                <span class="review-username">${htmlspecialchars(review.username)}</span>
                <span class="review-date">${reviewDate}</span>
            </div>
            <div class="review-rating">
                ${generateStarRating(review.rating)}
            </div>
            <p class="review-comment">${review.comment ? htmlspecialchars(review.comment) : 'Sin comentario.'}</p>
        `;
        container.appendChild(reviewElement);
    });
}

/**
 * Genera el HTML para las estrellas de puntuación.
 * @param {number} rating - La puntuación de 1 a 5.
 * @returns {string} HTML de las estrellas.
 */
function generateStarRating(rating) {
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        starsHtml += `<i class="fas fa-star ${i <= rating ? 'filled' : ''}"></i>`;
    }
    return starsHtml;
}


// ===============================================
// Lógica para las interacciones de UI del Producto Principal y Formulario de Reseñas
// ===============================================
document.addEventListener('DOMContentLoaded', () => {
    // --- Referencias a los elementos del DOM (Producto Principal) ---
    const addToCartBtnPage = document.querySelector('.add-to-cart-btn-page');
    const addToWishlistBtnPage = document.querySelector('.add-to-wishlist-btn-page');
    
    const productQuantityInput = document.getElementById('productQuantity');
    const quantityDecrementBtn = document.querySelector('.quantity-decrement-btn-page');
    const quantityIncrementBtn = document.querySelector('.quantity-increment-btn-page');

    const descriptionTabBtn = document.getElementById('descriptionTabBtnPage');
    const detailsTabBtn = document.getElementById('detailsTabBtnPage');
    
    const descriptionPanel = document.getElementById('descriptionPanelPage');
    const detailsPanel = document.getElementById('detailsPanelPage');

    // Referencias para el formulario de reseñas (AHORA EN SECCIÓN INDEPENDIENTE CON ACORDEÓN)
    const reviewForm = document.getElementById('reviewForm');
    const reviewRatingStars = document.getElementById('reviewRatingStars');
    const reviewRatingInput = document.getElementById('reviewRating');
    const reviewProductIdInput = document.getElementById('reviewProductId');

    // Referencias para el ACORDEÓN
    const toggleReviewFormBtn = document.getElementById('toggleReviewFormBtn');
    const reviewFormAccordionContent = document.getElementById('reviewFormAccordionContent');
    const accordionIcon = toggleReviewFormBtn ? toggleReviewFormBtn.querySelector('.accordion-icon') : null;


    console.log('product_detail_page.js DOMContentLoaded: Initializing UI elements.');

    // --- Listeners para el botón "Añadir al Carrito" del Producto Principal ---
    if (addToCartBtnPage) {
        addToCartBtnPage.addEventListener('click', (event) => {
            const productId = event.currentTarget.dataset.productId;
            const productName = event.currentTarget.dataset.productName;
            const productPrice = parseFloat(event.currentTarget.dataset.productPrice);
            const productImage = event.currentTarget.dataset.productImage;
            const quantity = productQuantityInput ? parseInt(productQuantityInput.value) : 1;
            const productStock = parseInt(event.currentTarget.dataset.productStock || '0');

            console.log('product_detail_page.js: Clic en Añadir al Carrito (principal). Datos:', { productId, productName, productPrice, productImage, quantity, productStock });

            if (window.addToCart) {
                window.addToCart({ id: productId, name: productName, price: productPrice, imageUrl: productImage }, quantity);
            } else {
                console.error("product_detail_page.js: window.addToCart no está definido.");
                window.showMessage("Error: No se pudo añadir al carrito. (Función del carrito no encontrada)", 'error');
            }
        });
    } else {
        console.warn('product_detail_page.js: Botón .add-to-cart-btn-page NO ENCONTRADO en la página de detalles.');
    }

    // --- Listeners para el botón "Añadir a Wishlist" del Producto Principal ---
    if (addToWishlistBtnPage) {
        addToWishlistBtnPage.addEventListener('click', (event) => {
            const product = {
                id: event.currentTarget.dataset.productId,
                name: event.currentTarget.dataset.productName,
                price: parseFloat(event.currentTarget.dataset.productPrice),
                imageUrl: event.currentTarget.dataset.productImage
            };
            
            console.log('product_detail_page.js: Clic en Añadir a Wishlist (principal). Datos:', product);

            if (window.prepareAddToWishlistModal) {
                window.prepareAddToWishlistModal(product);
            } else {
                console.error("product_detail_page.js: prepareAddToWishlistModal no está definido.");
                window.showMessage("Error: No se pudo preparar el modal de wishlist. (Función no encontrada)",'error');
            }
        });
    } else {
        console.warn('product_detail_page.js: Botón .add-to-wishlist-btn-page NO ENCONTRADO.');
    }

    // --- Lógica del Selector de Cantidad ---
    if (productQuantityInput && quantityDecrementBtn && quantityIncrementBtn) {
        quantityDecrementBtn.addEventListener('click', () => {
            let currentValue = parseInt(productQuantityInput.value);
            if (isNaN(currentValue) || currentValue < 1) currentValue = 1;
            if (currentValue > 1) {
                productQuantityInput.value = currentValue - 1;
            }
        });

        quantityIncrementBtn.addEventListener('click', () => {
            let currentValue = parseInt(productQuantityInput.value);
            if (isNaN(currentValue)) currentValue = 1;
            const maxStock = parseInt(productQuantityInput.max || '9999');
            if (currentValue < maxStock) {
                productQuantityInput.value = currentValue + 1;
            }
        });

        productQuantityInput.addEventListener('change', () => {
            let currentValue = parseInt(productQuantityInput.value);
            if (isNaN(currentValue) || currentValue < 1) {
                productQuantityInput.value = 1;
            } else if (currentValue > parseInt(productQuantityInput.max || '9999')) {
                productQuantityInput.value = parseInt(productQuantityInput.max || '9999');
            }
        });
    } else {
        console.warn('product_detail_page.js: Elementos del selector de cantidad no encontrados. Funcionalidad de cantidad no se inicializará.');
    }

    // --- Lógica de Pestañas (Descripción, Detalles) ---
    const allTabElementsExist = descriptionTabBtn && detailsTabBtn && 
                                 descriptionPanel && detailsPanel; 

    if (allTabElementsExist) {
        const tabButtons = [descriptionTabBtn, detailsTabBtn]; 
        const tabPanels = [descriptionPanel, detailsPanel]; 

        // Función para mostrar la pestaña activa y ocultar las demás
        function showTab(activeTabBtn, activeTabPanel) {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanels.forEach(panel => panel.classList.remove('active')); 

            activeTabBtn.classList.add('active');
            activeTabPanel.classList.add('active');
        }

        descriptionTabBtn.addEventListener('click', () => showTab(descriptionTabBtn, descriptionPanel));
        detailsTabBtn.addEventListener('click', () => showTab(detailsTabBtn, detailsPanel));

        showTab(descriptionTabBtn, descriptionPanel); // Por defecto, la descripción es la pestaña activa.
    } else {
        console.warn('product_detail_page.js: Elementos de pestañas no encontrados. La funcionalidad de pestañas no se inicializará.');
    }

    // ===============================================
    // Lógica para el Formulario de Reseñas (Ahora en acordeón)
    // ===============================================
    
    // Lógica para las estrellas de puntuación 
    if (reviewRatingStars && reviewRatingInput) {
        function updateStarDisplay(rating, container, isHover = false) {
            const stars = container.querySelectorAll('.fa-star');
            stars.forEach((star, index) => {
                if (isHover) {
                    if (index < rating) {
                        star.classList.add('fas'); 
                        star.classList.remove('far'); 
                    } else {
                        star.classList.add('far');
                        star.classList.remove('fas');
                    }
                } else {
                    if (index < rating) {
                        star.classList.add('fas', 'filled');
                        star.classList.remove('far');
                    } else {
                        star.classList.add('far');
                        star.classList.remove('fas', 'filled');
                    }
                }
            });
        }

        reviewRatingStars.addEventListener('click', (event) => {
            const clickedStar = event.target.closest('.fa-star');
            if (clickedStar) {
                const rating = parseInt(clickedStar.dataset.rating);
                reviewRatingInput.value = rating;
                updateStarDisplay(rating, reviewRatingStars);
            }
        });

        reviewRatingStars.addEventListener('mouseover', (event) => {
            const hoveredStar = event.target.closest('.fa-star');
            if (hoveredStar) {
                const hoverRating = parseInt(hoveredStar.dataset.rating);
                updateStarDisplay(hoverRating, reviewRatingStars, true);
            }
        });

        reviewRatingStars.addEventListener('mouseout', () => {
            const currentRating = parseInt(reviewRatingInput.value);
            updateStarDisplay(currentRating, reviewRatingStars);
        });

        // Inicializar display de estrellas a 0 al cargar
        updateStarDisplay(0, reviewRatingStars);

    } else {
        console.warn('product_detail_page.js: Elementos del sistema de estrellas de reseña no encontrados. La funcionalidad de puntuación no se inicializará.');
    }

    // Lógica para el envío del formulario de reseña
    if (reviewForm) {
        reviewForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const productId = reviewProductIdInput.value;
            const username = document.getElementById('reviewUsername').value.trim();
            const rating = parseInt(reviewRatingInput.value);
            const comment = document.getElementById('reviewComment').value.trim();

            if (!username) {
                window.showMessage("Por favor, introduce tu nombre.", 'warning');
                return;
            }
            if (rating < 1 || rating > 5 || rating === 0) {
                window.showMessage("Por favor, selecciona una puntuación de estrellas.", 'warning');
                return;
            }

            console.log('product_detail_page.js: Enviando reseña. Datos:', { productId, username, rating, comment });

            try {
                const response = await fetch('api/reviews.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        username: username,
                        rating: rating,
                        comment: comment
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
                }

                const data = await response.json();

                if (data.success) {
                    window.showMessage("¡Reseña enviada con éxito!", 'success');
                    reviewForm.reset(); 
                    reviewRatingInput.value = 0; 
                    if (reviewRatingStars) { 
                        updateStarDisplay(0, reviewRatingStars); 
                    }
                    window.loadReviews(productId); // Recargar las reseñas para mostrar la nueva
                    
                    // Colapsar el acordeón después de enviar la reseña
                    if (reviewFormAccordionContent && reviewFormAccordionContent.classList.contains('active')) {
                        toggleAccordion(toggleReviewFormBtn, reviewFormAccordionContent, accordionIcon); // Llamar a la función de alternar
                    }

                } else {
                    window.showMessage(`Error al enviar la reseña: ${data.message}`, 'error');
                    console.error("Error API al enviar reseña:", data.message);
                }
            } catch (error) {
                window.showMessage("Error de conexión al enviar la reseña. Por favor, inténtalo de nuevo.", 'error');
                console.error("Error de fetch al enviar reseña:", error);
            }
        });
    } else {
        console.warn('product_detail_page.js: Formulario de reseña (reviewForm) no encontrado. La funcionalidad de envío no se inicializará.');
    }

    // ===============================================
    // Lógica del Acordeón para el formulario de reseña
    // ===============================================
    if (toggleReviewFormBtn && reviewFormAccordionContent && accordionIcon) {
        toggleReviewFormBtn.addEventListener('click', () => {
            toggleAccordion(toggleReviewFormBtn, reviewFormAccordionContent, accordionIcon);
        });

        // Asegurarse de que el acordeón esté cerrado por defecto al cargar la página
        // La clase 'active' se añadirá por JS si se expande.
        // Si quieres que inicie abierto, puedes añadir 'active' al HTML y remover esta línea.
        reviewFormAccordionContent.style.maxHeight = null;
        reviewFormAccordionContent.classList.remove('active');
        toggleReviewFormBtn.classList.remove('active');
        accordionIcon.classList.remove('active'); // Asegurarse de que el icono esté en estado "cerrado"
    } else {
        console.warn("product_detail_page.js: Elementos del acordeón de reseñas no encontrados. La funcionalidad del acordeón no se inicializará.");
    }

    /**
     * Alterna la visibilidad de un contenido de acordeón.
     * @param {HTMLElement} headerBtn - El botón que se hace clic (el encabezado del acordeón).
     * @param {HTMLElement} contentPanel - El div del contenido del acordeón.
     * @param {HTMLElement} iconElement - El icono dentro del botón del encabezado.
     */
    function toggleAccordion(headerBtn, contentPanel, iconElement) {
        headerBtn.classList.toggle('active');
        iconElement.classList.toggle('active');
        contentPanel.classList.toggle('active');

        if (contentPanel.classList.contains('active')) {
            // Cuando se abre, establecer max-height a scrollHeight para una transición suave
            contentPanel.style.maxHeight = contentPanel.scrollHeight + "px";
        } else {
            // Cuando se cierra, establecer max-height a 0
            contentPanel.style.maxHeight = null; // O directamente "0px"
        }
    }


    // Lógica para cambiar la imagen principal al hacer clic en las miniaturas
    const mainProductImage = document.getElementById('mainProductImage');
    const thumbnailContainer = document.getElementById('imageThumbnails'); 

    if (mainProductImage && thumbnailContainer) {
        thumbnailContainer.addEventListener('click', (event) => {
            const clickedThumbnail = event.target.closest('.thumbnail-img-page');
            if (clickedThumbnail) {
                // Remover clase 'active' de todas las miniaturas
                thumbnailContainer.querySelectorAll('.thumbnail-img-page').forEach(thumb => {
                    thumb.classList.remove('active');
                });
                // Añadir clase 'active' a la miniatura clicada
                clickedThumbnail.classList.add('active');
                // Cambiar la imagen principal
                mainProductImage.src = clickedThumbnail.src;
            }
        });
    } else {
        console.warn('product_detail_page.js: Elementos de imagen principal o miniaturas no encontrados.');
    }
});
