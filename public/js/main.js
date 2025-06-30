// public/js/main.js
// Este script contiene funciones globales reutilizables para el sitio.

console.log('main.js cargado.');

/**
 * Muestra un mensaje al usuario. Temporalmente usa alert() como fallback.
 * @param {string} message - El texto del mensaje.
 * @param {string} type - El tipo de mensaje ('success', 'error', 'info').
 */
window.showMessage = (message, type = 'info') => {
    // Para esta iteración, usamos alert() como solicitado, eliminando los toasts.
    // En una versión final, se implementaría un modal o elemento UI personalizado.

};

/**
 * Abre un modal dado su elemento DOM.
 * @param {HTMLElement} modalElement - El elemento DOM del modal.
 */
window.openModal = (modalElement) => {
    if (modalElement) {
        console.log('main.js: Abriendo modal:', modalElement.id);
        modalElement.classList.add('show');
        document.body.classList.add('modal-open'); // Para prevenir scroll en el body
    } else {
        console.error('main.js: openModal llamado con un elemento modal nulo o indefinido.');
        window.showMessage('Error interno: No se pudo abrir el elemento.','error');
    }
};

/**
 * Cierra un modal dado su elemento DOM.
 * @param {HTMLElement} modalElement - El elemento DOM del modal.
 */
window.closeModal = (modalElement) => {
    if (modalElement) {
        console.log('main.js: Cerrando modal:', modalElement.id);
        modalElement.classList.remove('show');
        document.body.classList.remove('modal-open');
    } else {
        console.error('main.js: closeModal llamado con un elemento modal nulo o indefinido.');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('main.js: DOMContentLoaded - Funciones openModal, closeModal, showMessage disponibles.');
});
