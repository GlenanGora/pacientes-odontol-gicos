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
            // Lógica para listar el historial de recetas
            $query = "SELECT r.id_receta, r.fecha_emision, p.nombres, p.apellidos, CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente
                      FROM tbl_recetas r
                      JOIN tbl_pacientes p ON r.id_paciente = p.id_paciente
                      ORDER BY r.fecha_emision DESC";
            $recetas = cargar_combo($query);
            echo json_encode($recetas);
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
            $indicaciones_generales = sanear_entrada($data['indicaciones_generales']);
            $medicamentos = $data['medicamentos'];

            if (empty($id_paciente) || empty($medicamentos)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios para emitir la receta.']);
                exit();
            }

            $conexion->begin_transaction();
            try {
                // Insertar en la tabla de recetas
                $query_receta = "INSERT INTO tbl_recetas (id_paciente, indicaciones_generales) VALUES (?, ?)";
                $stmt_receta = $conexion->prepare($query_receta);
                $stmt_receta->bind_param("is", $id_paciente, $indicaciones_generales);
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
