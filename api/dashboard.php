<?php
/**
 * Archivo: dashboard.php
 * Descripción: API REST para obtener datos estadísticos del dashboard.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/functions.php';
require_once '../core/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $datos_dashboard = [];

    // RF11.1: Total de pacientes registrados
    $query_total_pacientes = "SELECT COUNT(*) AS total FROM tbl_pacientes";
    $result_total = $conexion->query($query_total_pacientes);
    $datos_dashboard['total_pacientes'] = $result_total->fetch_assoc()['total'];

    // RF11.1: Citas agendadas para hoy
    $fecha_hoy = date('Y-m-d');
    $query_citas_hoy = "SELECT COUNT(*) AS total FROM tbl_citas WHERE fecha = '$fecha_hoy'";
    $result_citas_hoy = $conexion->query($query_citas_hoy);
    $datos_dashboard['citas_hoy'] = $result_citas_hoy->fetch_assoc()['total'];

    // RF11.1: Procedimientos más realizados (últimos 30 días)
    $fecha_hace_30_dias = date('Y-m-d', strtotime('-30 days'));
    $query_procedimientos = "SELECT t.nombre_tratamiento, COUNT(pr.id_procedimiento_realizado) AS conteo
                             FROM tbl_procedimientos_realizados pr
                             JOIN tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento
                             WHERE pr.fecha_realizacion >= '$fecha_hace_30_dias'
                             GROUP BY t.nombre_tratamiento
                             ORDER BY conteo DESC
                             LIMIT 5";
    $datos_dashboard['procedimientos_populares'] = cargar_combo($query_procedimientos);

    // RF6.4: Ingresos anuales reales de la tabla tbl_pagos
    $ingresos_anuales = array_fill(0, 12, 0); // Inicializa un array con 12 meses en cero
    $anio_actual = date('Y');
    
    $query_ingresos_anuales = "SELECT MONTH(fecha_pago) AS mes, SUM(monto) AS total_ingresos
                               FROM tbl_pagos
                               WHERE YEAR(fecha_pago) = '$anio_actual'
                               GROUP BY mes
                               ORDER BY mes ASC";
    
    $result_ingresos = $conexion->query($query_ingresos_anuales);

    if ($result_ingresos) {
        while ($fila = $result_ingresos->fetch_assoc()) {
            $mes_index = $fila['mes'] - 1; // Ajusta el índice para el array (0-11)
            $ingresos_anuales[$mes_index] = (float)$fila['total_ingresos'];
        }
    }
    
    $datos_dashboard['ingresos_anuales'] = $ingresos_anuales;

    echo json_encode($datos_dashboard);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}

$conexion->close();
