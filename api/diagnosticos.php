<?php
/**
 * Archivo: diagnosticos.php
 * Descripción: API REST para la gestión del catálogo de diagnósticos.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/functions.php';
require_once '../core/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Lógica para listar diagnósticos (action=listar)
        if (isset($_GET['action']) && $_GET['action'] == 'listar') {
            $query = "SELECT id_diagnostico, nombre_diagnostico FROM tbl_diagnosticos ORDER BY nombre_diagnostico";
            $diagnosticos = cargar_combo($query);
            echo json_encode($diagnosticos);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        }
        break;

    case 'POST':
        // Lógica para agregar un nuevo diagnóstico (action=agregar)
        if (isset($_GET['action']) && $_GET['action'] == 'agregar') {
            $data = json_decode(file_get_contents('php://input'), true);
            $nombre_diagnostico = sanear_entrada($data['nombre_diagnostico']);

            if (!empty($nombre_diagnostico)) {
                $query = "INSERT INTO tbl_diagnosticos (nombre_diagnostico) VALUES (?)";
                $stmt = $conexion->prepare($query);
                $stmt->bind_param("s", $nombre_diagnostico);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Diagnóstico agregado con éxito.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error al agregar el diagnóstico: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El nombre del diagnóstico no puede estar vacío.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        }
        break;

    case 'PUT':
        // Lógica para editar un diagnóstico (action=editar)
        if (isset($_GET['action']) && $_GET['action'] == 'editar' && isset($_GET['id'])) {
            $id_diagnostico = sanear_entrada($_GET['id']);
            $data = json_decode(file_get_contents('php://input'), true);
            $nombre_diagnostico = sanear_entrada($data['nombre_diagnostico']);

            if (!empty($nombre_diagnostico)) {
                $query = "UPDATE tbl_diagnosticos SET nombre_diagnostico = ? WHERE id_diagnostico = ?";
                $stmt = $conexion->prepare($query);
                $stmt->bind_param("si", $nombre_diagnostico, $id_diagnostico);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Diagnóstico actualizado con éxito.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el diagnóstico: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El nombre del diagnóstico no puede estar vacío.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción o ID de diagnóstico no válidos.']);
        }
        break;

    case 'DELETE':
        // Lógica para eliminar un diagnóstico (action=eliminar)
        if (isset($_GET['action']) && $_GET['action'] == 'eliminar' && isset($_GET['id'])) {
            $id_diagnostico = sanear_entrada($_GET['id']);
            $query = "DELETE FROM tbl_diagnosticos WHERE id_diagnostico = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_diagnostico);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Diagnóstico eliminado con éxito.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el diagnóstico: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción o ID de diagnóstico no válidos.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();