<?php
/**
 * Archivo: recetas.php
 * Descripción: API REST para la gestión de recetas médicas.
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

switch ($metodo) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] == 'listar') {
            $filtros_sql = [];
            $params = [];
            $types = "";
            $limit = isset($_GET['limit']) ? sanear_entrada($_GET['limit']) : 50;
            $page = isset($_GET['page']) ? sanear_entrada($_GET['page']) : 1;
            $offset = ($page - 1) * $limit;
            
            // Lógica para listar el historial de recetas con filtros
            if (isset($_GET['paciente']) && !empty($_GET['paciente'])) {
                $paciente = sanear_entrada($_GET['paciente']);
                $filtros_sql[] = "CONCAT(p.nombres, ' ', p.apellidos) LIKE ?";
                $params[] = "%$paciente%";
                $types .= "s";
            }

            if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
                $fecha = sanear_entrada($_GET['fecha']);
                $filtros_sql[] = "r.fecha_emision LIKE ?";
                $params[] = "$fecha%";
                $types .= "s";
            }
            
            $where_clause = !empty($filtros_sql) ? " WHERE " . implode(" AND ", $filtros_sql) : "";

            // Consulta para obtener el total de registros
            $query_total = "SELECT COUNT(*) AS total
                            FROM tbl_recetas r
                            JOIN tbl_recetas_detalle rd ON r.id_receta = rd.id_receta
                            JOIN tbl_pacientes p ON r.id_paciente = p.id_paciente
                            LEFT JOIN tbl_procedimientos_realizados pr ON r.id_procedimiento_realizado = pr.id_procedimiento_realizado
                            LEFT JOIN tbl_planes_tratamiento pt ON pr.id_plan_tratamiento = pt.id_plan_tratamiento
                            LEFT JOIN tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento
                            LEFT JOIN tbl_diagnosticos d ON pt.id_diagnostico = d.id_diagnostico" . $where_clause;
            
            $stmt_total = $conexion->prepare($query_total);
            if (!empty($params)) {
                $stmt_total->bind_param($types, ...$params);
            }
            $stmt_total->execute();
            $total_records = $stmt_total->get_result()->fetch_assoc()['total'];
            $stmt_total->close();

            // Consulta para obtener los datos paginados
            $query_data = "SELECT 
                                r.id_receta, 
                                r.fecha_emision, 
                                CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente,
                                t.nombre_tratamiento,
                                d.nombre_diagnostico,
                                rd.nombre_medicamento,
                                rd.dosis,
                                rd.frecuencia,
                                rd.duracion
                           FROM tbl_recetas r
                           JOIN tbl_recetas_detalle rd ON r.id_receta = rd.id_receta
                           JOIN tbl_pacientes p ON r.id_paciente = p.id_paciente
                           LEFT JOIN tbl_procedimientos_realizados pr ON r.id_procedimiento_realizado = pr.id_procedimiento_realizado
                           LEFT JOIN tbl_planes_tratamiento pt ON pr.id_plan_tratamiento = pt.id_plan_tratamiento
                           LEFT JOIN tbl_tratamientos t ON pr.id_tratamiento = t.id_tratamiento
                           LEFT JOIN tbl_diagnosticos d ON pt.id_diagnostico = d.id_diagnostico"
                           . $where_clause . 
                           " ORDER BY r.fecha_emision DESC
                           LIMIT ?, ?";
            
            $params_data = array_merge($params, [$offset, $limit]);
            $types_data = $types . "ii";

            $stmt_data = $conexion->prepare($query_data);
            $stmt_data->bind_param($types_data, ...$params_data);
            $stmt_data->execute();
            $resultado_data = $stmt_data->get_result();
            $recetas = [];
            while ($fila = $resultado_data->fetch_assoc()) {
                $recetas[] = $fila;
            }
            $stmt_data->close();
            
            echo json_encode(['data' => $recetas, 'total' => $total_records]);

        } elseif (isset($_GET['action']) && $_GET['action'] == 'detalle' && isset($_GET['id_receta'])) {
            // Lógica para obtener los detalles de una receta
            $id_receta = sanear_entrada($_GET['id_receta']);
            $detalle = [];

            $query_receta = "SELECT r.*, CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente
                             FROM tbl_recetas r
                             JOIN tbl_pacientes p ON r.id_paciente = p.id_paciente
                             WHERE r.id_receta = ?";
            $stmt_receta = $conexion->prepare($query_receta);
            $stmt_receta->bind_param("i", $id_receta);
            $stmt_receta->execute();
            $resultado_receta = $stmt_receta->get_result();
            $detalle['receta'] = $resultado_receta->fetch_assoc();
            $stmt_receta->close();

            $query_medicamentos = "SELECT * FROM tbl_recetas_detalle WHERE id_receta = ?";
            $stmt_medicamentos = $conexion->prepare($query_medicamentos);
            $stmt_medicamentos->bind_param("i", $id_receta);
            $stmt_medicamentos->execute();
            $resultado_medicamentos = $stmt_medicamentos->get_result();
            $medicamentos = [];
            while($med = $resultado_medicamentos->fetch_assoc()) {
                $medicamentos[] = $med;
            }
            $stmt_medicamentos->close();
            $detalle['medicamentos'] = $medicamentos;
            
            echo json_encode($detalle);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida o parámetros faltantes.']);
        }
        break;

    case 'POST':
        if (isset($_GET['action']) && $_GET['action'] == 'emitir') {
            $data = json_decode(file_get_contents('php://input'), true);

            $id_paciente = sanear_entrada($data['id_paciente']);
            $id_procedimiento_realizado = sanear_entrada($data['id_procedimiento_realizado']);
            $indicaciones_generales = sanear_entrada($data['indicaciones_generales']);
            $medicamentos = $data['medicamentos'];

            if (empty($id_paciente) || empty($id_procedimiento_realizado) || empty($medicamentos)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios para emitir la receta.']);
                exit();
            }

            $conexion->begin_transaction();
            try {
                // Insertar en la tabla de recetas
                $query_receta = "INSERT INTO tbl_recetas (id_paciente, id_procedimiento_realizado, indicaciones_generales) VALUES (?, ?, ?)";
                $stmt_receta = $conexion->prepare($query_receta);
                $stmt_receta->bind_param("iis", $id_paciente, $id_procedimiento_realizado, $indicaciones_generales);
                $stmt_receta->execute();
                $id_receta = $conexion->insert_id;
                $stmt_receta->close();

                // Insertar los detalles de los medicamentos
                $query_medicamento = "INSERT INTO tbl_recetas_detalle (id_receta, nombre_medicamento, dosis, frecuencia, duracion) VALUES (?, ?, ?, ?, ?)";
                $stmt_medicamento = $conexion->prepare($query_medicamento);

                foreach ($medicamentos as $med) {
                    $nombre = sanear_entrada($med['nombre']);
                    $dosis = sanear_entrada($med['dosis']);
                    // Se verifica si la clave 'frecuencia' y 'duracion' existen antes de acceder a ellas
                    $frecuencia = isset($med['frecuencia']) ? sanear_entrada($med['frecuencia']) : NULL;
                    $duracion = isset($med['duracion']) ? sanear_entrada($med['duracion']) : NULL;
                    $stmt_medicamento->bind_param("issss", $id_receta, $nombre, $dosis, $frecuencia, $duracion);
                    $stmt_medicamento->execute();
                }
                $stmt_medicamento->close();

                $conexion->commit();
                echo json_encode(['success' => true, 'message' => 'Receta emitida con éxito.', 'id_receta' => $id_receta]);

            } catch (Exception $e) {
                $conexion->rollback();
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al emitir la receta: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();
