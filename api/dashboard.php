<?php
/**
 * Archivo: dashboard.php
 * Descripción: API REST para obtener datos estadísticos del dashboard.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');

// Configuración de manejo de errores para evitar que se imprima HTML
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Convertir el error a una excepción para poder capturarlo
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Incluir archivos necesarios. Usamos un bloque try-catch para manejar errores de inclusión
try {
    require_once '../core/functions.php';
    require_once '../core/db.php';
} catch (ErrorException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al cargar dependencias: ' . $e->getMessage()]);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $datos_dashboard = [];

    // Total de pacientes registrados
    $query_total_pacientes = "SELECT COUNT(*) AS total FROM tbl_pacientes";
    $result_total = $conexion->query($query_total_pacientes);
    $datos_dashboard['total_pacientes'] = $result_total->fetch_assoc()['total'];

    // Citas agendadas para hoy
    $fecha_hoy = date('Y-m-d');
    $query_citas_hoy = "SELECT COUNT(*) AS total FROM tbl_citas WHERE fecha = '{$fecha_hoy}'";
    $result_citas_hoy = $conexion->query($query_citas_hoy);
    $datos_dashboard['citas_hoy'] = $result_citas_hoy->fetch_assoc()['total'];
    
    // Cantidad de pacientes por estado
    $query_pacientes_estado = "SELECT ep.nombre_estado, COUNT(p.id_paciente) AS conteo
                               FROM tbl_pacientes p
                               JOIN tbl_estados_paciente ep ON p.id_estado = ep.id_estado
                               GROUP BY ep.nombre_estado";
    $datos_dashboard['pacientes_por_estado'] = cargar_combo($query_pacientes_estado);

    // Cantidad de pacientes por sexo
    $query_pacientes_sexo = "SELECT s.nombre_sexo, COUNT(p.id_paciente) AS conteo
                             FROM tbl_pacientes p
                             JOIN tbl_sexos s ON p.id_sexo = s.id_sexo
                             GROUP BY s.nombre_sexo";
    $datos_dashboard['pacientes_por_sexo'] = cargar_combo($query_pacientes_sexo);

    // Cantidad de pacientes por departamento
    $query_pacientes_depto = "SELECT d.nombre, COUNT(p.id_paciente) AS conteo
                              FROM tbl_pacientes p
                              JOIN tbl_departamentos d ON p.id_departamento = d.id_departamento
                              GROUP BY d.nombre
                              ORDER BY conteo DESC";
    $datos_dashboard['pacientes_por_departamento'] = cargar_combo($query_pacientes_depto);

    // Cantidad de pacientes por distrito
    $query_pacientes_distrito = "SELECT d.nombre, COUNT(p.id_paciente) AS conteo
                                 FROM tbl_pacientes p
                                 JOIN tbl_distritos d ON p.id_distrito = d.id_distrito
                                 GROUP BY d.nombre
                                 ORDER BY conteo DESC";
    $datos_dashboard['pacientes_por_distrito'] = cargar_combo($query_pacientes_distrito);

    // Procedimientos más realizados (últimos 30 días)
    $fecha_hace_30_dias = date('Y-m-d', strtotime('-30 days'));
    $query_procedimientos = "SELECT t.nombre_tratamiento, COUNT(pr.id_procedimiento_realizado) AS conteo
                             FROM tbl_procedimientos_realizados pr
                             JOIN tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento
                             WHERE pr.fecha_realizacion >= '{$fecha_hace_30_dias}'
                             GROUP BY t.nombre_tratamiento
                             ORDER BY conteo DESC
                             LIMIT 5";
    $datos_dashboard['procedimientos_populares'] = cargar_combo($query_procedimientos);

    echo json_encode($datos_dashboard);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}

$conexion->close();
