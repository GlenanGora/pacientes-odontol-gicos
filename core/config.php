<?php
/**
 * Archivo: config.php
 * Descripción: Carga la configuración de la aplicación desde el archivo JSON.
 * Autor: Gemini
 */

// Define la zona horaria para asegurar la consistencia en las fechas
date_default_timezone_set('America/Lima');

// Define una constante para la ruta de la configuración
define('CONFIG_FILE', __DIR__ . '/../configuracion.json');

// Inicializa una variable global para la configuración
$config = null;

// Verifica si el archivo de configuración existe
if (file_exists(CONFIG_FILE)) {
    // Lee el contenido del archivo
    $config_json = file_get_contents(CONFIG_FILE);

    // Decodifica el JSON a un objeto PHP
    $config = json_decode($config_json);

    // Verifica si la decodificación fue exitosa
    if ($config === null) {
        die("Error: No se pudo decodificar el archivo de configuración JSON.");
    }
} else {
    // Muestra un error si el archivo no existe
    die("Error: Archivo de configuración 'configuracion.json' no encontrado.");
}

// Puedes acceder a la configuración de esta manera en otros archivos:
// $nombre_clinica = $config->clinica->nombre;
// $db_host = $config->database->host;

?>