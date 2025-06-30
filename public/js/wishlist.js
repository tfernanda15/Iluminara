// public/js/wishlist.js
console.log('wishlist.js cargado. (Versión con productos clickables)');

// Función auxiliar para obtener un elemento por ID de forma segura
function getElementByIdSafe(id, context = document) {
    const element = context.getElementById(id);
    if (!element) {
        console.error(`ERROR CRÍTICO (wishlist.js): Elemento #${id} NO ENCONTRADO en el DOM. Revisa tu HTML.`);
    }
    return element;
}

// Función auxiliar para obtener un elemento por querySelector de forma segura
function querySelectorSafe(selector, context = document) {
    const element = context.querySelector(selector);
    if (!element) {
        console.error(`ERROR CRÍTICO (wishlist.js): Elemento '${selector}' NO ENCONTRADO en el DOM. Revisa tu HTML.`);
    }
    return element;
}


document.addEventListener('DOMContentLoaded', () => {
    // --- Declaración de Elementos HTML de la Wishlist usando funciones seguras ---
    // myWishlistLink ya no se usa si se eliminó del HTML
    const wishlistTabBtn = getElementByIdSafe('wishlistTabBtn');
    
    // Secciones y contenedores principales
    const wishlistBoardsSection = querySelectorSafe('.wishlist-boards-section');
    const wishlistBoardsContainer = getElementByIdSafe('wishlistBoardsContainer');
    const createBoardBtn = getElementByIdSafe('createBoardBtn');

    const wishlistItemsSection = querySelectorSafe('.wishlist-items-section');
    const backToBoardsBtn = getElementByIdSafe('backToBoardsBtn');
    const currentBoardNameDisplay = getElementByIdSafe('currentBoardName');
    const currentBoardItemsContainer = getElementByIdSafe('currentBoardItems');

    // Modales y sus elementos para crear tablero
    const createBoardModal = getElementByIdSafe('createBoardModal'); 
    const closeCreateBoardModalBtn = getElementByIdSafe('closeCreateBoardModalBtn');
    const createBoardForm = getElementByIdSafe('createBoardForm');
    const newBoardNameInput = getElementByIdSafe('newBoardName');
    const newBoardDescriptionInput = getElementByIdSafe('newBoardDescription');
    const cancelCreateBoardBtn = getElementByIdSafe('cancelCreateBoardBtn');

    const addProductToWishlistModal = getElementByIdSafe('addProductToWishlistModal'); 
    const closeAddProductToWishlistModalBtn = getElementByIdSafe('closeAddProductToWishlistModalBtn');
    const wishlistProductImage = getElementByIdSafe('wishlistProductImage');
    const wishlistProductName = getElementByIdSafe('wishlistProductName');
    const wishlistProductPrice = getElementByIdSafe('wishlistProductPrice');
    const boardSelect = getElementByIdSafe('boardSelect');
    const itemNotes = getElementByIdSafe('itemNotes');
    const addProductToWishlistForm = getElementByIdSafe('addProductToWishlistForm');

    // Inputs ocultos para datos del producto que se añadirán
    const wishlistProductIdInput = getElementByIdSafe('wishlistProductId');
    const wishlistProductActualPriceInput = getElementByIdSafe('wishlistProductActualPrice');
    const wishlistProductActualImageInput = getElementByIdSafe('wishlistProductActualImage');

    const openCreateBoardLink = getElementByIdSafe('openCreateBoardLink');
    const cancelAddToWishlistBtn = getElementByIdSafe('cancelAddToWishlistBtn');

    const myAccountModal = getElementByIdSafe('myAccountModal'); 


    // ===============================================
    // Funciones Auxiliares
    // ===============================================

    /** Helper para escapar HTML (seguridad) */
    function htmlspecialchars(str) {
        if (typeof str !== 'string' && typeof str !== 'number' || str === null || str === undefined) return ''; 
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str))); 
        return div.innerHTML;
    }

    /** Helper para formatear precio a COP. */
    function formatPrice(price) {
        if (isNaN(price) || price === null || price === undefined) return '$ 0 COP';
        return '$ ' + new Intl.NumberFormat('es-CO').format(price) + ' COP';
    }

    /**
     * Obtiene el ID del usuario desde localStorage.
     * Genera un ID temporal si no existe.
     */
    function getUserId() {
        let userId = localStorage.getItem('userId');
        if (!userId || userId === 'null' || userId === 'undefined' || userId.startsWith('anon_')) { 
            userId = 'anon_' + Math.random().toString(36).substring(2, 15);
            localStorage.setItem('userId', userId); 
            console.log('wishlist.js: Asignado o actualizado ID de usuario temporal:', userId);
        }
        return userId; 
    }

    // ===============================================
    // Lógica del Modal "Mi Cuenta" -> "Mi Wishlist" (Vistas de Tableros / Items)
    // ===============================================

    /** Muestra la vista de tableros de wishlist y oculta la vista de ítems. */
    function showWishlistBoardsView() {
        console.log('wishlist.js: showWishlistBoardsView - intentando mostrar sección de tableros.');
        if (wishlistBoardsSection) {
            wishlistBoardsSection.style.display = 'block'; 
            console.log('wishlist.js: .wishlist-boards-section display: block');
            wishlistBoardsSection.offsetHeight; 
            console.log('wishlist.js: Forced reflow on wishlistBoardsSection via showWishlistBoardsView.');
        } 
        if (wishlistItemsSection) {
            wishlistItemsSection.style.display = 'none';    
            console.log('wishlist.js: .wishlist-items-section display: none');
        } 
        if (createBoardBtn) {
            createBoardBtn.style.display = 'inline-block';         
        }
    }

    /** Muestra la vista de ítems de un tablero y oculta la vista de tableros. */
    function showWishlistItemsView(boardName) {
        console.log('wishlist.js: showWishlistItemsView - intentando mostrar sección de ítems para:', boardName);
        if (wishlistBoardsSection) {
            wishlistBoardsSection.style.display = 'none'; 
            console.log('wishlist.js: .wishlist-boards-section display: none');
        } 
        if (wishlistItemsSection) {
            wishlistItemsSection.style.display = 'block';   
            console.log('wishlist.js: .wishlist-items-section display: block');
            wishlistItemsSection.offsetHeight; 
            console.log('wishlist.js: Forced reflow on wishlistItemsSection via showWishlistItemsView.');
        } 
        if (createBoardBtn) {
            createBoardBtn.style.display = 'none';                 
        }
        if (currentBoardNameDisplay) {
            currentBoardNameDisplay.textContent = `Tablero: ${htmlspecialchars(boardName)}`;
        }
    }


    /**
     * Carga y renderiza los tableros de wishlist del usuario.
     * Expuesta globalmente para ser llamada desde auth.js o main.js.
     */
    window.loadWishlistBoards = async () => {
        const userId = getUserId(); 
        console.log('wishlist.js: loadWishlistBoards - Intentando cargar tableros para user_id:', userId);

        if (!userId || userId.startsWith('anon_')) { 
            if (wishlistBoardsContainer) wishlistBoardsContainer.innerHTML = '<p class="placeholder-text">Inicia sesión para ver tus tableros.</p>';
            showWishlistBoardsView(); 
            return;
        }
        
        if (wishlistBoardsContainer) {
            wishlistBoardsContainer.innerHTML = ''; 
        }

        try {
            const apiUrl = `api/wishlist.php?action=get_boards&user_id=${encodeURIComponent(userId)}`;
            console.log('DEBUG (loadWishlistBoards): URL de la API para tableros:', apiUrl);
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'X-User-ID': userId 
                }
            });

            if (!response.ok) {
                const errorText = await response.text(); 
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }

            const data = await response.json();
            console.log('wishlist.js: loadWishlistBoards - API Response:', data);

            if (data.success) {
                renderWishlistBoards(data.boards);
            } else {
                if (wishlistBoardsContainer) wishlistBoardsContainer.innerHTML = `<p class="placeholder-text" style="color: red;">Error al cargar tableros: ${htmlspecialchars(data.message)}</p>`;
                window.showMessage('Error al cargar tableros: ' + data.message, 'error');
                console.error('wishlist.js: Error fetching boards from API:', data.message);
            }
        } catch (error) {
            console.error('wishlist.js: Error de conexión/respuesta al cargar tableros:', error);
            if (wishlistBoardsContainer) wishlistBoardsContainer.innerHTML = '<p class="placeholder-text" style="color: red;">Error de conexión al servidor al cargar tableros. Consulta la consola para más detalles.</p>';
            window.showMessage('Error de conexión al servidor al cargar tableros.', 'error');
        }
    };

    /**
     * Renderiza las tarjetas de tablero en el contenedor de tableros.
     * @param {Array} boards Array de objetos de tablero.
     */
    function renderWishlistBoards(boards) {
        console.log('wishlist.js: renderWishlistBoards - Iniciando renderizado de', boards.length, 'tableros.');
        if (!wishlistBoardsContainer) {
            console.error('renderWishlistBoards: wishlistBoardsContainer NO ENCONTRADO para renderizar. (Ya debería haberse detectado al inicio)');
            return;
        }
        wishlistBoardsContainer.innerHTML = ''; 

        if (boards.length === 0) {
            wishlistBoardsContainer.innerHTML = '<p class="placeholder-text">No tienes tableros de wishlist aún. ¡Crea uno!</p>';
            console.log('renderWishlistBoards: No hay tableros para renderizar.');
            return;
        }

        boards.forEach(board => {
            console.log("DEBUG (renderWishlistBoards): Procesando tablero:", board);

            const boardId = (board.id !== null && board.id !== undefined && board.id !== '') ? String(board.id) : null;
            const boardName = htmlspecialchars(board.name);
            const boardDescription = htmlspecialchars(board.description || 'Sin descripción');
            const itemCount = htmlspecialchars(board.item_count);

            if (!boardId || boardId === '0') { 
                console.error("ERROR CRÍTICO (renderWishlistBoards): El tablero recibido no tiene un 'id' válido o es '0'. Objeto de tablero:", board);
                window.showMessage(`Error: Un tablero no tiene ID válido. Por favor, revisa la base de datos o el modelo PHP.`, 'error');
                return; 
            }

            const boardCard = document.createElement('div');
            boardCard.classList.add('board-card');
            boardCard.dataset.boardId = boardId; 
            boardCard.innerHTML = `
                <h4>${boardName}</h4>
                <p>${boardDescription}</p>
                <p>Items: ${itemCount}</p>
                <div class="board-actions">
                    <button class="btn btn-secondary view-board-btn" data-board-id="${boardId}" data-board-name="${boardName}">Ver Tablero</button>
                    <button class="btn btn-tertiary delete-board-btn" data-board-id="${boardId}">Eliminar</button>
                </div>
            `;
            wishlistBoardsContainer.appendChild(boardCard);
            console.log('renderWishlistBoards: Tablero añadido:', board.name, 'con data-board-id:', boardId);
        });

        attachBoardActionListeners();
        console.log('renderWishlistBoards: Listeners adjuntados a los botones de tableros.');
    }

    /** Adjunta listeners a los botones de acciones de tableros (ver, eliminar) */
    function attachBoardActionListeners() {
        document.querySelectorAll('.view-board-btn').forEach(button => {
            button.removeEventListener('click', handleViewBoardClick);
            button.addEventListener('click', handleViewBoardClick);
        });

        document.querySelectorAll('.delete-board-btn').forEach(button => {
            button.removeEventListener('click', handleDeleteBoardClick);
            button.addEventListener('click', handleDeleteBoardClick);
        });
    }

    function handleViewBoardClick(e) {
        const boardId = e.target.dataset.boardId; 
        const boardName = e.target.dataset.boardName;
        console.log('DEBUG (handleViewBoardClick): Botón "Ver Tablero" clicado. data-board-id capturado del botón:', boardId, 'data-board-name:', boardName); 
        
        if (!boardId || boardId === '0' || boardId.trim() === '') { 
            console.error("ERROR: handleViewBoardClick: boardId es nulo, vacío, '0' o solo espacios después de leer data-board-id. No se puede cargar ítems.");
            window.showMessage('Error: No se pudo obtener el ID del tablero para ver los ítems. (boardId vacío)', 'error');
            return;
        }
        window.loadWishlistItems(boardId, boardName); 
    }

    async function handleDeleteBoardClick(e) {
        const boardId = e.target.dataset.boardId;
        if (await window.customConfirm(
            'Eliminar Tablero', 
            '¿Estás seguro de que quieres eliminar este tablero y todos sus productos? Esta acción es irreversible.'
        )) {
            await deleteWishlistBoard(boardId);
        }
    }


    /**
     * Carga y renderiza los ítems de un tablero específico de la wishlist.
     * Expuesta globalmente para ser llamada.
     */
    window.loadWishlistItems = async function(boardId, boardName) {
        showWishlistItemsView(boardName); 

        if (!currentBoardItemsContainer) {
            console.error('loadWishlistItems: currentBoardItemsContainer NO ENCONTRADO. No se pueden cargar ítems. Verifica tu HTML.');
            window.showMessage('Error interno: Contenedor de ítems de wishlist no disponible.', 'error');
            return;
        }
        currentBoardItemsContainer.innerHTML = ''; 
        
        const userId = getUserId();
        console.log(`DEBUG (loadWishlistItems): Valores antes de la petición: boardId="${boardId}" (tipo: ${typeof boardId}), userId="${userId}" (tipo: ${typeof userId})`); 
        
        if (!boardId || boardId === '0' || boardId.trim() === '' || !userId || userId.trim() === '') { 
            console.error("CRÍTICO (loadWishlistItems): boardId o userId son nulos/vacíos/inválidos (o '0') antes de la petición get_board_items. boardId:", boardId, "userId:", userId);
            currentBoardItemsContainer.innerHTML = '<p class="placeholder-text" style="color: red;">Error: No se pudo obtener el ID del tablero o el ID de usuario para cargar los ítems.</p>';
            window.showMessage('Error interno: Faltan datos para cargar los ítems de la wishlist.', 'error');
            return;
        }

        try {
            const apiUrl = `api/wishlist.php?action=get_board_items&board_id=${encodeURIComponent(boardId)}&user_id=${encodeURIComponent(userId)}`;
            console.log('DEBUG (loadWishlistItems): URL de la API para ítems:', apiUrl);
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: { 'X-User-ID': userId } 
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }

            const data = await response.json();
            console.log('wishlist.js: loadWishlistItems - API Response:', data);

            if (data.success) {
                renderWishlistItems(data.items, boardId, boardName);
            } else {
                currentBoardItemsContainer.innerHTML = `<p class="placeholder-text" style="color: red;">Error al cargar ítems: ${htmlspecialchars(data.message)}</p>`;
                window.showMessage('Error al cargar ítems del tablero: ' + data.message, 'error');
                console.error('wishlist.js: Error fetching board items from API:', data.message);
            }
        }
        catch (error) {
            console.error('wishlist.js: Error de conexión/respuesta al cargar ítems del tablero:', error);
            currentBoardItemsContainer.innerHTML = '<p class="placeholder-text" style="color: red;">Error de conexión al servidor al cargar ítems del tablero. Consulta la consola para más detalles.</p>';
            window.showMessage('Error de conexión al servidor al cargar ítems.', 'error');
        }
    }

    /**
     * Renderiza los ítems del tablero en el contenedor de ítems.
     * @param {Array} items Array de objetos de ítems de wishlist.
     * @param {string} boardId ID del tablero actual.
     * @param {string} boardName Nombre del tablero actual.
     */
    function renderWishlistItems(items, boardId, boardName) { 
        console.log('wishlist.js: renderWishlistItems - Iniciando renderizado de', items.length, 'ítems.');
        if (!currentBoardItemsContainer) {
            console.error('renderWishlistItems: currentBoardItemsContainer NO ENCONTRADO para renderizar ítems. (Ya debería haberse detectado al inicio)');
            return;
        }
        currentBoardItemsContainer.innerHTML = '';

        if (items.length === 0) {
            currentBoardItemsContainer.innerHTML = '<p class="placeholder-text">Este tablero está vacío. ¡Añade productos!</p>';
            console.log('renderWishlistItems: No hay ítems para renderizar.');
            return;
        }

        items.forEach(item => {
            const product = item.product || {}; 
            const imageUrl = (product.imageUrl && typeof product.imageUrl === 'string' && product.imageUrl.trim() !== '' && product.imageUrl.toLowerCase() !== 'null') 
                             ? product.imageUrl 
                             : 'https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';

            // Construir la URL de la página de detalle del producto
            const productDetailUrl = `product_detail.php?id=${htmlspecialchars(product.id)}`;

            const itemCard = document.createElement('div');
            itemCard.classList.add('product-card'); 
            itemCard.innerHTML = `
                <a href="${productDetailUrl}" class="product-link-wrapper">
                    <img src="${htmlspecialchars(imageUrl)}" alt="${htmlspecialchars(product.name || 'Producto')}" class="product-img" onerror="this.onerror=null;this.src='https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';this.classList.add('error-image');">
                    <div class="product-content">
                        <h4 class="product-name">${htmlspecialchars(product.name || 'Producto Desconocido')}</h4>
                    </div>
                </a>
                <p class="product-price">${formatPrice(product.price)}</p>
                <p class="item-notes">${htmlspecialchars(item.notes || 'Sin notas')}</p>
                <div class="product-actions">
                    <button class="btn btn-primary add-to-cart-btn-wishlist"
                        data-product-id="${htmlspecialchars(product.id)}"
                        data-product-name="${htmlspecialchars(product.name)}"
                        data-product-price="${htmlspecialchars(product.price)}"
                        data-product-image="${htmlspecialchars(imageUrl)}">Añadir al Carrito</button>
                    <button class="btn btn-tertiary remove-from-wishlist-btn"
                        data-item-id="${htmlspecialchars(item.id)}" 
                        data-board-id="${htmlspecialchars(boardId)}"
                        data-product-id="${htmlspecialchars(product.id)}"
                        data-board-name="${htmlspecialchars(boardName)}">Eliminar de Wishlist</button>
                </div>
            `;
            currentBoardItemsContainer.appendChild(itemCard);
            console.log('renderWishlistItems: Ítem añadido:', product.name || 'Producto Desconocido');
        });

        attachWishlistItemsListeners(); 
        console.log('renderWishlistItems: Listeners adjuntados a los ítems de wishlist.');
    }

    /** Abre el modal para crear un nuevo tablero de wishlist. */
    function openCreateBoardModal() {
        if (!createBoardModal) {
            window.showMessage('Error interno: Modal para crear tablero no disponible.', 'error');
            return;
        }
        console.log('wishlist.js: Abriendo modal de crear tablero.');
        window.openModal(createBoardModal); 
        if (createBoardForm) createBoardForm.reset(); 
    }

    /** Envía la solicitud para crear un nuevo tablero de wishlist. */
    async function createWishlistBoard(name, description) {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Debes iniciar sesión para crear tableros.', 'info');
            return;
        }
        console.log('wishlist.js: createWishlistBoard - Enviando solicitud para crear tablero para user_id:', userId, 'name:', name); 
        window.showMessage('Creando nuevo tablero...', 'info');

        try {
            const response = await fetch('api/wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-User-ID': userId },
                body: JSON.stringify({ action: 'create_board', user_id: userId, name: name, description: description })
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            console.log('wishlist.js: createBoard - API Response:', data); 

            if (data.success) {
                window.showMessage(data.message || 'Tablero creado con éxito.', 'success');
                window.closeModal(createBoardModal); 
                if (newBoardNameInput) newBoardNameInput.value = ''; 
                if (newBoardDescriptionInput) newBoardDescriptionInput.value = ''; 
                window.loadWishlistBoards(); 
                window.populateBoardSelect(); 
            } else {
                window.showMessage('Error al crear tablero: ' + data.message, 'error');
                console.error('wishlist.js: Error creating board via API:', data.message);
            }
        } catch (error) {
            console.error('wishlist.js: Error de conexión/respuesta al crear tablero:', error);
            window.showMessage('Error de conexión al servidor al crear tablero.', 'error');
        }
    }

    async function deleteWishlistBoard(boardId) {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Debes iniciar sesión para eliminar tableros.', 'info');
            return;
        }
        console.log('wishlist.js: deleteWishlistBoard - Intentando eliminar tablero', boardId, 'para user_id:', userId);
        window.showMessage('Eliminando tablero...', 'info');

        try {
            const response = await fetch('api/wishlist.php', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-User-ID': userId },
                body: JSON.stringify({ action: 'delete_board', board_id: boardId, user_id: userId })
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            console.log('wishlist.js: deleteBoard - API Response:', data); 

            if (data.success) {
                window.showMessage(data.message || 'Tablero eliminado con éxito.', 'success');
                window.loadWishlistBoards(); 
            } else {
                window.showMessage('Error al eliminar tablero: ' + data.message, 'error');
                console.error('wishlist.js: Error deleting board via API:', data.message);
            }
        } catch (error) {
            console.error('wishlist.js: Error de conexión/respuesta al eliminar tablero:', error);
            window.showMessage('Error de conexión al servidor al eliminar tablero.', 'error');
        }
    }

    async function deleteWishlistItem(boardId, productId, boardName) { 
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Debes iniciar sesión para eliminar productos de la wishlist.', 'info');
            return;
        }
        console.log('wishlist.js: deleteWishlistItem - Eliminando product', productId, 'del tablero', boardId, 'para user_id:', userId);
        window.showMessage('Eliminando producto de la wishlist...', 'info');

        try {
            const response = await fetch('api/wishlist.php', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'X-User-ID': userId },
                body: JSON.stringify({ action: 'remove_item', board_id: boardId, product_id: productId, user_id: userId })
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            console.log('wishlist.js: removeItem - API Response:', data); 

            if (data.success) {
                window.showMessage(data.message || 'Producto eliminado de la wishlist.', 'success');
                // Si el usuario está viendo un tablero específico, actualiza esa vista.
                // Si está en la vista de todos los tableros, se actualizará con loadWishlistBoards.
                if (wishlistItemsSection && wishlistItemsSection.style.display === 'block' && currentBoardNameDisplay) {
                    const currentBoardId = boardSelect.value; 
                    const currentBoardName = boardSelect.options[boardSelect.selectedIndex].text; 
                    window.loadWishlistItems(currentBoardId, currentBoardName); 
                }
                window.loadWishlistBoards(); // Recarga la lista de tableros para actualizar conteos
            } else {
                window.showMessage('Error al eliminar producto de wishlist: ' + data.message, 'error');
                console.error('wishlist.js: Error deleting wishlist item via API:', data.message);
            }
        } catch (error) {
            console.error('wishlist.js: Error de conexión/respuesta al eliminar ítem de wishlist:', error);
            window.showMessage('Error de conexión al servidor al eliminar ítem.', 'error');
        }
    }

    /** Prepara y abre el modal para añadir un producto a la wishlist. */
    window.prepareAddToWishlistModal = async (product) => { 
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Inicia sesión para añadir productos a tu Wishlist Mágica.', 'info');
            return;
        }
        
        console.log('wishlist.js: prepareAddToWishlistModal llamado. Producto:', product);

        if (!addProductToWishlistModal) {
            window.showMessage('Error interno: Modal para añadir a wishlist no disponible.', 'error');
            return;
        }
        
        // Asegurarse de que el product ID es válido antes de abrir el modal
        if (!product || !product.id || product.id.trim() === '') {
            console.error('ERROR: prepareAddToWishlistModal recibió un producto sin ID o con ID vacío:', product);
            window.showMessage('Error interno: No se pudo obtener la información completa del producto para añadir a la wishlist.', 'error');
            return;
        }

        window.openModal(addProductToWishlistModal); 

        // Rellenar los datos del modal de forma segura
        if (wishlistProductImage) wishlistProductImage.src = product?.imageUrl || 'https://placehold.co/100x100/8A2BE2/5C2E7E?text=Product';
        if (wishlistProductName) wishlistProductName.textContent = product?.name || 'Producto Desconocido';
        if (wishlistProductPrice) wishlistProductPrice.textContent = formatPrice(product?.price);
        if (wishlistProductIdInput) wishlistProductIdInput.value = product?.id || '';
        if (wishlistProductActualPriceInput) wishlistProductActualPriceInput.value = product?.price || 0; 
        if (wishlistProductActualImageInput) wishlistProductActualImageInput.value = product?.imageUrl || ''; 
        if (itemNotes) itemNotes.value = ''; 

        await populateBoardSelect(); 
        console.log('wishlist.js: Modal Añadir a Wishlist preparado y abierto.');
    };

    /** Popula el selector de tableros dentro del modal de añadir producto. */
    window.populateBoardSelect = async function() { 
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) { 
            console.warn('populateBoardSelect: userId o autenticación no disponibles. No se puede poblar el select.');
            if (boardSelect) {
                boardSelect.innerHTML = '<option value="" disabled>Inicia sesión o crea tableros</option>';
                boardSelect.disabled = true;
            }
            return;
        }

        if (!boardSelect) {
            console.error('ERROR CRÍTICO (populateBoardSelect): Elemento #boardSelect NO ENCONTRADO.');
            window.showMessage('Error interno: Selector de tableros no disponible.', 'error');
            return;
        }

        boardSelect.innerHTML = '<option value="">Cargando tableros...</option>';
        boardSelect.disabled = true; 
        try {
            const apiUrl = `api/wishlist.php?action=get_boards&user_id=${encodeURIComponent(userId)}`;
            console.log('DEBUG (populateBoardSelect): URL de la API para poblar select:', apiUrl);
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: { 'X-User-ID': userId }
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            console.log('wishlist.js: populateBoardSelect - API Response:', data);

            if (data.success && data.boards.length > 0) {
                boardSelect.innerHTML = '<option value="">Selecciona un tablero</option>';
                data.boards.forEach(board => {
                    const optionValue = (board.id !== null && board.id !== undefined && board.id !== '') ? String(board.id) : '';
                    if (optionValue !== '') {
                        const option = document.createElement('option');
                        option.value = optionValue;
                        option.textContent = htmlspecialchars(board.name);
                        boardSelect.appendChild(option);
                    } else {
                        console.error("ERROR: populateBoardSelect: Tablero con ID inválido encontrado:", board);
                    }
                });
                if (boardSelect.options.length > 1) { 
                    boardSelect.disabled = false;
                } else {
                    boardSelect.innerHTML = '<option value="" disabled>No tienes tableros. Crea uno primero.</option>';
                    boardSelect.disabled = true;
                    window.showMessage('No tienes tableros de wishlist. Crea uno para añadir productos.', 'info');
                }
            } else {
                boardSelect.innerHTML = '<option value="" disabled>No tienes tableros. Crea uno primero.</option>';
                boardSelect.disabled = true; 
                window.showMessage('No tienes tableros de wishlist. Crea uno para añadir productos.', 'info');
                console.log('populateBoardSelect: No hay tableros o error en la API.');
            }
        } catch (error) {
            console.error('wishlist.js: Error de conexión/respuesta al poblar select de tableros:', error);
            boardSelect.innerHTML = '<option value="" disabled>Error al cargar tableros</option>';
            boardSelect.disabled = true;
            window.showMessage('Error de conexión al servidor al cargar tableros (select).', 'error');
        }
    };

    /** Envía el producto seleccionado al tablero de wishlist elegido. */
    async function addProductToSelectedWishlist() {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Debes iniciar sesión para añadir productos a wishlist.', 'info');
            return;
        }

        const boardId = boardSelect?.value || '';
        const productId = wishlistProductIdInput?.value || '';
        const notes = itemNotes?.value || '';
        const productName = wishlistProductName?.textContent || ''; 
        const productPrice = parseFloat(wishlistProductActualPriceInput?.value) || 0; 
        const productImage = wishlistProductActualImageInput?.value || ''; 

        console.log(`DEBUG (addProductToSelectedWishlist): Datos del producto a enviar:
            boardId: "${boardId}" (tipo: ${typeof boardId})
            productId: "${productId}" (tipo: ${typeof productId})
            productName: "${productName}" (tipo: ${typeof productName})
            productPrice: "${productPrice}" (tipo: ${typeof productPrice})
            productImage: "${productImage}" (tipo: ${typeof productImage})
            notes: "${notes}" (tipo: ${typeof notes})
            userId: "${userId}" (tipo: ${typeof userId})`);


        if (!boardId || boardId.trim() === '') {
            window.showMessage('Por favor, selecciona un tablero.', 'error');
            return;
        }
        if (!productId || productId.trim() === '') {
             window.showMessage('Error interno: ID de producto no encontrado para añadir a wishlist.', 'error');
             console.error('addProductToSelectedWishlist: productId es nulo o vacío.');
             return;
        }
        if (!productName || productName.trim() === '' || !productPrice || isNaN(productPrice) || !productImage || productImage.trim() === '') {
            window.showMessage('Error interno: Datos completos del producto (nombre, precio, imagen) no disponibles para añadir a wishlist. Intenta de nuevo.', 'error');
            console.error('addProductToSelectedWishlist: Faltan datos de producto (name, price, image) o precio inválido.', {productName, productPrice, productImage});
            return;
        }


        console.log('wishlist.js: addProductToSelectedWishlist - Enviando a API...');
        window.showMessage('Añadiendo producto a la wishlist...', 'info');

        try {
            const response = await fetch('api/wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-User-ID': userId },
                body: JSON.stringify({ 
                    action: 'add_item', 
                    board_id: boardId, 
                    product_id: productId, 
                    notes: notes, 
                    user_id: userId,
                    product_name: productName,    
                    product_price: productPrice,  
                    product_image: productImage   
                })
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            console.log('wishlist.js: addItem - API Response:', data); 

            if (data.success) {
                window.showMessage(data.message || 'Producto añadido a la wishlist con éxito.', 'success');
                window.closeModal(addProductToWishlistModal); 
                // Si el usuario está viendo un tablero específico, actualiza esa vista.
                // Si está en la vista de todos los tableros, se actualizará con loadWishlistBoards.
                if (wishlistItemsSection && wishlistItemsSection.style.display === 'block' && currentBoardNameDisplay) {
                    const currentBoardId = boardSelect.value; 
                    const currentBoardName = boardSelect.options[boardSelect.selectedIndex].text; 
                    window.loadWishlistItems(currentBoardId, currentBoardName); 
                }
                window.loadWishlistBoards(); // Recarga la lista de tableros para actualizar conteos
            } else {
                window.showMessage('Error al añadir a wishlist: ' + data.message, 'error');
                console.error('wishlist.js: Error adding product to wishlist via API:', data.message);
            }
        } catch (error) {
            console.error('wishlist.js: Error de conexión/respuesta al añadir producto a wishlist:', error);
            window.showMessage('Error de conexión al servidor al añadir a wishlist.', 'error');
        }
    }


    // ===============================================
    // Listeners de Eventos (conectando los elementos a las funciones)
    // ===============================================

    // myWishlistLink (acceso directo desde mini-menú) fue eliminado/comentado en el HTML.
    // Por lo tanto, no hay listener para él aquí.

    // Listener para la pestaña "Mi Wishlist" en el modal "Mi Cuenta"
    // Este listener es el CRÍTICO ahora para la carga
    if (wishlistTabBtn) {
        wishlistTabBtn.addEventListener('click', () => {
            console.log('wishlist.js: Clic en pestaña "Mi Wishlist".');
            // Asegurarse de que el panel de la wishlist sea visible antes de cargar los tableros
            if (wishlistBoardsSection) {
                wishlistBoardsSection.style.display = 'block'; 
                // Forzar el redibujado (reflow) INMEDIATAMENTE DESPUÉS de hacer visible
                // Acceder a una propiedad de layout como offsetHeight fuerza al navegador a recalcular.
                wishlistBoardsSection.offsetHeight; 
                console.log('wishlist.js: Forced reflow on wishlistBoardsSection via wishlistTabBtn click.');
            }

            // Cargar los tableros. Un setTimeout mínimo de 1ms
            // permite que el navegador complete el ciclo de eventos actual (actualizar display y reflow)
            // antes de iniciar la carga asíncrona.
            setTimeout(() => {
                window.loadWishlistBoards(); 
            }, 1); 
        });
    }

    // Listeners para el modal "Crear Tablero"
    if (createBoardBtn) createBoardBtn.addEventListener('click', openCreateBoardModal);
    if (closeCreateBoardModalBtn) closeCreateBoardModalBtn.addEventListener('click', () => window.closeModal(createBoardModal));
    if (cancelCreateBoardBtn) cancelCreateBoardBtn.addEventListener('click', () => window.closeModal(createBoardModal));
    if (createBoardForm) {
        createBoardForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = newBoardNameInput ? newBoardNameInput.value.trim() : '';
            const description = newBoardDescriptionInput ? newBoardDescriptionInput.value.trim() : '';
            if (name && name.trim() !== '') { 
                createWishlistBoard(name, description);
            } else {
                window.showMessage('El nombre del tablero no puede estar vacío.', 'error');
            }
        });
    }

    // Listeners para el modal "Añadir Producto a Wishlist"
    if (openCreateBoardLink) {
        openCreateBoardLink.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('wishlist.js: Clic en "Crear nuevo tablero" desde el modal de añadir producto.');
            window.closeModal(addProductToWishlistModal); 
            openCreateBoardModal(); 
        });
    }

    if (closeAddProductToWishlistModalBtn) closeAddProductToWishlistModalBtn.addEventListener('click', () => window.closeModal(addProductToWishlistModal));
    if (cancelAddToWishlistBtn) cancelAddToWishlistBtn.addEventListener('click', () => window.closeModal(addProductToWishlistModal));

    if (addProductToWishlistForm) {
        addProductToWishlistForm.addEventListener('submit', (e) => {
            e.preventDefault();
            addProductToSelectedWishlist();
        });
    }

    // Listener para el botón "Volver a Mis Tableros"
    if (backToBoardsBtn) {
        backToBoardsBtn.addEventListener('click', () => {
            console.log('wishlist.js: Clic en "Volver a Mis Tableros".');
            showWishlistBoardsView(); 
            // Cargar los tableros después de un retraso mínimo
            setTimeout(() => {
                window.loadWishlistBoards(); 
            }, 1); 
        });
    }

    /** Adjunta listeners a los botones de ítems de wishlist (añadir al carrito, eliminar de wishlist) */
    function attachWishlistItemsListeners() {
        document.querySelectorAll('.add-to-cart-btn-wishlist').forEach(button => {
            button.removeEventListener('click', handleAddToCartFromWishlistClick); 
            button.addEventListener('click', handleAddToCartFromWishlistClick);
        });

        document.querySelectorAll('.remove-from-wishlist-btn').forEach(button => {
            button.removeEventListener('click', handleRemoveFromWishlistClick); 
            button.addEventListener('click', handleRemoveFromWishlistClick);
        });
    }

    function handleAddToCartFromWishlistClick() {
        const product = {
            id: this.dataset.productId,
            name: this.dataset.productName,
            price: parseFloat(this.dataset.productPrice),
            imageUrl: this.dataset.productImage
        };
        const quantity = 1; 

        if (window.addToCart) {
            window.addToCart(product, quantity);
        } else {
            window.showMessage("Error: No se pudo añadir al carrito desde la wishlist. (Función no encontrada)", 'error');
            console.error("cart.js no cargado o window.addToCart no definido.");
        }
    }

    async function handleRemoveFromWishlistClick() {
        const boardId = this.dataset.boardId; 
        const productId = this.dataset.productId; 
        const boardName = this.dataset.boardName; 

        if (await window.customConfirm(
            'Eliminar Producto', 
            '¿Estás seguro de que quieres eliminar este producto de la wishlist?'
        )) { 
            await deleteWishlistItem(boardId, productId, boardName); 
        }
    }

    /** * Adjunta listeners a los botones "Añadir a Wishlist" de las tarjetas de producto.
     * Esta función debe ser llamada por products.js y product_detail_page.js
     * cada vez que rendericen productos para asegurar que los botones sean interactivos.
     */
    window.attachAddToWishlistListeners = function() {
        document.querySelectorAll(
            '.add-to-wishlist-btn, .add-to-wishlist-btn-page, .add-to-wishlist-btn-related'
        ).forEach(button => {
            button.removeEventListener('click', handleAddToWishlistClick); 
            button.addEventListener('click', handleAddToWishlistClick);
        });
    };

    /** Manejador de clic para los botones "Añadir a Wishlist". */
    function handleAddToWishlistClick() {
        const product = {
            id: this.dataset.productId,
            name: this.dataset.productName,
            price: parseFloat(this.dataset.productPrice),
            imageUrl: this.dataset.productImage
        };
        console.log("DEBUG (handleAddToWishlistClick): Producto preparado para modal:", product);
        window.prepareAddToWishlistModal(product);
    }

    // Listener global para el cambio de estado de autenticación
    window.addEventListener('authStatusChanged', (event) => {
        console.log('wishlist.js: Evento authStatusChanged detectado. Usuario autenticado:', event.detail.isAuthenticated);
        if (event.detail.isAuthenticated) {
            console.log("DEBUG: Usuario autenticado. Recargando tableros de wishlist.");
            const myAccountModalElement = getElementByIdSafe('myAccountModal'); 
            const wishlistTabBtnElement = getElementByIdSafe('wishlistTabBtn');

            if (myAccountModalElement && wishlistTabBtnElement && myAccountModalElement.style.display === 'block' && wishlistTabBtnElement.classList.contains('active')) {
                // Forzar reflow también aquí si el modal ya está abierto y en la pestaña wishlist
                if (wishlistBoardsSection) {
                    wishlistBoardsSection.style.display = 'block'; 
                    wishlistBoardsSection.offsetHeight; // Fuerza el reflow
                    console.log('wishlist.js: Forced reflow on wishlistBoardsSection via authStatusChanged.');
                }
                 setTimeout(() => {
                    window.loadWishlistBoards();
                 }, 1); 
            } else if (myAccountModalElement && myAccountModalElement.style.display === 'block') {
                 console.log("DEBUG: Modal de cuenta abierto pero no en pestaña wishlist. No recargando tableros automáticamente.");
            } else {
                 console.log("DEBUG: Modal de cuenta no abierto. No recargando tableros automáticamente.");
            }

        } else {
            if (wishlistBoardsContainer) wishlistBoardsContainer.innerHTML = '<p class="placeholder-text">Inicia sesión para ver tus tableros.</p>';
            if (wishlistItemsSection) wishlistItemsSection.style.display = 'none';
            if (wishlistBoardsSection) wishlistBoardsSection.style.display = 'block'; // Asegura que el placeholder se vea
            console.log("DEBUG: Usuario desautenticado. Limpiando vista de wishlist.");
        }
    });

}); // Fin DOMContentLoaded

