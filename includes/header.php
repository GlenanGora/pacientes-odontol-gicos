<?php
/**
 * Archivo: header.php
 * Descripción: Barra de navegación superior de la aplicación.
 * Autor: Gemini
 */

// Se carga la configuración para mostrar el nombre de la clínica
require_once 'core/config.php';

// Cierra la sesión si se recibe la petición de cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

?>
<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#"><?php echo htmlspecialchars($config->clinica->nombre); ?></a>
    <div class="navbar-nav w-100">
        <div class="nav-item text-nowrap d-flex justify-content-end">
            <span class="nav-link px-3 text-white">Hola, <?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></span>
            <a class="nav-link px-3 text-white" href="?logout">Cerrar Sesión</a>
        </div>
    </div>
</header>