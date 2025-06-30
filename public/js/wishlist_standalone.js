// public/js/wishlist_standalone.js
console.log('wishlist_standalone.js cargado.');

document.addEventListener('DOMContentLoaded', () => {
    // ===============================================
    // 1. Obtención de Elementos del DOM (¡NUEVOS IDs!)
    // ===============================================
    const standaloneWishlistModal = document.getElementById('standaloneWishlistModal');
    const closeStandaloneWishlistModalBtn = document.getElementById('closeStandaloneWishlistModalBtn');
    const openStandaloneWishlistLink = document.getElementById('openStandaloneWishlistLink'); // Enlace en el header/dropdown

    // Secciones y contenedores principales de la wishlist standalone
    const standaloneWishlistBoardsSection = document.getElementById('standaloneWishlistBoardsSection');
    const standaloneWishlistBoardsContainer = document.getElementById('standaloneWishlistBoardsContainer');
    const standaloneCreateBoardBtn = document.getElementById('standaloneCreateBoardBtn');

    const standaloneWishlistItemsSection = document.getElementById('standaloneWishlistItemsSection');
    const standaloneBackToBoardsBtn = document.getElementById('standaloneBackToBoardsBtn');
    const standaloneCurrentBoardNameDisplay = document.getElementById('standaloneCurrentBoardName');
    const standaloneCurrentBoardItemsContainer = document.getElementById('standaloneCurrentBoardItems');

    // Modales y sus elementos para crear tablero (Standalone)
    const standaloneCreateBoardModal = document.getElementById('standaloneCreateBoardModal');
    const closeStandaloneCreateBoardModalBtn = document.getElementById('closeStandaloneCreateBoardModalBtn');
    const standaloneCreateBoardForm = document.getElementById('standaloneCreateBoardForm');
    const standaloneNewBoardNameInput = document.getElementById('standaloneNewBoardName');
    const standaloneNewBoardDescriptionInput = document.getElementById('standaloneNewBoardDescription');
    const cancelStandaloneCreateBoardBtn = document.getElementById('cancelStandaloneCreateBoardBtn');

    // Modal y sus elementos para añadir producto a Wishlist (Standalone)
    const standaloneAddProductToWishlistModal = document.getElementById('standaloneAddProductToWishlistModal');
    const closeStandaloneAddProductToWishlistModalBtn = document.getElementById('closeStandaloneAddProductToWishlistModalBtn');
    const standaloneWishlistProductImage = document.getElementById('standaloneWishlistProductImage');
    const standaloneWishlistProductName = document.getElementById('standaloneWishlistProductName');
    const standaloneWishlistProductPrice = document.getElementById('standaloneWishlistProductPrice');
    const standaloneBoardSelect = document.getElementById('standaloneBoardSelect');
    const standaloneItemNotes = document.getElementById('standaloneItemNotes');
    const standaloneAddProductToWishlistForm = document.getElementById('standaloneAddProductToWishlistForm');

    // Inputs ocultos para datos del producto que se añadirán (Standalone)
    const standaloneWishlistProductIdInput = document.getElementById('standaloneWishlistProductId');
    const standaloneWishlistProductActualPriceInput = document.getElementById('standaloneWishlistProductActualPrice');
    const standaloneWishlistProductActualImageInput = document.getElementById('standaloneWishlistProductActualImage');

    const openStandaloneCreateBoardLink = document.getElementById('openStandaloneCreateBoardLink');
    const cancelStandaloneAddToWishlistBtn = document.getElementById('cancelStandaloneAddToWishlistBtn');

    // ===============================================
    // 2. Validación Inicial de Elementos Críticos (para depuración)
    // ===============================================
    if (!standaloneWishlistModal) console.error('CRÍTICO (JS Init): #standaloneWishlistModal NO ENCONTRADO.');
    if (!standaloneWishlistBoardsContainer) console.error('CRÍTICO (JS Init): #standaloneWishlistBoardsContainer NO ENCONTRADO.');
    if (!standaloneWishlistItemsSection) console.error('CRÍTICO (JS Init): #standaloneWishlistItemsSection NO ENCONTRADO.');
    if (!standaloneCurrentBoardItemsContainer) console.error('CRÍTICO (JS Init): #standaloneCurrentBoardItems NO ENCONTRADO.');
    if (!standaloneAddProductToWishlistModal) console.error('CRÍTICO (JS Init): #standaloneAddProductToWishlistModal NO ENCONTRADO.');
    if (!standaloneWishlistProductActualImageInput) console.error('CRÍTICO (JS Init): #standaloneWishlistProductActualImage NO ENCONTRADO.');
    if (!standaloneCreateBoardModal) console.error('CRÍTICO (JS Init): #standaloneCreateBoardModal NO ENCONTRADO.');
    if (!openStandaloneWishlistLink) console.warn('WARNING (JS Init): #openStandaloneWishlistLink NO ENCONTRADO.');


    // ===============================================
    // 3. Funciones Auxiliares
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

    function getUserId() {
        let userId = localStorage.getItem('userId');
        if (!userId || userId === 'null' || userId === 'undefined' || userId.startsWith('anon_')) {
            userId = 'anon_' + Math.random().toString(36).substring(2, 15);
            localStorage.setItem('userId', userId);
            console.log('wishlist_standalone.js: Asignado o actualizado ID de usuario temporal:', userId);
        }
        return userId;
    }

    // ===============================================
    // 4. Lógica de Vistas (Tableros / Items)
    // ===============================================

    /** Muestra la vista de tableros de wishlist y oculta la vista de ítems. */
    function showStandaloneWishlistBoardsView() {
        console.log('standalone: showStandaloneWishlistBoardsView - Mostrando sección de tableros.');
        if (standaloneWishlistBoardsSection) standaloneWishlistBoardsSection.style.display = 'block';
        if (standaloneWishlistItemsSection) standaloneWishlistItemsSection.style.display = 'none';
        if (standaloneCreateBoardBtn) standaloneCreateBoardBtn.style.display = 'inline-block';
    }

    /** Muestra la vista de ítems de un tablero y oculta la vista de tableros. */
    function showStandaloneWishlistItemsView(boardName) {
        console.log('standalone: showStandaloneWishlistItemsView - Mostrando sección de ítems para:', boardName);
        if (standaloneWishlistBoardsSection) standaloneWishlistBoardsSection.style.display = 'none';
        if (standaloneWishlistItemsSection) standaloneWishlistItemsSection.style.display = 'block';
        if (standaloneCreateBoardBtn) standaloneCreateBoardBtn.style.display = 'none';
        if (standaloneCurrentBoardNameDisplay) standaloneCurrentBoardNameDisplay.textContent = `Tablero: ${htmlspecialchars(boardName)}`;
    }

    // ===============================================
    // 5. Carga y Renderizado de Tableros (Wishlist Boards)
    // ===============================================

    window.loadStandaloneWishlistBoards = async () => {
        const userId = getUserId();
        console.log('standalone: loadStandaloneWishlistBoards - Intentando cargar tableros para user_id:', userId);

        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Por favor, inicia sesión para gestionar tu Wishlist Mágica.', 'info');
            if (standaloneWishlistBoardsContainer) standaloneWishlistBoardsContainer.innerHTML = '<p class="placeholder-text">Inicia sesión para ver tus tableros.</p>';
            showStandaloneWishlistBoardsView();
            return;
        }

        if (standaloneWishlistBoardsContainer) standaloneWishlistBoardsContainer.innerHTML = '<p class="placeholder-text">Cargando tableros...</p>';
        window.showMessage('Cargando tableros de wishlist...', 'info');

        try {
            const apiUrl = `api/wishlist.php?action=get_boards&user_id=${encodeURIComponent(userId)}`;
            console.log('DEBUG (standaloneLoadBoards): URL:', apiUrl);
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: { 'X-User-ID': userId }
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }

            const data = await response.json();
            console.log('standalone: loadStandaloneWishlistBoards - API Response:', data);

            if (data.success) {
                renderStandaloneWishlistBoards(data.boards);
                window.showMessage('Tableros cargados exitosamente.', 'success');
            } else {
                if (standaloneWishlistBoardsContainer) standaloneWishlistBoardsContainer.innerHTML = `<p class="placeholder-text" style="color: red;">Error al cargar tableros: ${htmlspecialchars(data.message)}</p>`;
                window.showMessage('Error al cargar tableros: ' + data.message, 'error');
                console.error('standalone: Error fetching boards from API:', data.message);
            }
        } catch (error) {
            console.error('standalone: Error de conexión/respuesta al cargar tableros:', error);
            if (standaloneWishlistBoardsContainer) standaloneWishlistBoardsContainer.innerHTML = '<p class="placeholder-text" style="color: red;">Error de conexión al servidor al cargar tableros. Consulta la consola para más detalles.</p>';
            window.showMessage('Error de conexión al servidor al cargar tableros.', 'error');
        }
    };

    /** Renderiza las tarjetas de tablero en el contenedor de tableros. */
    function renderStandaloneWishlistBoards(boards) {
        console.log('standalone: renderStandaloneWishlistBoards - Iniciando renderizado de', boards.length, 'tableros.');
        if (!standaloneWishlistBoardsContainer) {
            console.error('renderStandaloneWishlistBoards: standaloneWishlistBoardsContainer NO ENCONTRADO.');
            return;
        }
        standaloneWishlistBoardsContainer.innerHTML = '';

        if (boards.length === 0) {
            standaloneWishlistBoardsContainer.innerHTML = '<p class="placeholder-text">No tienes tableros de wishlist aún. ¡Crea uno!</p>';
            console.log('standalone: No hay tableros para renderizar.');
            return;
        }

        boards.forEach(board => {
            console.log("DEBUG (renderStandaloneWishlistBoards): Procesando tablero:", board);

            // Asegurarse de que board.id es una cadena y no es nula/vacía/cero
            const boardId = (board.id !== null && board.id !== undefined && board.id !== '') ? String(board.id) : null;
            const boardName = htmlspecialchars(board.name);
            const boardDescription = htmlspecialchars(board.description || 'Sin descripción');
            const itemCount = htmlspecialchars(board.item_count);

            if (!boardId || boardId === '0') {
                console.error("ERROR CRÍTICO (renderStandaloneWishlistBoards): El tablero recibido no tiene un 'id' válido o es '0'. Objeto de tablero:", board);
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
                    <button class="btn btn-secondary standalone-view-board-btn" data-board-id="${boardId}" data-board-name="${boardName}">Ver Tablero</button>
                    <button class="btn btn-tertiary standalone-delete-board-btn" data-board-id="${boardId}">Eliminar</button>
                </div>
            `;
            standaloneWishlistBoardsContainer.appendChild(boardCard);
            console.log('standalone: Tablero añadido:', board.name, 'con data-board-id:', boardId);
        });

        attachStandaloneBoardActionListeners();
        console.log('standalone: Listeners adjuntados a los botones de tableros.');
    }

    /** Adjunta listeners a los botones de acciones de tableros (ver, eliminar) */
    function attachStandaloneBoardActionListeners() {
        document.querySelectorAll('.standalone-view-board-btn').forEach(button => {
            button.removeEventListener('click', handleStandaloneViewBoardClick);
            button.addEventListener('click', handleStandaloneViewBoardClick);
        });

        document.querySelectorAll('.standalone-delete-board-btn').forEach(button => {
            button.removeEventListener('click', handleStandaloneDeleteBoardClick);
            button.addEventListener('click', handleStandaloneDeleteBoardClick);
        });
    }

    function handleStandaloneViewBoardClick(e) {
        const boardId = e.target.dataset.boardId;
        const boardName = e.target.dataset.boardName;
        console.log('DEBUG (handleStandaloneViewBoardClick): Clic en "Ver Tablero". boardId capturado:', boardId, 'boardName:', boardName);

        if (!boardId || boardId === '0' || boardId.trim() === '') {
            console.error("ERROR: handleStandaloneViewBoardClick: boardId es nulo, vacío, '0' o solo espacios. No se puede cargar ítems.");
            window.showMessage('Error: No se pudo obtener el ID del tablero para ver los ítems. (boardId vacío)', 'error');
            return;
        }
        window.loadStandaloneWishlistItems(boardId, boardName);
    }

    async function handleStandaloneDeleteBoardClick(e) {
        const boardId = e.target.dataset.boardId;
        if (window.confirm('¿Estás seguro de que quieres eliminar este tablero y todos sus productos? Esta acción es irreversible.')) {
            await deleteStandaloneWishlistBoard(boardId);
        }
    }

    // ===============================================
    // 6. Carga y Renderizado de Ítems (Wishlist Items)
    // ===============================================

    window.loadStandaloneWishlistItems = async function(boardId, boardName) {
        showStandaloneWishlistItemsView(boardName);

        if (!standaloneCurrentBoardItemsContainer) {
            console.error('loadStandaloneWishlistItems: standaloneCurrentBoardItemsContainer NO ENCONTRADO.');
            window.showMessage('Error interno: Contenedor de ítems de wishlist no disponible.', 'error');
            return;
        }
        standaloneCurrentBoardItemsContainer.innerHTML = '<p class="placeholder-text">Cargando ítems del tablero...</p>';
        window.showMessage(`Cargando ítems de "${boardName}"...`, 'info');

        const userId = getUserId();
        console.log(`DEBUG (loadStandaloneWishlistItems): Valores antes de la petición: boardId="${boardId}" (tipo: ${typeof boardId}), userId="${userId}" (tipo: ${typeof userId})`);

        if (!boardId || boardId === '0' || boardId.trim() === '' || !userId || userId.trim() === '') {
            console.error("CRÍTICO (loadStandaloneWishlistItems): boardId o userId son nulos/vacíos/inválidos (o '0') antes de la petición get_board_items. boardId:", boardId, "userId:", userId);
            standaloneCurrentBoardItemsContainer.innerHTML = '<p class="placeholder-text" style="color: red;">Error: No se pudo obtener el ID del tablero o el ID de usuario para cargar los ítems.</p>';
            window.showMessage('Error interno: Faltan datos para cargar los ítems de la wishlist.', 'error');
            return;
        }

        try {
            const apiUrl = `api/wishlist.php?action=get_board_items&board_id=${encodeURIComponent(boardId)}&user_id=${encodeURIComponent(userId)}`;
            console.log('DEBUG (loadStandaloneWishlistItems): URL de la API para ítems:', apiUrl);
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: { 'X-User-ID': userId }
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }

            const data = await response.json();
            console.log('standalone: loadStandaloneWishlistItems - API Response:', data);

            if (data.success) {
                renderStandaloneWishlistItems(data.items, boardId, boardName);
                window.showMessage('Ítems del tablero cargados exitosamente.', 'success');
            } else {
                standaloneCurrentBoardItemsContainer.innerHTML = `<p class="placeholder-text" style="color: red;">Error al cargar ítems: ${htmlspecialchars(data.message)}</p>`;
                window.showMessage('Error al cargar ítems del tablero: ' + data.message, 'error');
                console.error('standalone: Error fetching board items from API:', data.message);
            }
        }
        catch (error) {
            console.error('standalone: Error de conexión/respuesta al cargar ítems del tablero:', error);
            standaloneCurrentBoardItemsContainer.innerHTML = '<p class="placeholder-text" style="color: red;">Error de conexión al servidor al cargar ítems del tablero. Consulta la consola para más detalles.</p>';
            window.showMessage('Error de conexión al servidor al cargar ítems.', 'error');
        }
    }

    /** Renderiza los ítems del tablero en el contenedor de ítems. */
    function renderStandaloneWishlistItems(items, boardId, boardName) {
        console.log('standalone: renderStandaloneWishlistItems - Iniciando renderizado de', items.length, 'ítems.');
        if (!standaloneCurrentBoardItemsContainer) {
            console.error('renderStandaloneWishlistItems: standaloneCurrentBoardItemsContainer NO ENCONTRADO.');
            return;
        }
        standaloneCurrentBoardItemsContainer.innerHTML = '';

        if (items.length === 0) {
            standaloneCurrentBoardItemsContainer.innerHTML = '<p class="placeholder-text">Este tablero está vacío. ¡Añade productos!</p>';
            console.log('standalone: No hay ítems para renderizar.');
            return;
        }

        items.forEach(item => {
            const product = item.product || {};
            const imageUrl = (product.imageUrl && typeof product.imageUrl === 'string' && product.imageUrl.trim() !== '' && product.imageUrl.toLowerCase() !== 'null')
                             ? product.imageUrl
                             : 'https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';

            const itemCard = document.createElement('div');
            itemCard.classList.add('product-card');
            itemCard.innerHTML = `
                <img src="${htmlspecialchars(imageUrl)}" alt="${htmlspecialchars(product.name || 'Producto')}" class="product-img" onerror="this.src='https://placehold.co/600x600/8A2BE2/5C2E7E?text=No+Image';this.classList.add('error-image');">
                <div class="product-content">
                    <h4 class="product-name">${htmlspecialchars(product.name || 'Producto Desconocido')}</h4>
                    <p class="product-price">${formatPrice(product.price)}</p>
                    <p class="item-notes">${htmlspecialchars(item.notes || 'Sin notas')}</p>
                    <div class="product-actions">
                        <button class="btn btn-primary add-to-cart-btn-wishlist"
                            data-product-id="${htmlspecialchars(product.id)}"
                            data-product-name="${htmlspecialchars(product.name)}"
                            data-product-price="${htmlspecialchars(product.price)}"
                            data-product-image="${htmlspecialchars(imageUrl)}">Añadir al Carrito</button>
                        <button class="btn btn-tertiary standalone-remove-from-wishlist-btn"
                            data-item-id="${htmlspecialchars(item.id)}"
                            data-board-id="${htmlspecialchars(boardId)}"
                            data-product-id="${htmlspecialchars(product.id)}"
                            data-board-name="${htmlspecialchars(boardName)}">Eliminar de Wishlist</button>
                    </div>
                </div>
            `;
            standaloneCurrentBoardItemsContainer.appendChild(itemCard);
            console.log('standalone: Ítem añadido:', product.name || 'Producto Desconocido');
        });

        attachStandaloneWishlistItemsListeners();
        console.log('standalone: Listeners adjuntados a los ítems de wishlist.');
    }

    // ===============================================
    // 7. Operaciones de Creación/Eliminación (Tableros y Productos)
    // ===============================================

    /** Abre el modal para crear un nuevo tablero de wishlist. */
    function openStandaloneCreateBoardModal() {
        if (!standaloneCreateBoardModal) {
            console.warn('openStandaloneCreateBoardModal: standaloneCreateBoardModal no encontrado.');
            window.showMessage('Error interno: Modal para crear tablero no disponible.', 'error');
            return;
        }
        console.log('standalone: Abriendo modal de crear tablero.');
        window.openModal(standaloneCreateBoardModal);
        if (standaloneCreateBoardForm) standaloneCreateBoardForm.reset();
    }

    /** Envía la solicitud para crear un nuevo tablero de wishlist. */
    async function createStandaloneWishlistBoard(name, description) {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Debes iniciar sesión para crear tableros.', 'info');
            return;
        }
        console.log('standalone: createStandaloneWishlistBoard - Creando tablero para user_id:', userId, 'name:', name);
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
            console.log('standalone: createBoard - API Response:', data);

            if (data.success) {
                window.showMessage(data.message || 'Tablero creado con éxito.', 'success');
                window.closeModal(standaloneCreateBoardModal);
                if (standaloneNewBoardNameInput) standaloneNewBoardNameInput.value = '';
                if (standaloneNewBoardDescriptionInput) standaloneNewBoardDescriptionInput.value = '';
                window.loadStandaloneWishlistBoards(); // Recargar los tableros en el modal principal
                window.populateStandaloneBoardSelect(); // Recargar el selector en el modal de añadir producto
            } else {
                window.showMessage('Error al crear tablero: ' + data.message, 'error');
                console.error('standalone: Error creating board via API:', data.message);
            }
        } catch (error) {
            console.error('standalone: Error de conexión/respuesta al crear tablero:', error);
            window.showMessage('Error de conexión al servidor al crear tablero.', 'error');
        }
    }

    async function deleteStandaloneWishlistBoard(boardId) {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Debes iniciar sesión para eliminar tableros.', 'info');
            return;
        }
        console.log('standalone: deleteStandaloneWishlistBoard - Eliminando tablero', boardId, 'para user_id:', userId);
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
            console.log('standalone: deleteBoard - API Response:', data);

            if (data.success) {
                window.showMessage(data.message || 'Tablero eliminado con éxito.', 'success');
                window.loadStandaloneWishlistBoards();
                window.populateStandaloneBoardSelect();
            } else {
                window.showMessage('Error al eliminar tablero: ' + data.message, 'error');
                console.error('standalone: Error deleting board via API:', data.message);
            }
        } catch (error) {
            console.error('standalone: Error de conexión/respuesta al eliminar tablero:', error);
            window.showMessage('Error de conexión al servidor al eliminar tablero.', 'error');
        }
    }

    async function deleteStandaloneWishlistItem(boardId, productId, boardName) {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Debes iniciar sesión para eliminar productos de la wishlist.', 'info');
            return;
        }
        console.log('standalone: deleteStandaloneWishlistItem - Eliminando product', productId, 'del tablero', boardId, 'para user_id:', userId);
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
            console.log('standalone: removeItem - API Response:', data);

            if (data.success) {
                window.showMessage(data.message || 'Producto eliminado de la wishlist.', 'success');
                if (standaloneWishlistItemsSection && standaloneWishlistItemsSection.style.display === 'block') {
                    window.loadStandaloneWishlistItems(boardId, boardName); // Recargar ítems del tablero actual si estamos en esa vista
                }
                window.loadStandaloneWishlistBoards(); // Recargar tableros para actualizar conteos
            } else {
                window.showMessage('Error al eliminar producto de wishlist: ' + data.message, 'error');
                console.error('standalone: Error deleting wishlist item via API:', data.message);
            }
        } catch (error) {
            console.error('standalone: Error de conexión/respuesta al eliminar ítem de wishlist:', error);
            window.showMessage('Error de conexión al servidor al eliminar ítem.', 'error');
        }
    }

    /** Prepara y abre el modal para añadir un producto a la wishlist. */
    window.prepareStandaloneAddToWishlistModal = async (product) => {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Inicia sesión para añadir productos a tu Wishlist Mágica.', 'info');
            return;
        }

        console.log('standalone: prepareStandaloneAddToWishlistModal llamado. Producto:', product);

        if (!standaloneAddProductToWishlistModal) {
            console.warn('prepareStandaloneAddToWishlistModal: standaloneAddProductToWishlistModal no encontrado.');
            window.showMessage('Error interno: Modal para añadir a wishlist no disponible.', 'error');
            return;
        }

        window.openModal(standaloneAddProductToWishlistModal);

        if (standaloneWishlistProductImage) standaloneWishlistProductImage.src = product.imageUrl || 'https://placehold.co/100x100/8A2BE2/5C2E7E?text=Product';
        if (standaloneWishlistProductName) standaloneWishlistProductName.textContent = product.name;
        if (standaloneWishlistProductPrice) standaloneWishlistProductPrice.textContent = formatPrice(product.price);
        if (standaloneWishlistProductIdInput) standaloneWishlistProductIdInput.value = product.id;
        if (standaloneWishlistProductActualPriceInput) standaloneWishlistProductActualPriceInput.value = product.price;
        if (standaloneWishlistProductActualImageInput) standaloneWishlistProductActualImageInput.value = product.imageUrl;
        if (standaloneItemNotes) standaloneItemNotes.value = '';

        await window.populateStandaloneBoardSelect();
        console.log('standalone: Modal Añadir a Wishlist preparado y abierto.');
    };

    /** Popula el selector de tableros dentro del modal de añadir producto. */
    window.populateStandaloneBoardSelect = async function() {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_') || !standaloneBoardSelect) {
            console.warn('populateStandaloneBoardSelect: userId, standaloneBoardSelect o autenticación no disponibles.');
            if (standaloneBoardSelect) {
                standaloneBoardSelect.innerHTML = '<option value="" disabled>Inicia sesión o crea tableros</option>';
                standaloneBoardSelect.disabled = true;
            }
            return;
        }

        standaloneBoardSelect.innerHTML = '<option value="">Cargando tableros...</option>';
        standaloneBoardSelect.disabled = true;
        try {
            const apiUrl = `api/wishlist.php?action=get_boards&user_id=${encodeURIComponent(userId)}`;
            console.log('DEBUG (populateStandaloneBoardSelect): URL:', apiUrl);
            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: { 'X-User-ID': userId }
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            console.log('standalone: populateStandaloneBoardSelect - API Response:', data);

            if (data.success && data.boards.length > 0) {
                standaloneBoardSelect.innerHTML = '<option value="">Selecciona un tablero</option>';
                data.boards.forEach(board => {
                    const optionValue = (board.id !== null && board.id !== undefined && board.id !== '') ? String(board.id) : '';
                    if (optionValue !== '') {
                        const option = document.createElement('option');
                        option.value = optionValue;
                        option.textContent = htmlspecialchars(board.name);
                        standaloneBoardSelect.appendChild(option);
                    } else {
                        console.error("ERROR: populateStandaloneBoardSelect: Tablero con ID inválido encontrado:", board);
                    }
                });
                if (standaloneBoardSelect.options.length > 1) {
                    standaloneBoardSelect.disabled = false;
                } else {
                    standaloneBoardSelect.innerHTML = '<option value="" disabled>No tienes tableros. Crea uno primero.</option>';
                    standaloneBoardSelect.disabled = true;
                    window.showMessage('No tienes tableros de wishlist. Crea uno para añadir productos.', 'info');
                }
            } else {
                standaloneBoardSelect.innerHTML = '<option value="" disabled>No tienes tableros. Crea uno primero.</option>';
                standaloneBoardSelect.disabled = true;
                window.showMessage('No tienes tableros de wishlist. Crea uno para añadir productos.', 'info');
                console.log('populateStandaloneBoardSelect: No hay tableros o error en la API.');
            }
        } catch (error) {
            console.error('standalone: Error de conexión/respuesta al poblar select de tableros:', error);
            standaloneBoardSelect.innerHTML = '<option value="" disabled>Error al cargar tableros</option>';
            standaloneBoardSelect.disabled = true;
            window.showMessage('Error de conexión al servidor al cargar tableros (select).', 'error');
        }
    };

    /** Envía el producto seleccionado al tablero de wishlist elegido. */
    async function addProductToStandaloneSelectedWishlist() {
        const userId = getUserId();
        if (!userId || userId.startsWith('anon_')) {
            window.showMessage('Debes iniciar sesión para añadir productos a wishlist.', 'info');
            return;
        }

        const boardId = standaloneBoardSelect ? standaloneBoardSelect.value : '';
        const productId = standaloneWishlistProductIdInput ? standaloneWishlistProductIdInput.value : '';
        const notes = standaloneItemNotes ? standaloneItemNotes.value : '';
        const productName = standaloneWishlistProductName ? standaloneWishlistProductName.textContent : '';
        const productPrice = standaloneWishlistProductActualPriceInput ? parseFloat(standaloneWishlistProductActualPriceInput.value) : 0;
        const productImage = standaloneWishlistProductActualImageInput ? standaloneWishlistProductActualImageInput.value : '';

        console.log(`DEBUG (addProductToStandaloneSelectedWishlist): Datos del producto a enviar:
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
             console.error('addProductToStandaloneSelectedWishlist: productId es nulo o vacío.');
             return;
        }
        if (!productName || productName.trim() === '' || !productPrice || !productImage || productImage.trim() === '') {
            window.showMessage('Error interno: Datos completos del producto (nombre, precio, imagen) no disponibles para añadir a wishlist. Intenta de nuevo.', 'error');
            console.error('addProductToStandaloneSelectedWishlist: Faltan datos de producto (name, price, image).', {productName, productPrice, productImage});
            return;
        }


        console.log('standalone: addProductToStandaloneSelectedWishlist - Enviando a API...');
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
            console.log('standalone: addItem - API Response:', data);

            if (data.success) {
                window.showMessage(data.message || 'Producto añadido a la wishlist con éxito.', 'success');
                window.closeModal(standaloneAddProductToWishlistModal);
                // Si la wishlist standalone está abierta en la vista de ítems, recargar
                if (standaloneWishlistItemsSection && standaloneWishlistItemsSection.style.display === 'block' && standaloneCurrentBoardNameDisplay) {
                    const currentBoardId = standaloneBoardSelect.value;
                    const currentBoardName = standaloneBoardSelect.options[standaloneBoardSelect.selectedIndex].text;
                    window.loadStandaloneWishlistItems(currentBoardId, currentBoardName);
                }
                window.loadStandaloneWishlistBoards(); // Recargar tableros para actualizar conteos
            } else {
                window.showMessage('Error al añadir a wishlist: ' + data.message, 'error');
                console.error('standalone: Error adding product to wishlist via API:', data.message);
            }
        } catch (error) {
            console.error('standalone: Error de conexión/respuesta al añadir producto a wishlist:', error);
            window.showMessage('Error de conexión al servidor al añadir a wishlist.', 'error');
        }
    }


    // ===============================================
    // 8. Listeners de Eventos (Conectando HTML a las funciones)
    // ===============================================

    // Abre el nuevo modal de Wishlist (desde el enlace del header)
    if (openStandaloneWishlistLink) {
        openStandaloneWishlistLink.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('standalone: Clic en "Mi Wishlist Mágica" (header/dropdown).');
            if (window.openModal) {
                window.openModal(standaloneWishlistModal);
                showStandaloneWishlistBoardsView(); // Asegura que la vista de tableros sea la primera
                window.loadStandaloneWishlistBoards(); // Carga los tableros al abrir el modal
            } else {
                console.warn('standalone: openModal no definido.');
                window.showMessage('Error: Funcionalidad de modal no disponible.', 'error');
            }
        });
    }

    // Cierra el modal principal de Wishlist
    if (closeStandaloneWishlistModalBtn) {
        closeStandaloneWishlistModalBtn.addEventListener('click', () => {
            window.closeModal(standaloneWishlistModal);
        });
    }

    // Botón "Crear Nuevo Tablero" dentro del modal principal
    if (standaloneCreateBoardBtn) standaloneCreateBoardBtn.addEventListener('click', openStandaloneCreateBoardModal);

    // Modal de Creación de Tablero (Standalone)
    if (closeStandaloneCreateBoardModalBtn) closeStandaloneCreateBoardModalBtn.addEventListener('click', () => window.closeModal(standaloneCreateBoardModal));
    if (cancelStandaloneCreateBoardBtn) cancelStandaloneCreateBoardBtn.addEventListener('click', () => window.closeModal(standaloneCreateBoardModal));
    if (standaloneCreateBoardForm) {
        standaloneCreateBoardForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = standaloneNewBoardNameInput ? standaloneNewBoardNameInput.value.trim() : '';
            const description = standaloneNewBoardDescriptionInput ? standaloneNewBoardDescriptionInput.value.trim() : '';
            if (name && name.trim() !== '') {
                createStandaloneWishlistBoard(name, description);
            } else {
                window.showMessage('El nombre del tablero no puede estar vacío.', 'error');
            }
        });
    }

    // Botón "Volver a Mis Tableros"
    if (standaloneBackToBoardsBtn) {
        standaloneBackToBoardsBtn.addEventListener('click', () => {
            console.log('standalone: Clic en "Volver a Mis Tableros".');
            showStandaloneWishlistBoardsView();
            window.loadStandaloneWishlistBoards();
        });
    }

    /** Adjunta listeners a los botones de ítems de wishlist (añadir al carrito, eliminar de wishlist) */
    function attachStandaloneWishlistItemsListeners() {
        document.querySelectorAll('.add-to-cart-btn-wishlist').forEach(button => {
            button.removeEventListener('click', handleStandaloneAddToCartFromWishlistClick);
            button.addEventListener('click', handleStandaloneAddToCartFromWishlistClick);
        });

        document.querySelectorAll('.standalone-remove-from-wishlist-btn').forEach(button => {
            button.removeEventListener('click', handleStandaloneRemoveFromWishlistClick);
            button.addEventListener('click', handleStandaloneRemoveFromWishlistClick);
        });
    }

    function handleStandaloneAddToCartFromWishlistClick() {
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

    async function handleStandaloneRemoveFromWishlistClick() {
        const boardId = this.dataset.boardId;
        const productId = this.dataset.productId;
        const boardName = this.dataset.boardName;

        if (window.confirm('¿Estás seguro de que quieres eliminar este producto de la wishlist?')) {
            await deleteStandaloneWishlistItem(boardId, productId, boardName);
        }
    }

    /** Adjunta listeners a los botones "Añadir a Wishlist" de las tarjetas de producto.
     * Esta función debe ser llamada por products.js y product_detail_page.js
     * cada vez que rendericen productos para asegurar que los botones sean interactivos.
     * Esta es la función global que products.js y product_detail_page.js deben llamar.
     */
    window.attachAddToWishlistListeners = function() {
        document.querySelectorAll(
            '.add-to-wishlist-btn, .add-to-wishlist-btn-page, .add-to-wishlist-btn-related'
        ).forEach(button => {
            button.removeEventListener('click', handleStandaloneAddToWishlistClick);
            button.addEventListener('click', handleStandaloneAddToWishlistClick);
        });
    };

    /** Manejador de clic para los botones "Añadir a Wishlist". */
    function handleStandaloneAddToWishlistClick() {
        const product = {
            id: this.dataset.productId,
            name: this.dataset.productName,
            price: parseFloat(this.dataset.productPrice),
            imageUrl: this.dataset.productImage
        };
        console.log("DEBUG (handleStandaloneAddToWishlistClick): Producto preparado para modal:", product);
        window.prepareStandaloneAddToWishlistModal(product);
    }

    // Listener global para el cambio de estado de autenticación (si es necesario recargar wishlist)
    window.addEventListener('authStatusChanged', (event) => {
        console.log('standalone: Evento authStatusChanged detectado. Usuario autenticado:', event.detail.isAuthenticated);
        if (event.detail.isAuthenticated) {
            console.log("DEBUG: Usuario autenticado. Recargando tableros de wishlist standalone.");
            // Recargar si el modal standalone está abierto o se va a abrir
            if (standaloneWishlistModal.style.display === 'block') { // Si el modal ya está abierto
                window.loadStandaloneWishlistBoards();
            }
        } else {
            console.log("DEBUG: Usuario desautenticado. Limpiando vista de wishlist standalone.");
            if (standaloneWishlistBoardsContainer) standaloneWishlistBoardsContainer.innerHTML = '<p class="placeholder-text">Inicia sesión para ver tus tableros.</p>';
            if (standaloneWishlistItemsSection) standaloneWishlistItemsSection.style.display = 'none';
            if (standaloneWishlistBoardsSection) standaloneWishlistBoardsSection.style.display = 'block';
        }
    });

}); // Fin DOMContentLoaded
