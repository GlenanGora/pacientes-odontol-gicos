<?php
/**
 * Archivo: functions.php
 * Descripción: Contiene funciones auxiliares para la aplicación, como la carga de datos para combos y sanitización.
 * Autor: Gemini
 */

// Incluye el archivo de conexión a la base de datos.
require_once 'db.php';

/**
 * Carga los datos para un combo o dropdown desde una tabla específica.
 * @param string $query La consulta SQL a ejecutar.
 * @return array Un array de objetos con los resultados, o un array vacío si no hay datos.
 */
function cargar_combo($query) {
    global $conexion; // Usa la conexión global
    $resultados = [];
    if ($resultado = $conexion->query($query)) {
        while ($fila = $resultado->fetch_object()) {
            $resultados[] = $fila;
        }
        $resultado->free();
    }
    return $resultados;
}

/**
 * Sanea una cadena de texto para prevenir inyecciones SQL y ataques XSS.
 * @param string $texto La cadena de texto a sanear.
 * @return string La cadena de texto saneada.
 */
function sanear_entrada($texto) {
    global $conexion; // Usa la conexión global para la función de escape
    $texto_saneado = trim($texto);
    $texto_saneado = stripslashes($texto_saneado);
    $texto_saneado = htmlspecialchars($texto_saneado, ENT_QUOTES, 'UTF-8');
    $texto_saneado = $conexion->real_escape_string($texto_saneado);
    return $texto_saneado;
}

/**
 * Redirecciona a una página específica.
 * @param string $url La URL a la que se desea redireccionar.
 */
function redireccionar($url) {
    header("Location: $url");
    exit();
}

/**
 * Obtiene todos los datos para los combos de la aplicación en un solo llamado.
 * Este es el "único endpoint" para la carga inicial de datos en el frontend.
 * @return string Retorna un JSON con todos los datos necesarios para los combos.
 */
function obtener_datos_combos() {
    global $conexion;

    $datos = [];

    // Cargar tipos de documento
    $query_doc = "SELECT id_documento_tipo AS id, nombre_tipo AS nombre FROM tbl_documento_tipos";
    $datos['documento_tipos'] = cargar_combo($query_doc);

    // Cargar sexos
    $query_sexo = "SELECT id_sexo AS id, nombre_sexo AS nombre FROM tbl_sexos";
    $datos['sexos'] = cargar_combo($query_sexo);

    // Cargar departamentos del Perú
    $query_departamentos = "SELECT id_departamento AS id, nombre FROM tbl_departamentos ORDER BY nombre";
    $datos['departamentos'] = cargar_combo($query_departamentos);

    // Cargar estados del paciente
    $query_estados = "SELECT id_estado AS id, nombre_estado AS nombre FROM tbl_estados_paciente";
    $datos['estados_paciente'] = cargar_combo($query_estados);

    // Cargar diagnósticos
    $query_diagnosticos = "SELECT id_diagnostico AS id, nombre_diagnostico AS nombre FROM tbl_diagnosticos ORDER BY nombre";
    $datos['diagnosticos'] = cargar_combo($query_diagnosticos);

    // Cargar tratamientos
    $query_tratamientos = "SELECT id_tratamiento AS id, nombre_tratamiento AS nombre, costo_base AS costo FROM tbl_tratamientos ORDER BY nombre";
    $datos['tratamientos'] = cargar_combo($query_tratamientos);
    
    // Retorna los datos como un JSON
    return json_encode($datos);
}

/**
 * Obtiene las provincias de un departamento.
 * @param int $id_departamento El ID del departamento.
 * @return string Retorna un JSON con las provincias.
 */
function obtener_provincias_por_departamento($id_departamento) {
    global $conexion;
    $id_departamento = sanear_entrada($id_departamento);
    $query = "SELECT id_provincia AS id, nombre FROM tbl_provincias WHERE id_departamento = '$id_departamento' ORDER BY nombre";
    $provincias = cargar_combo($query);
    return json_encode($provincias);
}

/**
 * Obtiene los distritos de una provincia.
 * @param int $id_provincia El ID de la provincia.
 * @return string Retorna un JSON con los distritos.
 */
function obtener_distritos_por_provincia($id_provincia) {
    global $conexion;
    $id_provincia = sanear_entrada($id_provincia);
    $query = "SELECT id_distrito AS id, nombre FROM tbl_distritos WHERE id_provincia = '$id_provincia' ORDER BY nombre";
    $distritos = cargar_combo($query);
    return json_encode($distritos);
}

?>
