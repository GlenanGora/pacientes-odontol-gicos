<?php
/**
 * Archivo: reportes.php
 * Descripción: API REST para obtener datos estadísticos para la página de reportes.
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
    $datos_reportes = [];
    $anio_filtro = isset($_GET['anio']) ? sanear_entrada($_GET['anio']) : date('Y');

    // Ingresos por mes
    $ingresos_anuales = array_fill(0, 12, 0); // Inicializa un array con 12 meses en cero
    $query_ingresos_anuales = "SELECT MONTH(fecha_pago) AS mes, SUM(monto) AS total_ingresos
                               FROM tbl_pagos
                               WHERE YEAR(fecha_pago) = '{$anio_filtro}'
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
    
    // Deudas por mes - Lógica corregida
    $deudas_anuales = array_fill(0, 12, 0);
    $query_deudas_anuales = "SELECT
                                MONTH(fecha_creacion) AS mes,
                                SUM(saldo_pendiente) AS total_deuda
                             FROM
                                (SELECT
                                    pt.fecha_creacion,
                                    (SELECT SUM(costo_personalizado) FROM tbl_procedimientos_realizados WHERE id_plan_tratamiento = pt.id_plan_tratamiento) - (SELECT IFNULL(SUM(monto), 0) FROM tbl_pagos WHERE id_plan_tratamiento = pt.id_plan_tratamiento) AS saldo_pendiente
                                 FROM
                                    tbl_planes_tratamiento pt
                                 WHERE
                                    YEAR(pt.fecha_creacion) = '{$anio_filtro}') AS deudas_por_plan
                             WHERE saldo_pendiente > 0
                             GROUP BY mes
                             ORDER BY mes ASC";
    $result_deudas = $conexion->query($query_deudas_anuales);
    if ($result_deudas) {
        while ($fila = $result_deudas->fetch_assoc()) {
            $mes_index = $fila['mes'] - 1;
            $deudas_anuales[$mes_index] = (float)$fila['total_deuda'];
        }
    }
    $datos_reportes['deudas_anuales'] = $deudas_anuales;


    // RF11.1: Estadísticas de atención por sexo
    $query_pacientes_sexo = "SELECT s.nombre_sexo, COUNT(p.id_paciente) AS conteo
                             FROM tbl_pacientes p
                             JOIN tbl_sexos s ON p.id_sexo = s.id_sexo
                             GROUP BY s.nombre_sexo";
    $datos_reportes['pacientes_por_sexo'] = cargar_combo($query_pacientes_sexo);

    // RF11.1: Estadísticas por diagnóstico
    $query_diagnosticos = "SELECT d.nombre_diagnostico, COUNT(pt.id_plan_tratamiento) AS conteo
                           FROM tbl_planes_tratamiento pt
                           JOIN tbl_diagnosticos d ON pt.id_diagnostico = d.id_diagnostico
                           WHERE YEAR(pt.fecha_creacion) = '{$anio_filtro}'
                           GROUP BY d.nombre_diagnostico
                           ORDER BY conteo DESC";
    $datos_reportes['estadisticas_diagnostico'] = cargar_combo($query_diagnosticos);

    // RF11.1: Procedimientos más realizados por mes
    $query_procedimientos_mes = "SELECT
                                    MONTH(pr.fecha_realizacion) AS mes,
                                    t.nombre_tratamiento,
                                    COUNT(*) AS conteo
                                 FROM
                                    tbl_procedimientos_realizados pr
                                 JOIN
                                    tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento
                                 WHERE
                                    YEAR(pr.fecha_realizacion) = '{$anio_filtro}'
                                 GROUP BY
                                    mes, nombre_tratamiento
                                 ORDER BY
                                    mes, conteo DESC";
    $datos_reportes['procedimientos_por_mes'] = cargar_combo($query_procedimientos_mes);

    // RF6.4: Pacientes con deuda pendiente - Lógica corregida
    $query_pacientes_deuda = "SELECT
                                CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
                                d.nombre_diagnostico AS nombre_diagnostico,
                                (SELECT SUM(costo_personalizado) FROM tbl_procedimientos_realizados WHERE id_plan_tratamiento = pt.id_plan_tratamiento) AS costo_total_plan,
                                (SELECT SUM(monto) FROM tbl_pagos WHERE id_plan_tratamiento = pt.id_plan_tratamiento) AS pagos_realizados,
                                ( (SELECT SUM(costo_personalizado) FROM tbl_procedimientos_realizados WHERE id_plan_tratamiento = pt.id_plan_tratamiento) - (SELECT IFNULL(SUM(monto), 0) FROM tbl_pagos WHERE id_plan_tratamiento = pt.id_plan_tratamiento) ) AS saldo_pendiente
                              FROM
                                tbl_pacientes p
                              JOIN
                                tbl_planes_tratamiento pt ON p.id_paciente = pt.id_paciente
                              LEFT JOIN
                                tbl_diagnosticos d ON pt.id_diagnostico = d.id_diagnostico
                              HAVING
                                saldo_pendiente > 0
                              ORDER BY
                                saldo_pendiente DESC";
    $datos_reportes['pacientes_con_deuda'] = cargar_combo($query_pacientes_deuda);

    // RF2.1: Próximas citas (futuras, ordenadas por fecha y hora)
    $query_proximas_citas = "SELECT c.fecha, c.hora, c.tipo_cita, CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente
                             FROM tbl_citas c
                             JOIN tbl_pacientes p ON c.id_paciente = p.id_paciente
                             WHERE c.fecha >= CURDATE()
                             ORDER BY c.fecha, c.hora ASC
                             LIMIT 10";
    $datos_reportes['proximas_citas'] = cargar_combo($query_proximas_citas);

    echo json_encode($datos_reportes);

} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}

$conexion->close();
