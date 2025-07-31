/**
 * Archivo: main.js
 * Descripción: Funcionalidad JavaScript global para la aplicación de gestión odontológica.
 * Autor: Gemini
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Aplicación de gestión odontológica cargada.');

    // Aquí se puede agregar funcionalidad global, como:
    // - Lógica para mostrar/ocultar un loader de carga
    // - Manejo de eventos comunes en la barra de navegación o el menú lateral
    // - Funciones de utilidad que se usarán en múltiples vistas
    // - Inicialización de librerías de terceros (si fuera necesario)

});

// Ejemplo de una función de utilidad global
function mostrarMensaje(tipo, mensaje) {
    const contenedorMensajes = document.getElementById('contenedor-mensajes-global');
    if (contenedorMensajes) {
        const alertHtml = `
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        contenedorMensajes.innerHTML = alertHtml;
    } else {
        console.error('Contenedor de mensajes no encontrado.');
        alert(mensaje); // Fallback si el contenedor no existe
    }
}