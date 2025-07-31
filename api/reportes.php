<?php
/**
 * Archivo: reportes.php
 * Descripción: API REST para obtener datos estadísticos para la página de reportes.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/functions.php';
require_once '../core/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $datos_reportes = [];

    // RF11.1: Estadísticas de atención por sexo
    $query_pacientes_sexo = "SELECT s.nombre_sexo, COUNT(p.id_paciente) AS conteo
                             FROM tbl_pacientes p
                             JOIN tbl_sexos s ON p.id_sexo = s.id_sexo
                             GROUP BY s.nombre_sexo";
    $datos_reportes['pacientes_por_sexo'] = cargar_combo($query_pacientes_sexo);

    // RF11.1: Procedimientos más realizados (de todos los tiempos para el reporte)
    $query_procedimientos = "SELECT t.nombre_tratamiento, COUNT(pr.id_procedimiento_realizado) AS conteo
                             FROM tbl_procedimientos_realizados pr
                             JOIN tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento
                             GROUP BY t.nombre_tratamiento
                             ORDER BY conteo DESC
                             LIMIT 10";
    $datos_reportes['procedimientos_populares'] = cargar_combo($query_procedimientos);

    // RF6.4: Ingresos por mes
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
            $mes_index = $fila['mes'] - 1;
            $ingresos_anuales[$mes_index] = (float)$fila['total_ingresos'];
        }
    }
    
    $datos_reportes['ingresos_anuales'] = $ingresos_anuales;

    echo json_encode($datos_reportes);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}

$conexion->close();