<?php
/**
 * Archivo: index.php
 * Descripción: Página principal (dashboard) de la aplicación de gestión odontológica.
 * Autor: Gemini
 */

session_start();

// Si el usuario no está autenticado, lo redirige a la página de login
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Incluye el archivo de configuración y la conexión a la base de datos
require_once 'core/config.php';
require_once 'core/db.php';
require_once 'core/functions.php';

// Define la página actual por defecto (el dashboard)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Define el título de la página
$page_title = ucfirst(str_replace(['_', '/'], ' ', $page));

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Clínica Odontológica</title>
    <!-- Incluye Bootstrap CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Enlace a nuestro archivo de estilos CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Incluye la barra de navegación (header) -->
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">

            <!-- Incluye el menú lateral (sidebar) -->
            <?php include 'includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
                </div>

                <!-- Lógica de enrutamiento para cargar las vistas -->
                <?php
                // Se agrega 'pacientes/historia_clinica' a la lista de páginas permitidas.
                $allowed_pages = ['dashboard', 'pacientes/listar', 'pacientes/registrar', 'pacientes/editar', 'pacientes/historia_clinica', 'citas/listar', 'citas/agendar', 'diagnosticos/gestionar', 'tratamientos/gestionar', 'recetas/historial', 'reportes/estadisticas'];
                $view_path = 'views/' . $page . '.php';

                if (in_array($page, $allowed_pages) && file_exists($view_path)) {
                    include $view_path;
                } else {
                    echo "<div class='alert alert-danger'>Página no encontrada.</div>";
                    // En caso de que la página no exista, se puede cargar una página de error o el dashboard por defecto.
                    // include 'views/dashboard.php';
                }
                ?>
            </main>
        </div>
    </div>

    <!-- Incluye Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Incluye nuestro archivo principal de JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>