<?php
/**
 * Archivo: recetas.php
 * Descripción: API REST para la gestión del historial de recetas.
 * Autor: Gemini
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../core/functions.php';
require_once '../core/db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Lógica para listar el historial de recetas o los detalles de una receta
        if (isset($_GET['id_receta'])) {
            // Obtener los detalles de los medicamentos de una receta específica
            $id_receta = sanear_entrada($_GET['id_receta']);
            $query = "SELECT nombre_medicamento, dosis, frecuencia, duracion FROM tbl_recetas_detalle WHERE id_receta = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_receta);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $medicamentos = [];
            while ($fila = $resultado->fetch_assoc()) {
                $medicamentos[] = $fila;
            }
            $stmt->close();
            echo json_encode($medicamentos);
        } else {
            // Obtener el historial de recetas de todos los pacientes, con filtro opcional por paciente
            $filtros_sql = "";
            $params = [];
            $types = "";

            if (isset($_GET['id_paciente']) && !empty($_GET['id_paciente'])) {
                $id_paciente = sanear_entrada($_GET['id_paciente']);
                $filtros_sql = "WHERE r.id_paciente = ?";
                $params[] = $id_paciente;
                $types .= "i";
            }

            $query = "SELECT r.id_receta, r.fecha_emision, r.indicaciones_generales,
                             CONCAT(p.nombres, ' ', p.apellidos) AS nombre_paciente
                      FROM tbl_recetas r
                      JOIN tbl_pacientes p ON r.id_paciente = p.id_paciente
                      $filtros_sql
                      ORDER BY r.fecha_emision DESC";
            
            if (!empty($params)) {
                $stmt = $conexion->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $recetas = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $recetas[] = $fila;
                }
                $stmt->close();
            } else {
                $recetas = cargar_combo($query);
            }
            
            echo json_encode($recetas);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

$conexion->close();
