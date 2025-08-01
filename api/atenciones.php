<?php
/**
 * Archivo: atenciones.php
 * Descripción: API REST para la gestión de atenciones, planes de tratamiento y procedimientos.
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
        if (isset($_GET['action']) && $_GET['action'] == 'generar_presupuesto' && isset($_GET['id_plan_tratamiento'])) {
            $id_plan = sanear_entrada($_GET['id_plan_tratamiento']);
            $presupuesto = [];

            // Obtener los datos de la clínica del archivo de configuración
            global $config;
            $presupuesto['clinica'] = $config->clinica;

            // Obtener los datos del paciente y el plan de tratamiento
            $query_paciente = "SELECT p.nombres, p.apellidos, p.numero_documento, p.telefono, p.correo_electronico 
                               FROM tbl_pacientes p
                               JOIN tbl_planes_tratamiento pt ON p.id_paciente = pt.id_paciente
                               WHERE pt.id_plan_tratamiento = ?";
            $stmt_paciente = $conexion->prepare($query_paciente);
            $stmt_paciente->bind_param("i", $id_plan);
            $stmt_paciente->execute();
            $resultado_paciente = $stmt_paciente->get_result();
            $presupuesto['paciente'] = $resultado_paciente->fetch_assoc();
            $stmt_paciente->close();

            // Obtener el listado de tratamientos del plan con el costo personalizado
            $query_tratamientos = "SELECT pr.id_procedimiento_realizado, t.nombre_tratamiento, pr.costo_personalizado
                                   FROM tbl_procedimientos_realizados pr
                                   JOIN tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento
                                   WHERE pr.id_plan_tratamiento = ?";
            $stmt_tratamientos = $conexion->prepare($query_tratamientos);
            $stmt_tratamientos->bind_param("i", $id_plan);
            $stmt_tratamientos->execute();
            $resultado_tratamientos = $stmt_tratamientos->get_result();
            $tratamientos = [];
            while ($fila = $resultado_tratamientos->fetch_assoc()) {
                $tratamientos[] = $fila;
            }
            $stmt_tratamientos->close();
            
            // Obtener los pagos asociados a cada procedimiento
            foreach ($tratamientos as &$tratamiento) {
                $query_pagos = "SELECT fecha_pago, monto, metodo_pago FROM tbl_pagos WHERE id_procedimiento_realizado = ?";
                $stmt_pagos = $conexion->prepare($query_pagos);
                $stmt_pagos->bind_param("i", $tratamiento['id_procedimiento_realizado']);
                $stmt_pagos->execute();
                $resultado_pagos = $stmt_pagos->get_result();
                $pagos_proc = [];
                while ($fila = $resultado_pagos->fetch_assoc()) {
                    $pagos_proc[] = $fila;
                }
                $stmt_pagos->close();
                $tratamiento['pagos'] = $pagos_proc;
            }
            $presupuesto['tratamientos'] = $tratamientos;

            echo json_encode($presupuesto);

        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción o ID de plan de tratamiento no válidos.']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($_GET['action'])) {
            $action = $_GET['action'];

            switch ($action) {
                case 'crear_plan_tratamiento':
                    // Lógica para crear un nuevo plan de tratamiento (RF3.2)
                    $id_paciente = sanear_entrada($data['id_paciente']);
                    $observaciones = sanear_entrada($data['observaciones']);

                    $conexion->begin_transaction();
                    try {
                        // Insertar en la tabla de planes de tratamiento
                        $stmt_plan = $conexion->prepare("INSERT INTO tbl_planes_tratamiento (id_paciente) VALUES (?)");
                        $stmt_plan->bind_param("i", $id_paciente);
                        $stmt_plan->execute();
                        $id_plan = $conexion->insert_id;
                        $stmt_plan->close();

                        // Asociar el diagnóstico y observaciones
                        // Para este caso, se actualizará el historial médico del paciente con la observación.
                        // RF1.5: Gestión de historia medica
                        $query_historial = "UPDATE tbl_historial_medico SET enfermedades_preexistentes = CONCAT(IFNULL(enfermedades_preexistentes, ''), '\n', ?), fecha_modificacion = NOW() WHERE id_paciente = ?";
                        $stmt_historial = $conexion->prepare($query_historial);
                        $stmt_historial->bind_param("si", $observaciones, $id_paciente);
                        $stmt_historial->execute();
                        $stmt_historial->close();
                        
                        $conexion->commit();
                        echo json_encode(['success' => true, 'message' => 'Plan de tratamiento creado con éxito.']);
                    } catch (Exception $e) {
                        $conexion->rollback();
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Error al crear el plan de tratamiento: ' . $e->getMessage()]);
                    }
                    break;

                case 'registrar_procedimiento':
                    // Lógica para registrar un procedimiento realizado (RF3.3)
                    $id_plan_tratamiento = sanear_entrada($data['id_plan_tratamiento']);
                    $id_tratamiento = sanear_entrada($data['id_tratamiento']);
                    $costo_personalizado = sanear_entrada($data['costo_personalizado']);
                    $notas_evolucion = sanear_entrada($data['notas_evolucion']);

                    $query = "INSERT INTO tbl_procedimientos_realizados (id_plan_tratamiento, id_tratamiento, costo_personalizado, notas_evolucion) VALUES (?, ?, ?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("iids", $id_plan_tratamiento, $id_tratamiento, $costo_personalizado, $notas_evolucion);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Procedimiento registrado con éxito.']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Error al registrar el procedimiento: ' . $stmt->error]);
                    }
                    $stmt->close();
                    break;

                case 'registrar_pago':
                    // Lógica para registrar un pago por procedimiento (RF6.1)
                    $id_paciente = sanear_entrada($data['id_paciente']);
                    $id_procedimiento_realizado = sanear_entrada($data['id_procedimiento_realizado']);
                    $monto = sanear_entrada($data['monto']);
                    $metodo_pago = sanear_entrada($data['metodo_pago']);
                    $tipo_pago = sanear_entrada($data['tipo_pago']);

                    if (empty($id_paciente) || empty($id_procedimiento_realizado) || empty($monto) || empty($metodo_pago) || empty($tipo_pago)) {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios para registrar el pago.']);
                        break;
                    }
                    
                    // Obtener el id_plan_tratamiento para el registro del pago
                    $query_plan_id = "SELECT id_plan_tratamiento FROM tbl_procedimientos_realizados WHERE id_procedimiento_realizado = ?";
                    $stmt_plan_id = $conexion->prepare($query_plan_id);
                    $stmt_plan_id->bind_param("i", $id_procedimiento_realizado);
                    $stmt_plan_id->execute();
                    $resultado_plan_id = $stmt_plan_id->get_result();
                    $fila_plan_id = $resultado_plan_id->fetch_assoc();
                    $id_plan_tratamiento = $fila_plan_id['id_plan_tratamiento'];
                    $stmt_plan_id->close();
                    
                    $query = "INSERT INTO tbl_pagos (id_paciente, id_plan_tratamiento, id_procedimiento_realizado, monto, metodo_pago, tipo_pago) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("iiidss", $id_paciente, $id_plan_tratamiento, $id_procedimiento_realizado, $monto, $metodo_pago, $tipo_pago);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Pago registrado con éxito.']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Error al registrar el pago: ' . $stmt->error]);
                    }
                    $stmt->close();
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
                    break;
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no especificada.']);
        }
        break;

    case 'PUT':
        // Lógica para editar un procedimiento (RF3.3)
        if (isset($_GET['action']) && $_GET['action'] == 'editar_procedimiento') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id_procedimiento = sanear_entrada($data['id_procedimiento_realizado']);
            $costo = sanear_entrada($data['costo_personalizado']);
            $notas = sanear_entrada($data['notas_evolucion']);

            if (!empty($id_procedimiento) && is_numeric($costo) && $costo >= 0) {
                $query = "UPDATE tbl_procedimientos_realizados SET costo_personalizado = ?, notas_evolucion = ? WHERE id_procedimiento_realizado = ?";
                $stmt = $conexion->prepare($query);
                $stmt->bind_param("dsi", $costo, $notas, $id_procedimiento);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Procedimiento actualizado con éxito.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el procedimiento: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Datos de procedimiento no válidos.']);
            }
        }
        break;

    case 'DELETE':
        if (isset($_GET['action'])) {
            $action = $_GET['action'];

            switch ($action) {
                case 'eliminar_procedimiento':
                    // Lógica para eliminar un procedimiento individual
                    if (isset($_GET['id'])) {
                        $id_procedimiento = sanear_entrada($_GET['id']);
                        $query = "DELETE FROM tbl_procedimientos_realizados WHERE id_procedimiento_realizado = ?";
                        $stmt = $conexion->prepare($query);
                        $stmt->bind_param("i", $id_procedimiento);

                        if ($stmt->execute()) {
                            echo json_encode(['success' => true, 'message' => 'Procedimiento eliminado con éxito.']);
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'message' => 'Error al eliminar el procedimiento: ' . $stmt->error]);
                        }
                        $stmt->close();
                    } else {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'ID de procedimiento no proporcionado.']);
                    }
                    break;
                
                case 'eliminar_plan_tratamiento':
                    // Lógica para eliminar un plan completo
                    if (isset($_GET['id'])) {
                        $id_plan = sanear_entrada($_GET['id']);
                        $conexion->begin_transaction();
                        try {
                            // La base de datos ya tiene ON DELETE CASCADE, por lo que solo necesitamos eliminar el plan
                            $query = "DELETE FROM tbl_planes_tratamiento WHERE id_plan_tratamiento = ?";
                            $stmt = $conexion->prepare($query);
                            $stmt->bind_param("i", $id_plan);
                            $stmt->execute();
                            $stmt->close();
                            $conexion->commit();
                            echo json_encode(['success' => true, 'message' => 'Plan de tratamiento y datos asociados eliminados con éxito.']);
                        } catch (Exception $e) {
                            $conexion->rollback();
                            http_response_code(500);
                            echo json_encode(['success' => false, 'message' => 'Error al eliminar el plan de tratamiento: ' . $e->getMessage()]);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'ID de plan de tratamiento no proporcionado.']);
                    }
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Acción de eliminación no válida.']);
                    break;
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción de eliminación no especificada.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();