<?php
/**
 * Archivo: pacientes.php
 * Descripción: API REST para la gestión de pacientes. Permite registrar, listar, editar, buscar y eliminar.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');

// Configuración de manejo de errores para evitar que se imprima HTML
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Convertir el error a una excepción para poder capturarlo
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Incluye el archivo de conexión y funciones auxiliares
try {
    require_once '../core/functions.php';
    require_once '../core/db.php';
} catch (ErrorException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al cargar dependencias: ' . $e->getMessage()]);
    exit();
}

// Verificación de método de petición
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Lógica para obtener pacientes con filtros
        if (isset($_GET['id'])) {
            // Obtener un solo paciente por ID
            $id_paciente = sanear_entrada($_GET['id']);
            $query = "SELECT
                        p.id_paciente,
                        p.nombres,
                        p.apellidos,
                        p.fecha_nacimiento,
                        p.id_documento_tipo,
                        p.numero_documento,
                        p.direccion,
                        p.telefono,
                        p.correo_electronico,
                        p.id_sexo,
                        p.id_departamento,
                        p.id_provincia,
                        p.id_distrito,
                        p.observaciones_generales,
                        p.fecha_registro,
                        p.fecha_modificacion,
                        p.id_estado,
                        c.nombre_contacto AS contacto_emergencia_nombre,
                        c.telefono_contacto AS contacto_emergencia_telefono,
                        TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad,
                        (SELECT MAX(fecha) FROM tbl_citas WHERE id_paciente = p.id_paciente) AS fecha_ultima_atencion,
                        (SELECT nombre_tratamiento FROM tbl_tratamientos t JOIN tbl_procedimientos_realizados pr ON t.id_tratamiento = pr.id_tratamiento JOIN tbl_planes_tratamiento pt ON pr.id_plan_tratamiento = pt.id_plan_tratamiento WHERE pt.id_paciente = p.id_paciente ORDER BY pr.fecha_realizacion DESC LIMIT 1) AS ultimo_tratamiento
                      FROM tbl_pacientes p
                      LEFT JOIN tbl_contactos_emergencia c ON p.id_paciente = c.id_paciente
                      WHERE p.id_paciente = '$id_paciente'";

            $paciente = cargar_combo($query);
            echo json_encode($paciente[0] ?? []);
        } elseif (isset($_GET['combo_data'])) {
            // Obtener datos para combos de forma centralizada
            echo obtener_datos_combos();
        } elseif (isset($_GET['departamento_id'])) {
            // Obtener provincias por departamento
            echo obtener_provincias_por_departamento($_GET['departamento_id']);
        } elseif (isset($_GET['provincia_id'])) {
            // Obtener distritos por provincia
            echo obtener_distritos_por_provincia($_GET['provincia_id']);
        } else {
            // Obtener todos los pacientes con filtros y paginación (RF1.2)
            $filtros_sql = [];
            $params = [];
            $types = "";
            $limit = isset($_GET['limit']) ? sanear_entrada($_GET['limit']) : 50;
            $page = isset($_GET['page']) ? sanear_entrada($_GET['page']) : 1;
            $offset = ($page - 1) * $limit;

            if (isset($_GET['nombre']) && !empty($_GET['nombre'])) {
                $nombre = sanear_entrada($_GET['nombre']);
                $filtros_sql[] = "CONCAT(p.nombres, ' ', p.apellidos) LIKE ? OR p.numero_documento LIKE ?";
                $params[] = "%$nombre%";
                $params[] = "%$nombre%";
                $types .= "ss";
            }

            if (isset($_GET['estado']) && !empty($_GET['estado'])) {
                $estado_id = sanear_entrada($_GET['estado']);
                $filtros_sql[] = "p.id_estado = ?";
                $params[] = $estado_id;
                $types .= "i";
            }
            
            $where_clause = !empty($filtros_sql) ? " WHERE " . implode(" AND ", $filtros_sql) : "";
            
            // Consulta para obtener el total de registros
            $query_total = "SELECT COUNT(*) AS total FROM tbl_pacientes p " . $where_clause;
            
            $stmt_total = $conexion->prepare($query_total);
            if (!empty($params)) {
                $stmt_total->bind_param($types, ...$params);
            }
            $stmt_total->execute();
            $total_records = $stmt_total->get_result()->fetch_assoc()['total'];
            $stmt_total->close();


            // Consulta para obtener los datos paginados
            $query_data = "SELECT
                                p.id_paciente,
                                CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
                                p.telefono,
                                TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS edad,
                                ep.nombre_estado AS estado,
                                (SELECT MAX(fecha) FROM tbl_citas WHERE id_paciente = p.id_paciente) AS fecha_ultima_atencion,
                                (SELECT nombre_tratamiento FROM tbl_tratamientos t JOIN tbl_procedimientos_realizados pr ON t.id_tratamiento = pr.id_tratamiento JOIN tbl_planes_tratamiento pt ON pr.id_plan_tratamiento = pt.id_plan_tratamiento WHERE pt.id_paciente = p.id_paciente ORDER BY pr.fecha_realizacion DESC LIMIT 1) AS ultimo_tratamiento
                             FROM tbl_pacientes p
                             INNER JOIN tbl_estados_paciente ep ON p.id_estado = ep.id_estado
                             $where_clause
                             ORDER BY p.fecha_modificacion DESC
                             LIMIT ?, ?";
            
            $params_data = array_merge($params, [$offset, $limit]);
            $types_data = $types . "ii";

            $stmt_data = $conexion->prepare($query_data);
            $stmt_data->bind_param($types_data, ...$params_data);
            $stmt_data->execute();
            $resultado_data = $stmt_data->get_result();
            $pacientes = [];
            while ($fila = $resultado_data->fetch_assoc()) {
                $pacientes[] = $fila;
            }
            $stmt_data->close();
            
            echo json_encode(['data' => $pacientes, 'total' => $total_records]);
        }
        break;

    case 'POST':
        // Lógica para registrar un nuevo paciente (RF1.1)
        $data = json_decode(file_get_contents('php://input'), true);

        // Sanear y validar datos
        $nombres = sanear_entrada($data['nombres']);
        $apellidos = sanear_entrada($data['apellidos']);
        $fecha_nacimiento = sanear_entrada($data['fecha_nacimiento']);
        $id_documento_tipo = sanear_entrada($data['id_documento_tipo']);
        $numero_documento = sanear_entrada($data['numero_documento']);
        $direccion = sanear_entrada($data['direccion']);
        $telefono = sanear_entrada($data['telefono']);
        $correo_electronico = sanear_entrada($data['correo_electronico']);
        $id_sexo = sanear_entrada($data['id_sexo']);
        $id_departamento = sanear_entrada($data['id_departamento']);
        $id_provincia = sanear_entrada($data['id_provincia']);
        $id_distrito = sanear_entrada($data['id_distrito']);
        $observaciones_generales = sanear_entrada($data['observaciones_generales']);
        $contacto_emergencia_nombre = sanear_entrada($data['contacto_emergencia_nombre']);
        $contacto_emergencia_telefono = sanear_entrada($data['contacto_emergencia_telefono']);
        $id_estado = sanear_entrada($data['id_estado']);

        // Utiliza transacciones para asegurar la consistencia de los datos
        $conexion->begin_transaction();
        try {
            // Inserta en la tabla de pacientes
            $stmt = $conexion->prepare("INSERT INTO tbl_pacientes (nombres, apellidos, fecha_nacimiento, id_documento_tipo, numero_documento, direccion, telefono, correo_electronico, id_sexo, id_departamento, id_provincia, id_distrito, observaciones_generales, id_estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissssiiiiis", $nombres, $apellidos, $fecha_nacimiento, $id_documento_tipo, $numero_documento, $direccion, $telefono, $correo_electronico, $id_sexo, $id_departamento, $id_provincia, $id_distrito, $observaciones_generales, $id_estado);
            $stmt->execute();
            $id_paciente = $conexion->insert_id;
            $stmt->close();

            // Inserta el contacto de emergencia si existe
            if (!empty($contacto_emergencia_nombre) && !empty($contacto_emergencia_telefono)) {
                $stmt_contacto = $conexion->prepare("INSERT INTO tbl_contactos_emergencia (id_paciente, nombre_contacto, telefono_contacto) VALUES (?, ?, ?)");
                $stmt_contacto->bind_param("iss", $id_paciente, $contacto_emergencia_nombre, $contacto_emergencia_telefono);
                $stmt_contacto->execute();
                $stmt_contacto->close();
            }

            // Crear un registro inicial en la tabla tbl_historial_medico para el nuevo paciente
            $stmt_historial = $conexion->prepare("INSERT INTO tbl_historial_medico (id_paciente) VALUES (?)");
            $stmt_historial->bind_param("i", $id_paciente);
            $stmt_historial->execute();
            $stmt_historial->close();


            $conexion->commit();
            echo json_encode(['success' => true, 'message' => 'Paciente registrado exitosamente.']);
        } catch (Exception $e) {
            $conexion->rollback();
            echo json_encode(['success' => false, 'message' => 'Error al registrar paciente: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        // Lógica para editar un paciente (RF1.3)
        $data = json_decode(file_get_contents('php://input'), true);

        // Sanear y validar datos
        $id_paciente = sanear_entrada($data['id_paciente']);
        $nombres = sanear_entrada($data['nombres']);
        $apellidos = sanear_entrada($data['apellidos']);
        $fecha_nacimiento = sanear_entrada($data['fecha_nacimiento']);
        $id_documento_tipo = sanear_entrada($data['id_documento_tipo']);
        $numero_documento = sanear_entrada($data['numero_documento']);
        $direccion = sanear_entrada($data['direccion']);
        $telefono = sanear_entrada($data['telefono']);
        $correo_electronico = sanear_entrada($data['correo_electronico']);
        $id_sexo = sanear_entrada($data['id_sexo']);
        $id_departamento = sanear_entrada($data['id_departamento']);
        $id_provincia = sanear_entrada($data['id_provincia']);
        $id_distrito = sanear_entrada($data['id_distrito']);
        $observaciones_generales = sanear_entrada($data['observaciones_generales']);
        $contacto_emergencia_nombre = sanear_entrada($data['contacto_emergencia_nombre']);
        $contacto_emergencia_telefono = sanear_entrada($data['contacto_emergencia_telefono']);
        $id_estado = sanear_entrada($data['id_estado']);

        $conexion->begin_transaction();
        try {
            // Actualiza en la tabla de pacientes
            $stmt = $conexion->prepare("UPDATE tbl_pacientes SET nombres = ?, apellidos = ?, fecha_nacimiento = ?, id_documento_tipo = ?, numero_documento = ?, direccion = ?, telefono = ?, correo_electronico = ?, id_sexo = ?, id_departamento = ?, id_provincia = ?, id_distrito = ?, observaciones_generales = ?, id_estado = ? WHERE id_paciente = ?");
            $stmt->bind_param("sssissssiiiiisi", $nombres, $apellidos, $fecha_nacimiento, $id_documento_tipo, $numero_documento, $direccion, $telefono, $correo_electronico, $id_sexo, $id_departamento, $id_provincia, $id_distrito, $observaciones_generales, $id_estado, $id_paciente);
            $stmt->execute();
            $stmt->close();

            // Actualiza o inserta el contacto de emergencia
            $query_contacto = "SELECT id_contacto FROM tbl_contactos_emergencia WHERE id_paciente = ?";
            $stmt_check_contacto = $conexion->prepare($query_contacto);
            $stmt_check_contacto->bind_param("i", $id_paciente);
            $stmt_check_contacto->execute();
            $resultado_contacto = $stmt_check_contacto->get_result();

            if ($resultado_contacto->num_rows > 0) {
                // Si el contacto ya existe, actualízalo
                $stmt_update_contacto = $conexion->prepare("UPDATE tbl_contactos_emergencia SET nombre_contacto = ?, telefono_contacto = ? WHERE id_paciente = ?");
                $stmt_update_contacto->bind_param("ssi", $contacto_emergencia_nombre, $contacto_emergencia_telefono, $id_paciente);
                $stmt_update_contacto->execute();
                $stmt_update_contacto->close();
            } else {
                // Si no existe, insértalo
                $stmt_insert_contacto = $conexion->prepare("INSERT INTO tbl_contactos_emergencia (id_paciente, nombre_contacto, telefono_contacto) VALUES (?, ?, ?)");
                $stmt_insert_contacto->bind_param("iss", $id_paciente, $contacto_emergencia_nombre, $contacto_emergencia_telefono);
                $stmt_insert_contacto->execute();
                $stmt_insert_contacto->close();
            }

            $stmt_check_contacto->close();
            $conexion->commit();
            echo json_encode(['success' => true, 'message' => 'Paciente actualizado exitosamente.']);
        } catch (Exception $e) {
            $conexion->rollback();
            echo json_encode(['success' => false, 'message' => 'Error al actualizar paciente: ' . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        // Lógica para eliminar un paciente
        // Nota: Se recomienda el borrado lógico en aplicaciones reales
        if (isset($_GET['id'])) {
            $id_paciente = sanear_entrada($_GET['id']);
            $query = "DELETE FROM tbl_pacientes WHERE id_paciente = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_paciente);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Paciente eliminado correctamente.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al eliminar paciente: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Falta el ID del paciente.']);
        }
        break;

    default:
        // Método no permitido
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();
