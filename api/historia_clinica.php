<?php
/**
 * Archivo: historia_clinica.php
 * Descripción: API REST para obtener el historial clínico completo de un paciente.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/functions.php';
require_once '../core/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    if (isset($_GET['id_paciente'])) {
        $id_paciente = sanear_entrada($_GET['id_paciente']);
        $datos_historial = [];

        // Obtener datos del paciente
        $query_paciente = "SELECT p.*, ep.nombre_estado FROM tbl_pacientes p JOIN tbl_estados_paciente ep ON p.id_estado = ep.id_estado WHERE id_paciente = ?";
        $stmt_paciente = $conexion->prepare($query_paciente);
        $stmt_paciente->bind_param("i", $id_paciente);
        $stmt_paciente->execute();
        $resultado_paciente = $stmt_paciente->get_result();
        $datos_historial['paciente'] = $resultado_paciente->fetch_assoc();
        $stmt_paciente->close();

        // Obtener historial médico
        $query_historial_medico = "SELECT * FROM tbl_historial_medico WHERE id_paciente = ?";
        $stmt_historial = $conexion->prepare($query_historial_medico);
        $stmt_historial->bind_param("i", $id_paciente);
        $stmt_historial->execute();
        $resultado_historial = $stmt_historial->get_result();
        $datos_historial['historial_medico'] = $resultado_historial->fetch_assoc();
        $stmt_historial->close();

        // Obtener planes de tratamiento y sus procedimientos
        $query_planes = "SELECT * FROM tbl_planes_tratamiento WHERE id_paciente = ? ORDER BY fecha_creacion DESC";
        $stmt_planes = $conexion->prepare($query_planes);
        $stmt_planes->bind_param("i", $id_paciente);
        $stmt_planes->execute();
        $resultado_planes = $stmt_planes->get_result();
        $planes = [];
        while ($plan = $resultado_planes->fetch_assoc()) {
            $id_plan = $plan['id_plan_tratamiento'];
            $query_procedimientos = "SELECT pr.*, t.nombre_tratamiento FROM tbl_procedimientos_realizados pr JOIN tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento WHERE id_plan_tratamiento = ?";
            $stmt_procedimientos = $conexion->prepare($query_procedimientos);
            $stmt_procedimientos->bind_param("i", $id_plan);
            $stmt_procedimientos->execute();
            $resultado_procedimientos = $stmt_procedimientos->get_result();
            $procedimientos = [];
            while ($proc = $resultado_procedimientos->fetch_assoc()) {
                $procedimientos[] = $proc;
            }
            $stmt_procedimientos->close();
            $plan['procedimientos'] = $procedimientos;
            $planes[] = $plan;
        }
        $stmt_planes->close();
        $datos_historial['planes_tratamiento'] = $planes;

        // Obtener historial de pagos
        $query_pagos = "SELECT * FROM tbl_pagos WHERE id_paciente = ? ORDER BY fecha_pago DESC";
        $stmt_pagos = $conexion->prepare($query_pagos);
        $stmt_pagos->bind_param("i", $id_paciente);
        $stmt_pagos->execute();
        $resultado_pagos = $stmt_pagos->get_result();
        $pagos = [];
        while ($pago = $resultado_pagos->fetch_assoc()) {
            $pagos[] = $pago;
        }
        $stmt_pagos->close();
        $datos_historial['pagos'] = $pagos;

        echo json_encode($datos_historial);

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de paciente no proporcionado.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}

$conexion->close();

