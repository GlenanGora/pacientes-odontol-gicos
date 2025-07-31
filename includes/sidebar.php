<?php
/**
 * Archivo: sidebar.php
 * Descripción: Menú de navegación lateral de la aplicación.
 * Autor: Gemini
 */
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3 sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>" aria-current="page" href="?page=dashboard">
                    <span class="me-2">&#127968;</span> <!-- Icono de casa -->
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (str_contains($page, 'pacientes')) ? 'active' : ''; ?>" href="?page=pacientes/listar">
                    <span class="me-2">&#128100;</span> <!-- Icono de persona -->
                    Pacientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (str_contains($page, 'citas')) ? 'active' : ''; ?>" href="?page=citas/listar">
                    <span class="me-2">&#128197;</span> <!-- Icono de calendario -->
                    Citas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (str_contains($page, 'diagnosticos/gestionar')) ? 'active' : ''; ?>" href="?page=diagnosticos/gestionar">
                    <span class="me-2">&#129661;</span> <!-- Icono de diente -->
                    Diagnósticos y Tratamientos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (str_contains($page, 'recetas')) ? 'active' : ''; ?>" href="?page=recetas/historial">
                    <span class="me-2">&#128220;</span> <!-- Icono de papel -->
                    Recetas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (str_contains($page, 'reportes')) ? 'active' : ''; ?>" href="?page=reportes/estadisticas">
                    <span class="me-2">&#128200;</span> <!-- Icono de gráfico de barras -->
                    Reportes
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
    .sidebar {
        min-height: 100vh; /* Altura completa de la ventana */
        background-color: #f8f9fa; /* Color de fondo claro */
    }
    .sidebar .nav-link {
        font-weight: 500;
        color: #333;
    }
    .sidebar .nav-link.active {
        color: #fff;
        background-color: #0d6efd;
    }
    .sidebar .nav-link:hover:not(.active) {
        background-color: #e9ecef;
        color: #0d6efd;
    }
    .sidebar-sticky {
        top: 0; /* Asegura que el menú lateral se mantenga visible */
    }
    .navbar-brand {
        font-weight: bold;
    }
</style>
