<?php
/**
 * Archivo: tratamientos.php
 * Descripción: API REST para la gestión del catálogo de tratamientos.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/functions.php';
require_once '../core/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Lógica para listar tratamientos (action=listar)
        if (isset($_GET['action']) && $_GET['action'] == 'listar') {
            $query = "SELECT id_tratamiento, nombre_tratamiento, costo_base FROM tbl_tratamientos ORDER BY nombre_tratamiento";
            $tratamientos = cargar_combo($query);
            echo json_encode($tratamientos);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        }
        break;

    case 'POST':
        // Lógica para agregar un nuevo tratamiento (action=agregar)
        if (isset($_GET['action']) && $_GET['action'] == 'agregar') {
            $data = json_decode(file_get_contents('php://input'), true);
            $nombre_tratamiento = sanear_entrada($data['nombre_tratamiento']);
            $costo_base = sanear_entrada($data['costo_base']);

            if (!empty($nombre_tratamiento) && is_numeric($costo_base) && $costo_base >= 0) {
                $query = "INSERT INTO tbl_tratamientos (nombre_tratamiento, costo_base) VALUES (?, ?)";
                $stmt = $conexion->prepare($query);
                $stmt->bind_param("sd", $nombre_tratamiento, $costo_base);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Tratamiento agregado con éxito.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Error al agregar el tratamiento: ' . $stmt->error]);
                }
                $stmt->close();
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Datos de tratamiento no válidos.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
        }
        break;

    case 'DELETE':
        // Lógica para eliminar un tratamiento (action=eliminar)
        if (isset($_GET['action']) && $_GET['action'] == 'eliminar' && isset($_GET['id'])) {
            $id_tratamiento = sanear_entrada($_GET['id']);
            $query = "DELETE FROM tbl_tratamientos WHERE id_tratamiento = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_tratamiento);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Tratamiento eliminado con éxito.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el tratamiento: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción o ID de tratamiento no válidos.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();