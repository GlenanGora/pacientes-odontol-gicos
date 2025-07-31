<?php
/**
 * Archivo: citas.php
 * Descripción: API REST para la gestión de citas.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/functions.php';
require_once '../core/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Lógica para obtener citas de un día específico o de un mes
        if (isset($_GET['fecha'])) {
            // Obtener citas por fecha (vista diaria)
            $fecha = sanear_entrada($_GET['fecha']);
            $query = "SELECT c.id_cita, c.fecha, c.hora, c.duracion, c.tipo_cita, c.estado,
                             p.nombres, p.apellidos,
                             CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente
                      FROM tbl_citas c
                      JOIN tbl_pacientes p ON c.id_paciente = p.id_paciente
                      WHERE c.fecha = '$fecha'
                      ORDER BY c.hora ASC";
            $citas = cargar_combo($query);
            echo json_encode($citas);
        } elseif (isset($_GET['mes'])) {
            // Obtener citas por mes (vista mensual)
            list($anio, $mes) = explode('-', sanear_entrada($_GET['mes']));
            $query = "SELECT c.id_cita, c.fecha, c.hora, c.duracion, c.tipo_cita, c.estado,
                             p.nombres, p.apellidos,
                             CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente
                      FROM tbl_citas c
                      JOIN tbl_pacientes p ON c.id_paciente = p.id_paciente
                      WHERE YEAR(c.fecha) = ? AND MONTH(c.fecha) = ?
                      ORDER BY c.fecha, c.hora ASC";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("ss", $anio, $mes);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $citas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $citas[] = $fila;
            }
            $stmt->close();
            echo json_encode($citas);
        } elseif (isset($_GET['pacientes'])) {
            // Obtener la lista de pacientes para el combo de agendar cita
            $query = "SELECT id_paciente AS id, CONCAT(nombres, ' ', apellidos) AS nombre FROM tbl_pacientes ORDER BY nombre";
            $pacientes = cargar_combo($query);
            echo json_encode($pacientes);
        } elseif (isset($_GET['id_cita'])) {
            // Obtener los detalles de una cita específica por su ID
            $id_cita = sanear_entrada($_GET['id_cita']);
            $query = "SELECT c.id_cita, c.fecha, c.hora, c.duracion, c.tipo_cita, c.estado,
                             CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente
                      FROM tbl_citas c
                      JOIN tbl_pacientes p ON c.id_paciente = p.id_paciente
                      WHERE c.id_cita = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_cita);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $cita = $resultado->fetch_assoc();
            $stmt->close();
            echo json_encode($cita);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Falta el parámetro de fecha, mes o ID de cita']);
        }
        break;

    case 'POST':
        // Lógica para agendar una nueva cita (RF2.1)
        $data = json_decode(file_get_contents('php://input'), true);

        // Sanear y validar datos
        $id_paciente = sanear_entrada($data['id_paciente']);
        $fecha = sanear_entrada($data['fecha']);
        $hora = sanear_entrada($data['hora']);
        $duracion = sanear_entrada($data['duracion']);
        $tipo_cita = sanear_entrada($data['tipo_cita']);

        // Verificación de disponibilidad de la cita
        $query_disponibilidad = "SELECT COUNT(*) AS total FROM tbl_citas WHERE fecha = ? AND hora = ?";
        $stmt_check = $conexion->prepare($query_disponibilidad);
        $stmt_check->bind_param("ss", $fecha, $hora);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result();
        $fila = $resultado_check->fetch_assoc();
        $stmt_check->close();

        if ($fila['total'] > 0) {
            http_response_code(409); // Conflict
            echo json_encode(['success' => false, 'message' => 'Ya existe una cita a esta hora. Por favor, elija otra.']);
            $conexion->close();
            exit();
        }

        // Inserta la nueva cita
        $query_insert = "INSERT INTO tbl_citas (id_paciente, fecha, hora, duracion, tipo_cita) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conexion->prepare($query_insert);
        $stmt_insert->bind_param("issis", $id_paciente, $fecha, $hora, $duracion, $tipo_cita);

        if ($stmt_insert->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cita agendada exitosamente.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al agendar la cita: ' . $stmt_insert->error]);
        }
        $stmt_insert->close();
        break;

    case 'PUT':
        // Lógica para modificar una cita (RF2.3)
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Sanear y validar datos
        $id_cita = sanear_entrada($data['id_cita']);
        $estado = sanear_entrada($data['estado']);
        $fecha = isset($data['fecha']) ? sanear_entrada($data['fecha']) : null;
        $hora = isset($data['hora']) ? sanear_entrada($data['hora']) : null;
        $duracion = isset($data['duracion']) ? sanear_entrada($data['duracion']) : null;
        $tipo_cita = isset($data['tipo_cita']) ? sanear_entrada($data['tipo_cita']) : null;

        $query_parts = [];
        $params = [];
        $types = "";

        if ($estado) {
            $query_parts[] = "estado = ?";
            $params[] = $estado;
            $types .= "s";
        }
        if ($fecha) {
            $query_parts[] = "fecha = ?";
            $params[] = $fecha;
            $types .= "s";
        }
        if ($hora) {
            $query_parts[] = "hora = ?";
            $params[] = $hora;
            $types .= "s";
        }
        if ($duracion) {
            $query_parts[] = "duracion = ?";
            $params[] = $duracion;
            $types .= "i";
        }
        if ($tipo_cita) {
            $query_parts[] = "tipo_cita = ?";
            $params[] = $tipo_cita;
            $types .= "s";
        }

        if (empty($query_parts)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No se proporcionaron datos para actualizar.']);
            $conexion->close();
            exit();
        }

        $query = "UPDATE tbl_citas SET " . implode(", ", $query_parts) . " WHERE id_cita = ?";
        $params[] = $id_cita;
        $types .= "i";

        $stmt = $conexion->prepare($query);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cita actualizada exitosamente.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la cita: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'DELETE':
        // Lógica para eliminar una cita (borrado físico)
        if (isset($_GET['id'])) {
            $id_cita = sanear_entrada($_GET['id']);
            $query = "DELETE FROM tbl_citas WHERE id_cita = '$id_cita'";
            if ($conexion->query($query)) {
                echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la cita: ' . $conexion->error]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Falta el ID de la cita.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();
