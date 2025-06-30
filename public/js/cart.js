// public/js/cart.js
console.log('cart.js cargado.');

window.cartItems = window.cartItems || []; 

document.addEventListener('DOMContentLoaded', () => {
    const cartBtn = document.getElementById('cartBtn');
    const cartModal = document.getElementById('cartModal'); 
    const closeCartModalBtn = document.getElementById('closeCartModalBtnCart'); 

    const cartCountSpan = cartBtn ? cartBtn.querySelector('.cart-count') : null; 

    const cartItemsList = document.getElementById('cartItemsContainer'); 
    const cartTotalPrice = document.getElementById('cartSubtotal'); 
    const continueToShippingBtn = document.getElementById('proceedToShippingBtn'); 

    // Paneles y botones de navegación del checkout
    const cartPanelBtn = document.getElementById('cartStepBtn'); 
    const shippingPanelBtn = document.getElementById('shippingStepBtn'); 
    const paymentPanelBtn = document.getElementById('paymentStepBtn'); 

    const cartPanel = document.getElementById('cartPanel');
    const shippingPanel = document.getElementById('shippingPanel');
    const paymentPanel = document.getElementById('paymentPanel');
    const shippingForm = document.getElementById('shippingForm');
    
    // Botón para confirmar pago (ahora un botón genérico al final del modal)
    const confirmPaymentBtn = document.getElementById('confirmPaymentBtn'); 
    
    // Asumiendo que los botones 'volver' tienen la clase 'back-btn' y se manejan genéricamente
    const backButtons = document.querySelectorAll('.checkout-panel .back-btn'); 

    // También el modal de confirmación de añadir al carrito
    const cartConfirmationModal = document.getElementById('cartConfirmationModal');
    const closeCartConfirmationModalBtn = document.getElementById('closeCartConfirmationModalBtn');
    const continueShoppingBtn = document.getElementById('continueShoppingBtn');
    const viewCartBtnFromConfirmation = document.getElementById('viewCartBtnFromConfirmation');
    const addedProductImage = document.getElementById('addedProductImage');
    const addedProductName = document.getElementById('addedProductName');
    const addedProductPrice = document.getElementById('addedProductPrice');
    const addedProductQuantity = document.getElementById('addedProductQuantity');

    // --- CRÍTICO para depurar el error del carrito ---
    console.log('cart.js: Intentando obtener cartModal...');
    if (cartModal) {
        console.log('cart.js: ¡ÉXITO! cartModal ENCONTRADO en el DOM.');
    } else {
        console.error('cart.js: ¡ERROR CRÍTICO! cartModal NO ENCONTRADO en el DOM. El modal del carrito no funcionará.');
        if (cartBtn) cartBtn.style.display = 'none'; 
        window.showMessage('Error: El carrito no puede funcionar, elementos esenciales no encontrados.','error');
        return; 
    }
    // ----------------------------------------------------

    // ===============================================
    // Variables y Referencias Específicas para el Pago
    // ===============================================
    let selectedPaymentMethod = 'card'; 
    const paymentOptionCards = document.querySelectorAll('.payment-option-card');
    const paymentDetailsContents = document.querySelectorAll('.payment-details-content');

    // Referencias para la tarjeta virtual interactiva
    const cardHolderNameInput = document.getElementById('cardHolderName');
    const cardNumberInput = document.getElementById('cardNumber');
    const expiryDateInput = document.getElementById('expiryDate');
    const cvvInput = document.getElementById('cvv');

    const displayedCardNumber = document.getElementById('displayedCardNumber');
    const displayedCardHolderName = document.getElementById('displayedCardHolderName');
    const displayedExpiryDate = document.getElementById('displayedExpiryDate');

    const visaLogo = document.querySelector('.card-logo.visa-logo');
    const mastercardLogo = document.querySelector('.card-logo.mastercard-logo');

    // Referencias para el nuevo diseño de Pago Virtual
    const modalRocket = document.getElementById('modalRocket'); // El cohete dentro del modal
    const initiateVirtualPaymentBtn = document.getElementById('initiateVirtualPaymentBtn'); // El botón para iniciar el viaje virtual
    const paymentMethodCardsGrid = document.querySelector('.payment-method-cards-grid'); // Cuadrícula de tarjetas de método

    // Referencia para el overlay de Viaje Astral (canvas)
    const astralJourneyOverlay = document.getElementById('astralJourneyOverlay');
    const astralTunnelCanvas = document.getElementById('astralTunnelCanvas');
    let ctx = null; // Contexto de dibujo del canvas
    let animationFrameId = null; // ID para requestAnimationFrame
    let tunnelZ = 0; // Posición Z del túnel para la animación
    const TUNNEL_SPEED = 2; // Velocidad de "avance" del túnel
    const RING_COUNT = 25; // Cantidad de anillos en el túnel
    const RING_DISTANCE = 30; // Distancia entre anillos
    const RING_INITIAL_SIZE = 1; // Tamaño inicial de los anillos
    const NEON_COLORS = [ // Colores neón para los anillos
        'rgba(0, 255, 255, 0.8)', // Cyan
        'rgba(255, 0, 255, 0.8)', // Magenta
        'rgba(255, 255, 0, 0.8)', // Amarillo
        'rgba(0, 255, 0, 0.8)',   // Verde neón
        'rgba(255, 100, 255, 0.8)', // Rosa claro
        'rgba(100, 200, 255, 0.8)' // Azul claro
    ];
    const GLOW_AMOUNT = 15; // Cantidad de brillo para las líneas neón

    // Referencias específicas para el Pago Físico
    const paymentCodeDisplay = document.getElementById('paymentCodeDisplay');
    const generatePaymentCodeBtn = document.getElementById('generatePaymentCodeBtn');
    let hasGeneratedPhysicalCode = false; // Bandera para saber si ya se generó un código


    // Cargar carrito desde localStorage al inicio
    const storedCartItems = localStorage.getItem('cartItems'); // CORREGIDO: getItem
    if (storedCartItems) {
        try {
            window.cartItems = JSON.parse(storedCartItems);
        } catch (e) {
            console.error("Error parsing cartItems from localStorage:", e);
            window.cartItems = []; 
        }
    } else {
        window.cartItems = []; 
    }

    /**
     * Guarda el carrito en localStorage.
     */
    function saveCart() {
        localStorage.setItem('cartItems', JSON.stringify(window.cartItems)); // CORREGIDO: setItem
        updateCartCount();
        renderCartItems(); 
    }

    /**
     * Actualiza el contador de ítems en el icono del carrito.
     */
    function updateCartCount() {
        if (cartCountSpan) {
            const totalItems = window.cartItems.reduce((sum, item) => sum + item.quantity, 0);
            cartCountSpan.textContent = totalItems;
            if (totalItems > 0) {
                cartCountSpan.classList.add('has-items');
            } else {
                cartCountSpan.classList.remove('has-items');
            }
        }
    }

    /**
     * Renderiza los ítems del carrito en el modal.
     */
    function renderCartItems() {
        if (!cartItemsList || !cartTotalPrice) {
            console.warn('renderCartItems: Elementos del carrito (cartItemsContainer o cartSubtotal) no encontrados. Omitiendo renderizado.');
            return;
        }

        cartItemsList.innerHTML = ''; 
        let total = 0;

        if (window.cartItems.length === 0) {
            cartItemsList.innerHTML = '<p style="text-align: center; color: var(--color-gray-dark);">Tu carrito está vacío, ¡descubre la magia de Iluminara!</p>';
            cartTotalPrice.textContent = '$ 0 COP';
            if (continueToShippingBtn) continueToShippingBtn.disabled = true;
            return;
        } else {
            if (continueToShippingBtn) continueToShippingBtn.disabled = false;
        }

        window.cartItems.forEach(item => {
            total += item.price * item.quantity;
            const itemElement = document.createElement('div');
            itemElement.classList.add('cart-item');
            const imageUrl = (item.imageUrl && typeof item.imageUrl === 'string' && item.imageUrl.trim() !== '' && item.imageUrl.toLowerCase() !== 'null') 
                             ? item.imageUrl 
                             : 'https://placehold.co/80x80/FF00FF/F2D325?text=Producto';

            itemElement.innerHTML = `
                <img src="${imageUrl}" alt="${item.name}" class="cart-item-img" onerror="this.src='https://placehold.co/80x80/FF00FF/F2D325?text=Error'; this.classList.add('error-image');">
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p class="cart-item-price">$ ${new Intl.NumberFormat('es-CO').format(item.price)} COP</p>
                    <div class="cart-item-quantity">
                        <button class="decrease-quantity-btn" data-id="${item.id}">-</button>
                        <input type="number" value="${item.quantity}" min="1" data-id="${item.id}">
                        <button class="increase-quantity-btn" data-id="${item.id}">+</button>
                    </div>
                </div>
                <button class="remove-item-btn" data-id="${item.id}"><i class="fas fa-trash"></i></button>
            `;
            cartItemsList.appendChild(itemElement);
        });

        cartTotalPrice.textContent = `$ ${new Intl.NumberFormat('es-CO').format(total)} COP`;

        document.querySelectorAll('.decrease-quantity-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                updateItemQuantity(id, -1);
            });
        });
        document.querySelectorAll('.increase-quantity-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                updateItemQuantity(id, 1);
            });
        });
        document.querySelectorAll('.cart-item-quantity input').forEach(input => {
            input.addEventListener('change', (e) => {
                const id = e.target.dataset.id;
                const newQuantity = parseInt(e.target.value);
                if (!isNaN(newQuantity) && newQuantity >= 1) {
                    updateItemQuantity(id, 0, newQuantity); 
                } else {
                    e.target.value = window.cartItems.find(item => item.id === id)?.quantity || 1; 
                }
            });
        });
        document.querySelectorAll('.remove-item-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                removeItemFromCart(id);
            });
        });
    }

    /**
     * Añade un producto al carrito o actualiza su cantidad.
     * Expuesto globalmente en `window.addToCart`.
     * @param {Object} product - El objeto producto (con id, name, price, imageUrl).
     * @param {number} quantity - La cantidad a añadir.
     */
    window.addToCart = (product, quantity) => {
        if (!product || product.id === undefined || product.id === null) {
            console.error("addToCart: Producto o ID de producto inválido.", product);
            window.showMessage('Error: Datos de producto incompletos.','error');
            return;
        }
        const productIdString = String(product.id); 

        if (isNaN(quantity) || quantity <= 0) {
            console.warn("addToCart: Cantidad inválida, ajustando a 1.", quantity);
            quantity = 1;
        }

        const existingItem = window.cartItems.find(item => String(item.id) === productIdString);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            window.cartItems.push({ ...product, quantity });
        }
        saveCart();
        console.log('Estado actual del carrito:', window.cartItems);
        // Mostrar el modal de confirmación de añadido al carrito
        showCartConfirmationModal(product, quantity);
    };

    /**
     * Actualiza la cantidad de un ítem en el carrito.
     * @param {string} productId - El ID del producto.
     * @param {number} change - El cambio en la cantidad (+1, -1).
     * @param {number} [newQuantity] - Nueva cantidad absoluta (opcional, si change es 0).
     */
    function updateItemQuantity(productId, change, newQuantity = null) {
        const itemIndex = window.cartItems.findIndex(item => String(item.id) === String(productId)); 

        if (itemIndex > -1) {
            if (newQuantity !== null) {
                window.cartItems[itemIndex].quantity = Math.max(1, newQuantity);
            } else {
                window.cartItems[itemIndex].quantity += change;
                if (window.cartItems[itemIndex].quantity < 1) {
                    window.cartItems[itemIndex].quantity = 1; 
                }
            }
            saveCart();
        }
    }

    /**
     * Elimina un ítem del carrito.
     * @param {string} productId - El ID del producto a eliminar.
     */
    function removeItemFromCart(productId) {
        window.cartItems = window.cartItems.filter(item => String(item.id) !== String(productId));
        saveCart();
        window.showMessage('Producto eliminado del carrito.', 'info');
    }

    /**
     * Muestra el modal de confirmación de que un producto se añadió al carrito.
     * @param {Object} product - El producto añadido.
     * @param {number} quantity - La cantidad añadida.
     */
    function showCartConfirmationModal(product, quantity) {
        if (!cartConfirmationModal || !addedProductImage || !addedProductName || !addedProductPrice || !addedProductQuantity) {
            console.error('Elementos del modal de confirmación de carrito no encontrados.');
            window.showMessage('Producto añadido al carrito. Abre el carrito para ver los detalles.','info');
            return;
        }

        addedProductImage.src = product.imageUrl || 'https://placehold.co/100x100/8A2BE2/5C2E7E?text=Product';
        addedProductName.textContent = product.name;
        addedProductPrice.textContent = `$ ${new Intl.NumberFormat('es-CO').format(product.price)} COP`;
        addedProductQuantity.textContent = quantity;

        if (window.openModal) {
            window.openModal(cartConfirmationModal);
        } else {
            console.error("window.openModal no definido. No se puede abrir el modal de confirmación.");
            cartConfirmationModal.style.display = 'block'; 
        }
    }

    // Event Listeners para el botón del carrito en el encabezado
    if (cartBtn) {
        cartBtn.addEventListener('click', (e) => {
            e.preventDefault(); 
            if (cartModal && window.openModal) {
                console.log('cart.js: Botón de carrito clicado. Abriendo modal...');
                window.openModal(cartModal);
                showCheckoutPanel(cartPanelBtn, cartPanel); 
                renderCartItems(); 
            } else {
                console.error('cart.js: No se pudo abrir el modal del carrito. cartModal o window.openModal no disponibles.');
                window.showMessage('Error: El modal del carrito no está disponible o la función para abrirlo falla.','error');
            }
        });
    }

    // Event listener para el botón de cierre del modal del carrito
    if (closeCartModalBtn) {
        closeCartModalBtn.addEventListener('click', () => {
            if (window.closeModal) {
                window.closeModal(cartModal);
            } else {
                cartModal.style.display = 'none'; 
            }
            // Asegurarse de detener cualquier animación al cerrar el modal
            stopModalRocketAnimation();
            stopAstralJourney();
        });
    }

    // Lógica para pestañas de Checkout
    function showCheckoutPanel(tabButton, tabPanel) {
        if (!tabButton || !tabPanel) {
            console.warn('showCheckoutPanel: Botón o panel de pestaña no encontrados.');
            return;
        }

        document.querySelectorAll('.checkout-steps .step-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.checkout-panel').forEach(panel => {
            panel.classList.remove('active');
            panel.style.display = 'none'; 
        });

        tabButton.classList.add('active');
        tabPanel.classList.add('active');
        tabPanel.style.display = 'block'; 

        // Asegurarse de detener animaciones al cambiar de panel
        stopModalRocketAnimation();
        stopAstralJourney();

        // Si estamos en el panel de pago, asegurar que el contenido de pago correcto esté visible
        if (tabPanel === paymentPanel) {
            updatePaymentDetailsDisplay(selectedPaymentMethod);
        } 
    }

    // Listeners para los botones de paso del checkout
    if (cartPanelBtn) cartPanelBtn.addEventListener('click', () => showCheckoutPanel(cartPanelBtn, cartPanel));
    if (shippingPanelBtn) shippingPanelBtn.addEventListener('click', () => showCheckoutPanel(shippingPanelBtn, shippingPanel));
    if (paymentPanelBtn) paymentPanelBtn.addEventListener('click', () => showCheckoutPanel(paymentPanelBtn, paymentPanel));

    // Navegación "Volver" en el checkout
    backButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (paymentPanel && paymentPanel.classList.contains('active')) {
                showCheckoutPanel(shippingPanelBtn, shippingPanel);
            } else if (shippingPanel && shippingPanel.classList.contains('active')) {
                showCheckoutPanel(cartPanelBtn, cartPanel);
            }
            stopModalRocketAnimation(); // Detener animación al volver
            stopAstralJourney();
        });
    });

    // Listener para el botón "Continuar a Envío"
    if (continueToShippingBtn) {
        continueToShippingBtn.addEventListener('click', () => {
            if (window.cartItems.length === 0) {
                window.showMessage('Tu carrito está vacío. Añade productos para continuar.','error');
                return;
            }
            if (shippingPanelBtn && shippingPanel) { 
                showCheckoutPanel(shippingPanelBtn, shippingPanel);
            }
        });
    }

    // Listener para el envío del formulario de envío (simula el envío y pasa a pago)
    if (shippingForm) {
        shippingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const fullName = document.getElementById('fullName').value.trim();
            const address = document.getElementById('address').value.trim();
            const city = document.getElementById('city').value.trim();
            const postalCode = document.getElementById('postalCode').value.trim();
            const country = document.getElementById('country').value.trim();

            if (!fullName || !address || !city || !postalCode || !country) {
                window.showMessage('Por favor, completa todos los campos de envío.', 'warning');
                return;
            }
            window.showMessage('Dirección de envío guardada.', 'success');
            if (paymentPanelBtn && paymentPanel) {
                showCheckoutPanel(paymentPanelBtn, paymentPanel);
            }
        });
    }

    // ===============================================
    // Lógica Específica de Selección y Confirmación de Pago
    // ===============================================

    /**
     * Capitaliza la primera letra de una cadena.
     * @param {string} string - La cadena a capitalizar.
     * @returns {string} La cadena con la primera letra en mayúscula.
     */
    function capitalizeFirstLetter(string) {
        if (!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    /**
     * Actualiza la visualización del contenido de detalles de pago
     * basándose en el método de pago seleccionado.
     * @param {string} method - El método de pago ('card', 'virtual', 'physical').
     */
    function updatePaymentDetailsDisplay(method) {
        paymentDetailsContents.forEach(content => {
            content.classList.remove('active');
            content.style.display = 'none'; 
        });
        const activeContent = document.getElementById(`payment${capitalizeFirstLetter(method)}Details`);
        if (activeContent) {
            activeContent.classList.add('active');
            activeContent.style.display = 'block'; 
        }
        // Asegurarse de detener animaciones si se cambia de método de pago (ej: de virtual a tarjeta)
        stopModalRocketAnimation();
        stopAstralJourney();

        // Reiniciar estado del código físico al cambiar a la pestaña de pago físico
        if (method === 'physical') {
            if (paymentCodeDisplay) {
                paymentCodeDisplay.innerHTML = '<span>Genera tu código</span><i class="fas fa-barcode code-icon"></i>';
                paymentCodeDisplay.classList.remove('generated');
            }
            if (generatePaymentCodeBtn) {
                generatePaymentCodeBtn.textContent = 'Generar Código de Pago';
                generatePaymentCodeBtn.disabled = false;
            }
            hasGeneratedPhysicalCode = false; // Resetear la bandera
        }
    }

    // Listeners para seleccionar el método de pago (las tarjetas clicables)
    if (paymentOptionCards.length > 0) {
        paymentOptionCards.forEach(card => {
            card.addEventListener('click', () => {
                paymentOptionCards.forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                
                selectedPaymentMethod = card.dataset.paymentMethod;
                console.log('Método de pago seleccionado:', selectedPaymentMethod);
                updatePaymentDetailsDisplay(selectedPaymentMethod);
            });
        });
    }

    /**
     * Actualiza los datos mostrados en la tarjeta virtual.
     */
    function updateCreditCardDisplay() {
        // Solo intenta actualizar si los elementos existen en el DOM
        if (!displayedCardNumber || !displayedCardHolderName || !displayedExpiryDate || !cardNumberInput || !cardHolderNameInput || !expiryDateInput) {
            console.warn("Elementos de la tarjeta virtual no encontrados para actualizar (probablemente en otra sección).");
            return;
        }

        let cardNumber = cardNumberInput.value.replace(/\s/g, ''); 
        if (cardNumber.length > 0) {
            cardNumber = cardNumber.match(/.{1,4}/g)?.join(' ') || '';
            displayedCardNumber.textContent = cardNumber;
        } else {
            displayedCardNumber.textContent = '#### #### #### ####';
        }

        if (visaLogo && mastercardLogo) {
            visaLogo.style.display = 'none';
            mastercardLogo.style.display = 'none';
            if (cardNumberInput.value.startsWith('4')) {
                visaLogo.style.display = 'block';
            } else if (cardNumberInput.value.startsWith('5')) {
                mastercardLogo.style.display = 'block';
            }
        }

        let cardHolderName = cardHolderNameInput.value.trim().toUpperCase();
        displayedCardHolderName.textContent = cardHolderName || 'JOHN DOE';

        let expiryDate = expiryDateInput.value.trim();
        if (expiryDate.length === 2 && !expiryDate.includes('/')) {
            expiryDate += '/';
        }
        displayedExpiryDate.textContent = expiryDate || 'MM/AA';
    }

    /**
     * Genera un código de pago de ejemplo (simulado).
     */
    function generatePhysicalPaymentCode() {
        const timestamp = Date.now().toString().slice(-8); 
        const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0'); 
        const orderId = 'ILUM' + Math.floor(Math.random() * 900) + 100; 
        return `${orderId}-${timestamp}-${random}`;
    }

    // Listener para el botón "Generar Código de Pago"
    if (generatePaymentCodeBtn) {
        generatePaymentCodeBtn.addEventListener('click', () => {
            const code = generatePhysicalPaymentCode();
            if (paymentCodeDisplay) {
                paymentCodeDisplay.innerHTML = `<span>${code}</span>`;
                paymentCodeDisplay.classList.add('generated'); 
                hasGeneratedPhysicalCode = true;
                generatePaymentCodeBtn.textContent = 'Código Generado';
                generatePaymentCodeBtn.disabled = true; 
                window.showMessage('Código de pago generado con éxito. Anótalo.', 'success');
            }
        });
    }

    /**
     * Inicia la animación de despegue del cohete dentro del modal.
     */
    function startModalRocketAnimation() {
        if (modalRocket) {
            modalRocket.style.display = 'block'; // AHORA SÍ: Asegurarse de que sea visible
            modalRocket.classList.add('takeoff');
            // Reiniciar animación si ya estaba en otra posición
            modalRocket.style.transition = 'none'; 
            modalRocket.style.bottom = '-100px';
            modalRocket.style.transform = 'translateY(0) translateX(0) scale(1)';
            modalRocket.style.opacity = 1;
            void modalRocket.offsetWidth; 
            modalRocket.style.transition = ''; 
            
            setTimeout(() => {
                stopModalRocketAnimation();
            }, 1500); 
        }
    }

    /**
     * Detiene y resetea la animación del cohete del modal.
     */
    function stopModalRocketAnimation() {
        if (modalRocket) {
            modalRocket.classList.remove('takeoff');
            modalRocket.style.display = 'none'; 
            modalRocket.style.bottom = '-100px'; 
            modalRocket.style.transform = 'translateY(0) translateX(0) scale(1)';
            modalRocket.style.opacity = 1; 
        }
    }

    /**
     * Dibuja un túnel poligonal en el canvas con efecto neón.
     * @param {CanvasRenderingContext2D} ctx - El contexto de dibujo 2D.
     * @param {number} width - Ancho del canvas.
     * @param {number} height - Alto del canvas.
     * @param {number} z - Posición Z actual para la perspectiva.
     */
    function drawTunnel(ctx, width, height, z) {
        ctx.clearRect(0, 0, width, height); 
        
        const gradient = ctx.createRadialGradient(width / 2, height / 2, 0, width / 2, height / 2, Math.max(width, height) / 2);
        gradient.addColorStop(0, '#000000'); 
        gradient.addColorStop(1, '#08001a'); 
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, width, height);

        for (let i = 0; i < RING_COUNT; i++) {
            let ringZ = (i * RING_DISTANCE + z) % (RING_COUNT * RING_DISTANCE);
            if (ringZ < 0) ringZ += (RING_COUNT * RING_DISTANCE); 

            const perspectiveFactor = 1 - (ringZ / (RING_COUNT * RING_DISTANCE)); 
            const size = RING_INITIAL_SIZE + (Math.max(width, height) / 2) * perspectiveFactor * 1.5; 
            const opacity = Math.max(0, perspectiveFactor - 0.1); 

            if (opacity <= 0.05) continue; 

            const colorIndex = Math.floor(i % NEON_COLORS.length);
            const baseColor = NEON_COLORS[colorIndex];
            const colorWithOpacity = baseColor.replace(/, [\d\.]+\)/, `, ${(opacity * 0.8).toFixed(1)})`); 

            ctx.beginPath();
            const numSides = 8; 
            const radius = size * 0.5; 

            for (let j = 0; j < numSides; j++) {
                const angle = (Math.PI * 2 / numSides) * j + Math.PI / numSides; 
                const x = width / 2 + radius * Math.cos(angle);
                const y = height / 2 + radius * Math.sin(angle);
                if (j === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            }
            ctx.closePath();

            ctx.strokeStyle = colorWithOpacity;
            ctx.lineWidth = 2 + (perspectiveFactor * 3); 
            
            ctx.shadowBlur = GLOW_AMOUNT * opacity;
            ctx.shadowColor = baseColor; 

            ctx.stroke();

            ctx.shadowBlur = 0;
            ctx.shadowColor = 'transparent';
        }
        
        // Dibujar pequeñas partículas/estrellas en movimiento para un efecto más completo
        const starCount = 100;
        ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
        for (let i = 0; i < starCount; i++) {
            const x = Math.random() * width;
            const y = (Math.random() * height + (z * 5)) % height; 
            const starSize = 1 + Math.random() * 2;
            ctx.beginPath();
            ctx.arc(x, y, starSize, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    /**
     * Bucle principal de animación del túnel.
     */
    function animateTunnel() {
        if (!ctx || !astralTunnelCanvas) {
            console.error("Canvas context or element not available for animation.");
            return;
        }

        astralTunnelCanvas.width = window.innerWidth;
        astralTunnelCanvas.height = window.innerHeight;

        tunnelZ += TUNNEL_SPEED; 
        
        drawTunnel(ctx, astralTunnelCanvas.width, astralTunnelCanvas.height, tunnelZ);
        
        animationFrameId = requestAnimationFrame(animateTunnel);
    }

    /**
     * Inicia la animación del viaje astral a pantalla completa.
     */
    function startAstralJourney() {
        if (astralJourneyOverlay) {
            astralJourneyOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; 

            if (astralTunnelCanvas) {
                ctx = astralTunnelCanvas.getContext('2d');
                if (animationFrameId) cancelAnimationFrame(animationFrameId); 
                animateTunnel();
            } else {
                console.error("astralTunnelCanvas not found.");
            }
        }
    }

    /**
     * Detiene la animación del viaje astral y lo oculta.
     */
    function stopAstralJourney() {
        if (astralJourneyOverlay) {
            astralJourneyOverlay.classList.remove('active');
            document.body.style.overflow = ''; 
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId); 
                animationFrameId = null;
            }
        }
    }


    /**
     * Simula el procesamiento de un pago.
     */
    async function simulatePaymentProcessing(method) {
        return new Promise(resolve => {
            setTimeout(() => {
                console.log(`Simulando procesamiento de pago con método: ${method}`);
                resolve(true); 
            }, 3500); 
        });
    }

    // Listener para el botón "Confirmar Pedido" (genérico)
    if (confirmPaymentBtn) {
        confirmPaymentBtn.addEventListener('click', async () => {
            console.log('Confirmar Pedido con método:', selectedPaymentMethod);
            
            let paymentConfirmed = false;

            if (selectedPaymentMethod === 'card') {
                const cardNumber = cardNumberInput ? cardNumberInput.value.trim() : ''; 
                const expiryDate = expiryDateInput ? expiryDateInput.value.trim() : '';
                const cvv = cvvInput ? cvvInput.value.trim() : '';
                const cardHolderName = cardHolderNameInput ? cardHolderNameInput.value.trim() : '';

                if (!cardHolderName) {
                    window.showMessage('Por favor, ingresa el nombre del titular de la tarjeta.', 'warning');
                    return;
                }
                if (!cardNumber || !expiryDate || !cvv) {
                    window.showMessage('Por favor, completa todos los datos de la tarjeta.', 'warning');
                    return;
                }
                if (cardNumber.length < 13 || cardNumber.length > 19 || isNaN(cardNumber.replace(/\s/g, ''))) {
                    window.showMessage('Número de tarjeta inválido (debe ser numérico entre 13 y 19 dígitos).', 'error');
                    return;
                }
                if (!/^(0[1-9]|1[0-2])\/?([0-9]{2})$/.test(expiryDate)) {
                    window.showMessage('Fecha de vencimiento inválida (formato MM/AA requerido).', 'error');
                    return;
                }
                if (cvv.length !== 3 && cvv.length !== 4 || isNaN(cvv)) {
                    window.showMessage('CVV inválido (3 o 4 dígitos numéricos).', 'error');
                    return;
                }

                window.showMessage('Procesando pago con tarjeta...', 'info');
                paymentConfirmed = await simulatePaymentProcessing('card');
                if (paymentConfirmed) {
                     finalizePaymentSuccess();
                } else {
                    window.showMessage('Hubo un error al procesar tu pago con tarjeta. Por favor, inténtalo de nuevo.', 'error');
                }

            } else if (selectedPaymentMethod === 'virtual') {
                console.warn("confirmPaymentBtn clicado con método virtual. Esto debería ser manejado por initiateVirtualPaymentBtn.");
                if (initiateVirtualPaymentBtn) {
                    initiateVirtualPaymentBtn.click(); // Redirige al flujo del botón virtual
                }
                return;

            } else if (selectedPaymentMethod === 'physical') {
                if (!hasGeneratedPhysicalCode) {
                    window.showMessage('Por favor, primero genera tu código de pago.', 'warning');
                    return;
                }
                window.showMessage('Confirmando pago físico...', 'info');
                paymentConfirmed = await simulatePaymentProcessing('physical'); 
                if (paymentConfirmed) {
                    finalizePaymentSuccess();
                } else {
                    window.showMessage('Hubo un error al confirmar tu pago físico. Por favor, inténtalo de nuevo.', 'error');
                }
            }
        });
    }

    /**
     * Función para finalizar el pago con éxito.
     */
    function finalizePaymentSuccess() {
        window.showMessage('¡Tu pedido ha sido confirmado con éxito! Gracias por tu compra.', 'success');
        window.cartItems = []; 
        saveCart(); 
        
        if (cartModal && window.closeModal) {
            window.closeModal(cartModal);
        } else if (cartModal) {
            cartModal.style.display = 'none'; 
            document.body.classList.remove('modal-open');
        }
        stopModalRocketAnimation();
        stopAstralJourney(); 
    }

    // Listener para el nuevo botón "Iniciar Viaje Virtual"
    if (initiateVirtualPaymentBtn) {
        initiateVirtualPaymentBtn.addEventListener('click', async () => {
            initiateVirtualPaymentBtn.disabled = true; 
            window.showMessage('Preparando tu viaje astral...', 'info', 1000);

            startModalRocketAnimation();
            
            await new Promise(resolve => setTimeout(resolve, 2500));

            if (cartModal && window.closeModal) {
                window.closeModal(cartModal);
            } else if (cartModal) {
                cartModal.style.display = 'none'; 
            }
            document.body.classList.remove('modal-open');

            startAstralJourney(); // Inicia el túnel del canvas

            const paymentConfirmed = await simulatePaymentProcessing('virtual'); 
            
            stopAstralJourney(); // Detiene el túnel del canvas

            // Mensaje específico después del viaje astral
            window.showMessage('En un entorno real, serías redirigido a la pasarela de pagos virtual (PSE/Nequi).', 'info', 5000);

            if (paymentConfirmed) {
                finalizePaymentSuccess();
            } else {
                window.showMessage('Hubo un error al procesar tu pago virtual. Por favor, inténtalo de nuevo.', 'error');
            }
            
            initiateVirtualPaymentBtn.disabled = false;
        });
    }

    // Event listeners para el modal de confirmación de carrito
    if (closeCartConfirmationModalBtn) {
        closeCartConfirmationModalBtn.addEventListener('click', () => {
            if (window.closeModal) {
                window.closeModal(cartConfirmationModal);
            } else {
                cartConfirmationModal.style.display = 'none'; 
            }
        });
    }
    if (continueShoppingBtn) {
        continueShoppingBtn.addEventListener('click', () => {
            if (window.closeModal) {
                window.closeModal(cartConfirmationModal);
            } else {
                cartConfirmationModal.style.display = 'none'; 
            }
        });
    }
    if (viewCartBtnFromConfirmation) {
        viewCartBtnFromConfirmation.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.closeModal) {
                window.closeModal(cartConfirmationModal); 
            } else {
                cartConfirmationModal.style.display = 'none'; 
            }
            
            if (cartModal && window.openModal) { 
                window.openModal(cartModal);
                showCheckoutPanel(cartPanelBtn, cartPanel); 
                renderCartItems(); 
            } else {
                cartModal.style.display = 'block'; 
                document.body.classList.add('modal-open');
            }
        });
    }

    // Inicialización al cargar la página
    updateCartCount();
    renderCartItems();
    if (cardHolderNameInput && cardNumberInput && expiryDateInput) {
        updateCreditCardDisplay(); 
    }
    stopAstralJourney(); // Asegurarse de que el overlay esté oculto al inicio
});
