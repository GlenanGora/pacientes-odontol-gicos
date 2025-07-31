<?php
/**
 * Archivo: db.php
 * Descripción: Establece la conexión a la base de datos MySQL.
 * Autor: Gemini
 */

// Incluye el archivo de configuración para obtener los datos de conexión.
require_once 'config.php';

// Verifica si el objeto de configuración ha sido cargado.
if (!$config) {
    die("Error: No se pudo cargar la configuración de la base de datos.");
}

// Extrae los parámetros de la base de datos del objeto de configuración.
$db_host = $config->database->host;
$db_user = $config->database->usuario;
$db_pass = $config->database->password;
$db_name = $config->database->nombre_db;

// Se utiliza la clase mysqli para la conexión.
$conexion = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verifica si hay errores en la conexión.
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// Opcional: Establece el conjunto de caracteres a UTF-8 para evitar problemas con tildes y eñes.
$conexion->set_charset("utf8mb4");

?>
