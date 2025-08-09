<?php
/**
 * Archivo: odontograma.php
 * Descripción: API REST para la gestión de odontogramas.
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
        if (isset($_GET['id_paciente'])) {
            $id_paciente = sanear_entrada($_GET['id_paciente']);
            
            // Obtener odontograma existente
            $query = "SELECT * FROM tbl_odontograma WHERE id_paciente = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_paciente);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $odontograma = $resultado->fetch_assoc();
            $stmt->close();

            if ($odontograma) {
                $odontograma['odontograma_data'] = json_decode($odontograma['odontograma_data'], true);
                echo json_encode(['success' => true, 'odontograma_data' => $odontograma['odontograma_data'], 'tipo_denticion' => $odontograma['tipo_denticion']]);
            } else {
                echo json_encode(['success' => true, 'odontograma_data' => null, 'tipo_denticion' => 'adulto']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de paciente no proporcionado.']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['id_paciente']) && isset($data['odontograma_data']) && isset($data['tipo_denticion'])) {
            $id_paciente = sanear_entrada($data['id_paciente']);
            $tipo_denticion = sanear_entrada($data['tipo_denticion']);
            $odontograma_data_json = $data['odontograma_data'];

            // Verificar si ya existe un odontograma para el paciente
            $query_check = "SELECT id_odontograma FROM tbl_odontograma WHERE id_paciente = ?";
            $stmt_check = $conexion->prepare($query_check);
            $stmt_check->bind_param("i", $id_paciente);
            $stmt_check->execute();
            $resultado_check = $stmt_check->get_result();

            if ($resultado_check->num_rows > 0) {
                // Actualizar odontograma existente
                $query_update = "UPDATE tbl_odontograma SET tipo_denticion = ?, odontograma_data = ?, fecha_modificacion = NOW() WHERE id_paciente = ?";
                $stmt_update = $conexion->prepare($query_update);
                $stmt_update->bind_param("ssi", $tipo_denticion, $odontograma_data_json, $id_paciente);
                if ($stmt_update->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Odontograma actualizado con éxito.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el odontograma: ' . $stmt_update->error]);
                }
                $stmt_update->close();
            } else {
                // Crear nuevo odontograma
                $query_insert = "INSERT INTO tbl_odontograma (id_paciente, tipo_denticion, odontograma_data) VALUES (?, ?, ?)";
                $stmt_insert = $conexion->prepare($query_insert);
                $stmt_insert->bind_param("iss", $id_paciente, $tipo_denticion, $odontograma_data_json);
                if ($stmt_insert->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Odontograma guardado con éxito.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error al guardar el odontograma: ' . $stmt_insert->error]);
                }
                $stmt_insert->close();
            }
            $resultado_check->close();
            $stmt_check->close();
            
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos de odontograma incompletos.']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();
