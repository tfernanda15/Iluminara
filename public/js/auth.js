// public/js/auth.js
console.log('auth.js cargado.');

document.addEventListener('DOMContentLoaded', () => {
    // Referencias a elementos de UI
    const authModal = document.getElementById('authModal');
    const closeAuthModalBtn = document.getElementById('closeAuthModalBtn');
    const loginTabBtn = document.getElementById('loginTabBtn');
    const registerTabBtn = document.getElementById('registerTabBtn');
    const loginPanel = document.getElementById('loginPanel');
    const registerPanel = document.getElementById('registerPanel');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const showRegisterLink = document.getElementById('showRegister');
    const showLoginLink = document.getElementById('showLogin');

    const loginBtn = document.getElementById('loginBtn'); // Botón de login en el header
    const userProfile = document.getElementById('userProfile'); // Elemento de perfil en el header
    const displayUsername = document.getElementById('displayUsername');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    const logoutBtn = document.getElementById('logoutBtn');
    const myAccountLink = document.getElementById('myAccountLink');
    const myWishlistLink = document.getElementById('myWishlistLink');

    // Estado de autenticación
    let isAuthenticated = false; 

    // --- Depuración del Modal de Autenticación ---
    if (authModal) {
        console.log('auth.js: authModal encontrado en el DOM.');
    } else {
        console.error('auth.js: ERROR: authModal NO encontrado en el DOM. El modal de login/registro no funcionará.');
        window.showMessage('Error interno: El modal de autenticación no se encontró. Contacta soporte.','error');
        return; // Detener ejecución si no hay modal
    }
    // ---------------------------------------------


    /**
     * Muestra u oculta el modal de autenticación.
     * @param {boolean} show Si es true, muestra el modal; si es false, lo oculta.
     * @param {string} initialTab La pestaña inicial a mostrar ('login' o 'register').
     */
    function toggleAuthModal(show, initialTab = 'login') {
        if (!authModal) { 
            console.error('auth.js: toggleAuthModal: authModal es nulo, no se puede operar.');
            return;
        }
        if (show) {
            console.log('auth.js: toggleAuthModal: Llamando window.openModal para authModal.');
            window.openModal(authModal);
            if (initialTab === 'register') {
                showRegisterPanel();
            } else {
                showLoginPanel();
            }
        } else {
            console.log('auth.js: toggleAuthModal: Llamando window.closeModal para authModal.');
            window.closeModal(authModal);
        }
    }

    /**
     * Muestra la pestaña de inicio de sesión.
     */
    function showLoginPanel() {
        if (loginTabBtn) loginTabBtn.classList.add('active');
        if (registerTabBtn) registerTabBtn.classList.remove('active');
        if (loginPanel) loginPanel.classList.add('active');
        if (registerPanel) registerPanel.classList.remove('active');
    }

    /**
     * Muestra la pestaña de registro.
     */
    function showRegisterPanel() {
        if (registerTabBtn) registerTabBtn.classList.add('active');
        if (loginTabBtn) loginTabBtn.classList.remove('active');
        if (registerPanel) registerPanel.classList.add('active');
        if (loginPanel) loginPanel.classList.remove('active');
    }

    /**
     * Actualiza la interfaz de usuario de autenticación.
     * Muestra el perfil de usuario si está autenticado, o el botón de login si no.
     * También despacha un evento para otros módulos.
     */
    function updateAuthUI() {
        const currentUserId = localStorage.getItem('userId');
        isAuthenticated = !!currentUserId; 
        console.log('auth.js: Actualizando UI de autenticación. isAuthenticated:', isAuthenticated, 'userId:', currentUserId);

        if (loginBtn && userProfile) { 
            if (isAuthenticated) {
                loginBtn.style.display = 'none';
                userProfile.style.display = 'flex';
                if (displayUsername) {
                    displayUsername.textContent = localStorage.getItem('username') || 'Usuario'; 
                }
            } else {
                loginBtn.style.display = 'flex'; // Asegurar que sea visible si no autenticado
                userProfile.style.display = 'none';
                localStorage.removeItem('username'); 
                localStorage.removeItem('userId'); 
                localStorage.removeItem('email'); 
            }
        } else {
            console.log('auth.js: Elementos de UI de autenticación (userProfile, loginBtn) no encontrados en esta página, omitiendo actualización.');
        }
        // Despachar un evento personalizado para notificar a otros módulos
        window.dispatchEvent(new CustomEvent('authStatusChanged', { detail: { isAuthenticated: isAuthenticated, userId: currentUserId } }));
    }

    // Event Listeners (se añaden solo si los elementos existen)
    if (loginBtn) {
        console.log('auth.js: Añadiendo event listener a loginBtn.');
        loginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('auth.js: loginBtn clickeado.');
            toggleAuthModal(true, 'login');
        });
    } else {
        console.warn('auth.js: loginBtn no encontrado en el DOM.');
    }

    if (closeAuthModalBtn) {
        closeAuthModalBtn.addEventListener('click', () => window.closeModal(authModal));
    }

    if (loginTabBtn) loginTabBtn.addEventListener('click', showLoginPanel);
    if (registerTabBtn) registerTabBtn.addEventListener('click', showRegisterPanel);
    if (showRegisterLink) showRegisterLink.addEventListener('click', (e) => {
        e.preventDefault();
        showRegisterPanel();
    });
    if (showLoginLink) showLoginLink.addEventListener('click', (e) => {
        e.preventDefault();
        showLoginPanel();
    });

    // Lógica para el formulario de LOGIN (CONEXIÓN A LA API REAL)
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = loginForm.querySelector('#login-email').value;
            const password = loginForm.querySelector('#login-password').value;

            console.log('auth.js: Intentando iniciar sesión con:', email);
            window.showMessage('Iniciando sesión...', 'info');

            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.success) {
                    localStorage.setItem('username', data.user.username);
                    localStorage.setItem('userId', data.user.id);
                    localStorage.setItem('email', data.user.email);
                    isAuthenticated = true;

                    console.log('auth.js: Login exitoso, userId seteado a:', localStorage.getItem('userId'));
                    updateAuthUI();
                    toggleAuthModal(false);
                    window.showMessage('¡Bienvenido de nuevo, ' + data.user.username + '!', 'success');
                } else {
                    console.error('auth.js: Error en el login:', data.message);
                    window.showMessage('Error en el login: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('auth.js: Error de red durante el login:', error);
                window.showMessage('Error de conexión al servidor. Inténtalo de nuevo más tarde.', 'error');
            }
        });
    }

    // Lógica para el formulario de REGISTRO (CONEXIÓN A LA API REAL)
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = registerForm.querySelector('#register-name').value;
            const email = registerForm.querySelector('#register-email').value;
            const password = registerForm.querySelector('#register-password').value;
            const confirmPassword = registerForm.querySelector('#register-confirm-password').value;

            if (password !== confirmPassword) {
                window.showMessage('Las contraseñas no coinciden.', 'error');
                return;
            }

            console.log('auth.js: Intentando registrar usuario:', username, email);
            window.showMessage('Registrando usuario...', 'info');

            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, email, password })
                });

                const data = await response.json();

                if (data.success) {
                    window.showMessage('Registro exitoso. Ahora puedes iniciar sesión.', 'success');
                    showLoginPanel(); 
                } else {
                    console.error('auth.js: Error en el registro:', data.message);
                    window.showMessage('Error en el registro: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('auth.js: Error de red durante el registro:', error);
                window.showMessage('Error de conexión al servidor. Inténtalo de nuevo más tarde.', 'error');
            }
        });
    }

    // Lógica para el dropdown de usuario
    if (userProfile) {
        userProfile.addEventListener('click', (e) => {
            e.stopPropagation(); 
            userProfile.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (userProfile && !userProfile.contains(e.target) && userProfile.classList.contains('active')) {
                userProfile.classList.remove('active');
            }
        });
    }

    // Lógica para cerrar sesión
    const confirmLogoutModal = document.getElementById('confirmLogoutModal');
    const closeConfirmLogoutModalBtn = document.getElementById('closeConfirmLogoutModalBtn');
    const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');
    const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirmLogoutModal) window.openModal(confirmLogoutModal);
        });
    }

    if (closeConfirmLogoutModalBtn) closeConfirmLogoutModalBtn.addEventListener('click', () => window.closeModal(confirmLogoutModal));
    if (cancelLogoutBtn) cancelLogoutBtn.addEventListener('click', () => window.closeModal(confirmLogoutModal));

    if (confirmLogoutBtn) {
        confirmLogoutBtn.addEventListener('click', async () => {
            console.log('auth.js: Cerrando sesión...');
            window.showMessage('Cerrando sesión...', 'info');
            localStorage.removeItem('username'); 
            localStorage.removeItem('userId'); 
            localStorage.removeItem('email'); 
            isAuthenticated = false; 
            updateAuthUI(); 
            window.closeModal(confirmLogoutModal);
            window.showMessage('Sesión cerrada.', 'success');
        });
    }

    // Lógica para "Mi Cuenta" (asume que myAccountModal y los paneles existen en index.php)
    const myAccountModal = document.getElementById('myAccountModal');
    const closeMyAccountModalBtn = document.getElementById('closeMyAccountModalBtn');
    const profileTabBtn = document.getElementById('profileTabBtn');
    const addressesTabBtn = document.getElementById('addressesTabBtn');
    const ordersTabBtn = document.getElementById('ordersTabBtn');
    const wishlistTabBtn = document.getElementById('wishlistTabBtn');
    const profilePanel = document.getElementById('profilePanel');
    const addressesPanel = document.getElementById('addressesPanel');
    const ordersPanel = document.getElementById('ordersPanel');
    const wishlistPanel = document.getElementById('wishlistPanel');

    const profileUsername = document.getElementById('profile-username');
    const profileEmail = document.getElementById('profile-email');
    const profilePhone = document.getElementById('profile-phone'); 
    const editProfileBtn = document.getElementById('editProfileBtn');
    const saveProfileBtn = document.getElementById('saveProfileBtn');
    const cancelEditProfileBtn = document.getElementById('cancelEditProfileBtn');

    if (myAccountLink) {
        myAccountLink.addEventListener('click', (e) => {
            e.preventDefault();
            if (myAccountModal) {
                window.openModal(myAccountModal);
                window.showAccountTab(profileTabBtn, profilePanel); 
                
                if (isAuthenticated) {
                    if (profileUsername) profileUsername.value = localStorage.getItem('username') || '';
                    if (profileEmail) profileEmail.value = localStorage.getItem('email') || ''; 
                }
            }
        });
    }
    if (closeMyAccountModalBtn) {
        closeMyAccountModalBtn.addEventListener('click', () => window.closeModal(myAccountModal));
    }


    /**
     * Controla la visibilidad de los paneles dentro del modal "Mi Cuenta".
     * Asegura que solo un panel esté activo y visible a la vez.
     * Exportado a window para que wishlist.js pueda usarlo.
     * @param {HTMLElement} tabButton - El botón de la pestaña clicado.
     * @param {HTMLElement} tabPanel - El panel asociado al botón de la pestaña.
     */
    window.showAccountTab = function(tabButton, tabPanel) {
        document.querySelectorAll('#myAccountModal .tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('#myAccountModal .account-panel').forEach(panel => {
            panel.classList.remove('active');
            panel.style.display = 'none'; 
        });

        if (tabButton) tabButton.classList.add('active');
        if (tabPanel) {
             tabPanel.classList.add('active');
             tabPanel.style.display = 'flex'; 
        }
        console.log('auth.js: showAccountTab activado para:', tabPanel ? tabPanel.id : 'N/A');
    }

    if (profileTabBtn) profileTabBtn.addEventListener('click', () => window.showAccountTab(profileTabBtn, profilePanel));
    if (addressesTabBtn) addressesTabBtn.addEventListener('click', () => window.showAccountTab(addressesTabBtn, addressesPanel));
    if (ordersTabBtn) ordersTabBtn.addEventListener('click', () => window.showAccountTab(ordersTabBtn, ordersPanel));
    
    if (wishlistTabBtn) {
        wishlistTabBtn.addEventListener('click', () => {
            console.log('auth.js: Clic en wishlistTabBtn.');
            window.showAccountTab(wishlistTabBtn, wishlistPanel);
            if (isAuthenticated && window.loadWishlistBoards) { 
                window.loadWishlistBoards(); 
            } else if (!isAuthenticated) {
                window.showMessage('Inicia sesión para ver tu Wishlist Mágica.', 'info');
            }
        });
    }

    // Funciones de edición de perfil (simuladas por ahora)
    if (editProfileBtn && profileUsername && profileEmail && profilePhone) {
        editProfileBtn.addEventListener('click', () => {
            profileUsername.readOnly = false;
            profileEmail.readOnly = false;
            profilePhone.readOnly = false;
            editProfileBtn.style.display = 'none';
            if (saveProfileBtn) saveProfileBtn.style.display = 'inline-block';
            if (cancelEditProfileBtn) cancelEditProfileBtn.style.display = 'inline-block';
        });
    }

    if (cancelEditProfileBtn && profileUsername && profileEmail && profilePhone) {
        cancelEditProfileBtn.addEventListener('click', () => {
            profileUsername.readOnly = true;
            profileEmail.readOnly = true;
            profilePhone.readOnly = true;
            if (editProfileBtn) editProfileBtn.style.display = 'inline-block';
            if (saveProfileBtn) saveProfileBtn.style.display = 'none';
            if (cancelEditProfileBtn) cancelEditProfileBtn.style.display = 'none';
        });
    }

    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('auth.js: Guardando perfil (simulado)...');
            window.showMessage('Guardando cambios en el perfil...', 'info');
            setTimeout(() => {
                window.showMessage('Perfil actualizado con éxito!', 'success');
                if (profileUsername) profileUsername.readOnly = true;
                if (profileEmail) profileEmail.readOnly = true;
                if (profilePhone) profilePhone.readOnly = true;
                if (editProfileBtn) editProfileBtn.style.display = 'inline-block';
                if (saveProfileBtn) saveProfileBtn.style.display = 'none';
                if (cancelEditProfileBtn) cancelEditProfileBtn.style.display = 'none';
            }, 1500);
        });
    }

    // Verificar si el usuario ya está logueado al cargar la página (ej. por localStorage)
    updateAuthUI(); 
});

