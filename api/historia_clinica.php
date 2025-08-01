<?php
/**
 * Archivo: historia_clinica.php
 * Descripción: API REST para obtener y actualizar el historial clínico completo de un paciente.
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
    require_once '../core/config.php';
} catch (ErrorException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al cargar dependencias: ' . $e->getMessage()]);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
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

            // Obtener planes de tratamiento
            $query_planes = "SELECT * FROM tbl_planes_tratamiento WHERE id_paciente = ? ORDER BY fecha_creacion DESC";
            $stmt_planes = $conexion->prepare($query_planes);
            $stmt_planes->bind_param("i", $id_paciente);
            $stmt_planes->execute();
            $resultado_planes = $stmt_planes->get_result();
            $planes = [];
            while ($plan = $resultado_planes->fetch_assoc()) {
                $id_plan = $plan['id_plan_tratamiento'];
                
                // Obtener procedimientos del plan
                $query_procedimientos = "SELECT pr.id_procedimiento_realizado, t.nombre_tratamiento, pr.costo_personalizado, pr.notas_evolucion FROM tbl_procedimientos_realizados pr JOIN tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento WHERE id_plan_tratamiento = ?";
                $stmt_procedimientos = $conexion->prepare($query_procedimientos);
                $stmt_procedimientos->bind_param("i", $id_plan);
                $stmt_procedimientos->execute();
                $resultado_procedimientos = $stmt_procedimientos->get_result();
                $procedimientos = [];
                while ($proc = $resultado_procedimientos->fetch_assoc()) {
                    // Obtener pagos de cada procedimiento
                    $query_pagos_proc = "SELECT monto, fecha_pago, metodo_pago, tipo_pago FROM tbl_pagos WHERE id_procedimiento_realizado = ?";
                    $stmt_pagos_proc = $conexion->prepare($query_pagos_proc);
                    $stmt_pagos_proc->bind_param("i", $proc['id_procedimiento_realizado']);
                    $stmt_pagos_proc->execute();
                    $resultado_pagos_proc = $stmt_pagos_proc->get_result();
                    $pagos_proc = [];
                    while($pago = $resultado_pagos_proc->fetch_assoc()) {
                        $pagos_proc[] = $pago;
                    }
                    $stmt_pagos_proc->close();
                    $proc['pagos'] = $pagos_proc;
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
        break;

    case 'PUT':
        // Lógica para actualizar el historial médico (RF1.5)
        $data = json_decode(file_get_contents('php://input'), true);

        // Sanear y validar datos
        $id_paciente = sanear_entrada($data['id_paciente']);
        $enfermedades = sanear_entrada($data['enfermedades_preexistentes']);
        $alergias = sanear_entrada($data['alergias']);
        $medicacion = sanear_entrada($data['medicacion_actual']);
        $habitos = sanear_entrada($data['habitos']);

        $conexion->begin_transaction();
        try {
            // Intenta actualizar el registro existente
            $query_update = "UPDATE tbl_historial_medico SET enfermedades_preexistentes = ?, alergias = ?, medicacion_actual = ?, habitos = ?, fecha_modificacion = NOW() WHERE id_paciente = ?";
            $stmt_update = $conexion->prepare($query_update);
            $stmt_update->bind_param("ssssi", $enfermedades, $alergias, $medicacion, $habitos, $id_paciente);
            $stmt_update->execute();

            // Si no se afectaron filas, el registro no existía, así que lo insertamos
            if ($stmt_update->affected_rows === 0) {
                $query_insert = "INSERT INTO tbl_historial_medico (id_paciente, enfermedades_preexistentes, alergias, medicacion_actual, habitos) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conexion->prepare($query_insert);
                $stmt_insert->bind_param("issss", $id_paciente, $enfermedades, $alergias, $medicacion, $habitos);
                $stmt_insert->execute();
                $stmt_insert->close();
            }
            $stmt_update->close();

            $conexion->commit();
            echo json_encode(['success' => true, 'message' => 'Historial médico actualizado con éxito.']);
        } catch (Exception $e) {
            $conexion->rollback();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el historial médico: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();
