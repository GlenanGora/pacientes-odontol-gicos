<?php
/**
 * Archivo: atenciones.php
 * Descripción: API REST para la gestión de atenciones, planes de tratamiento y procedimientos.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/functions.php';
require_once '../core/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
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
                        $query_historial = "UPDATE tbl_historial_medico SET enfermedades_preexistentes = CONCAT(enfermedades_preexistentes, '\n', ?), fecha_modificacion = NOW() WHERE id_paciente = ?";
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
                    $notas_evolucion = sanear_entrada($data['notas_evolucion']);

                    $query = "INSERT INTO tbl_procedimientos_realizados (id_plan_tratamiento, id_tratamiento, notas_evolucion) VALUES (?, ?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("iis", $id_plan_tratamiento, $id_tratamiento, $notas_evolucion);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Procedimiento registrado con éxito.']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Error al registrar el procedimiento: ' . $stmt->error]);
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

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();