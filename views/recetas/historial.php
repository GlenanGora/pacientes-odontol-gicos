<?php
/**
 * Archivo: historial.php
 * Ubicación: views/recetas/
 * Descripción: Muestra el historial de recetas de todos los pacientes.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Historial de Recetas</h3>
</div>

<div class="card">
    <div class="card-header">
        Lista de Recetas
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Fecha de Emisión</th>
                        <th>Tratamiento</th>
                        <th>Diagnóstico</th>
                        <th>Nombre medicamento</th>
                        <th>Dosis</th>
                        <th>Frecuencia</th>
                        <th>Duración</th>
                    </tr>
                </thead>
                <tbody id="recetas-table-body">
                    <!-- Los datos de las recetas se cargarán aquí con JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recetasTableBody = document.getElementById('recetas-table-body');

        // Función para cargar el historial de recetas
        function cargarRecetas() {
            let url = 'api/recetas.php?action=listar';

            fetch(url)
                .then(response => response.json())
                .then(recetas => {
                    recetasTableBody.innerHTML = '';
                    if (recetas.length > 0) {
                        recetas.forEach((receta) => {
                            const row = `
                                <tr>
                                    <td>${receta.nombre_paciente}</td>
                                    <td>${receta.fecha_emision}</td>
                                    <td>${receta.nombre_tratamiento}</td>
                                    <td>${receta.nombre_diagnostico}</td>
                                    <td>${receta.nombre_medicamento}</td>
                                    <td>${receta.dosis}</td>
                                    <td>${receta.frecuencia || 'N/A'}</td>
                                    <td>${receta.duracion || 'N/A'}</td>
                                </tr>
                            `;
                            recetasTableBody.innerHTML += row;
                        });
                    } else {
                        recetasTableBody.innerHTML = `<tr><td colspan="8" class="text-center">No se encontraron recetas.</td></tr>`;
                    }
                })
                .catch(error => console.error('Error al cargar las recetas:', error));
        }

        cargarRecetas();
    });
</script>