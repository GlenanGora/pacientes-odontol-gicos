<?php
/**
 * Archivo: login.php
 * Descripción: Página de inicio de sesión para la aplicación de gestión odontológica.
 * Autor: Gemini
 */

session_start();

// Redireccionar al dashboard si el usuario ya está autenticado
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

// Incluye la conexión a la base de datos y las funciones auxiliares
require_once 'core/db.php';
require_once 'core/functions.php';

$error_message = '';

// Lógica para procesar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanear y obtener los datos del formulario
    $usuario = sanear_entrada($_POST['usuario']);
    $password = sanear_entrada($_POST['password']);

    // Calcular el hash SHA-512 de la contraseña ingresada
    $password_hash = hash('sha512', $password);

    // Consulta para verificar las credenciales del usuario
    $query = "SELECT * FROM tbl_usuarios WHERE usuario = ? AND password = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ss", $usuario, $password_hash);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        // Credenciales correctas, iniciar sesión
        $usuario_data = $resultado->fetch_assoc();
        $_SESSION['usuario'] = $usuario_data['usuario'];
        $_SESSION['nombre_completo'] = $usuario_data['nombre_completo'];

        // Redireccionar al dashboard
        redireccionar('index.php');
    } else {
        // Credenciales incorrectas
        $error_message = 'Usuario o contraseña incorrectos.';
    }

    $stmt->close();
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Clínica Odontológica</title>
    <!-- Incluye Bootstrap CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Enlace a nuestro archivo de estilos CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-container">
                    <div class="text-center mb-4">
                        <!-- Aquí podrías colocar el logo de la clínica -->
                        <h2 class="h3">Iniciar Sesión</h2>
                        <p class="text-muted">Gestión de Clínica Odontológica</p>
                    </div>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Acceder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Incluye Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
