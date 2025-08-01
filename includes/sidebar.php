<?php
/**
 * Archivo: sidebar.php
 * Descripción: Menú de navegación lateral de la aplicación.
 * Autor: Gemini
 */
?>
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky d-flex flex-column h-100">
        <div class="sidebar-header">
            
        </div>
        <ul class="nav flex-column pt-3">
            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>" aria-current="page" href="?page=dashboard">
                    <span class="me-2">&#127968;</span> <!-- Icono de casa -->
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (str_contains($page, 'pacientes')) ? 'active' : ''; ?>" href="?page=pacientes/listar">
                    <span class="me-2">&#129489;</span> <!-- Icono de persona -->
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
                <a class="nav-link <?php echo (str_contains($page, 'diagnosticos')) ? 'active' : ''; ?>" href="?page=diagnosticos/gestionar">
                    <span class="me-2">&#129701;</span> <!-- Icono de diente -->
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
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        width: 250px;
        z-index: 1000;
        padding: 0;
        background-color: #323A5B;
        color: #F2F3F4;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
    }

    .sidebar-header {
        background-color: #323A5B;
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 700;
        padding: 1.5rem 1rem;
        text-align: center;
    }

    .sidebar .nav-item .nav-link {
        font-weight: 500;
        color: #F2F3F4;
        padding: 1rem 1.5rem;
        border-radius: 0;
        transition: background-color 0.2s ease, border-left 0.2s ease;
        display: flex;
        align-items: center;
    }

    .sidebar .nav-item .nav-link.active {
        color: #ffffff;
        background-color: #747E89;
        border-left: 3px solid #ffc107;
    }

    .sidebar .nav-item .nav-link:hover:not(.active) {
        background-color: #e9ecef;
        color: #0d6efd;
    }

    .sidebar .nav-item .nav-link span {
        margin-right: 10px;
    }
</style>
