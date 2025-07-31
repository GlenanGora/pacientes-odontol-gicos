<?php
/**
 * Archivo: historial.php
 * Ubicación: views/recetas/
 * Descripción: Muestra el historial de recetas de todos los pacientes y permite visualizar los detalles.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Historial de Recetas</h3>
    <!-- Aquí se podría agregar un botón para generar una nueva receta, si se implementara esa funcionalidad -->
</div>

<div class="card mb-4">
    <div class="card-header">
        Filtros de Búsqueda
    </div>
    <div class="card-body">
        <form id="form-filtro-recetas" class="row g-3">
            <div class="col-md-6">
                <label for="filtro-paciente" class="form-label">Paciente</label>
                <select id="filtro-paciente" class="form-select">
                    <option value="">Todos los pacientes</option>
                    <!-- Los pacientes se cargarán aquí con JavaScript -->
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Buscar</button>
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltrosRecetas()">Limpiar</button>
            </div>
        </form>
    </div>
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
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Fecha de Emisión</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="recetas-table-body">
                    <!-- Los datos de las recetas se cargarán aquí con JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para ver los detalles de la receta -->
<div class="modal fade" id="recetaModal" tabindex="-1" aria-labelledby="recetaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recetaModalLabel">Detalles de la Receta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Paciente:</strong> <span id="modal-paciente-receta"></span></p>
                <p><strong>Fecha de Emisión:</strong> <span id="modal-fecha-emision"></span></p>
                <p><strong>Indicaciones Generales:</strong> <span id="modal-indicaciones"></span></p>
                <h6 class="mt-4">Medicamentos:</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Dosis</th>
                            <th>Frecuencia</th>
                            <th>Duración</th>
                        </tr>
                    </thead>
                    <tbody id="modal-medicamentos-body">
                        <!-- Los medicamentos se cargarán aquí -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recetasTableBody = document.getElementById('recetas-table-body');
        const filtroPacienteSelect = document.getElementById('filtro-paciente');
        const formFiltroRecetas = document.getElementById('form-filtro-recetas');

        // Función para cargar los pacientes en el combo
        function cargarPacientesCombo() {
            fetch('api/citas.php?pacientes=true')
                .then(response => response.json())
                .then(pacientes => {
                    pacientes.forEach(paciente => {
                        const option = document.createElement('option');
                        option.value = paciente.id;
                        option.textContent = paciente.nombre;
                        filtroPacienteSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar pacientes para el combo:', error));
        }

        // Función para cargar el historial de recetas
        function cargarRecetas(filtros = {}) {
            let url = 'api/recetas.php';
            const params = new URLSearchParams(filtros);
            if (Object.keys(filtros).length > 0) {
                url += '?' + params.toString();
            }

            fetch(url)
                .then(response => response.json())
                .then(recetas => {
                    recetasTableBody.innerHTML = '';
                    if (recetas.length > 0) {
                        recetas.forEach((receta, index) => {
                            const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${receta.nombre_paciente}</td>
                                    <td>${receta.fecha_emision}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info text-white me-2" onclick='mostrarDetallesReceta(${JSON.stringify(receta)})'>
                                            &#128269; Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                            `;
                            recetasTableBody.innerHTML += row;
                        });
                    } else {
                        recetasTableBody.innerHTML = `<tr><td colspan="4" class="text-center">No se encontraron recetas.</td></tr>`;
                    }
                })
                .catch(error => console.error('Error al cargar las recetas:', error));
        }

        // Función global para mostrar los detalles de la receta en un modal
        window.mostrarDetallesReceta = function(receta) {
            const modalPaciente = document.getElementById('modal-paciente-receta');
            const modalFecha = document.getElementById('modal-fecha-emision');
            const modalIndicaciones = document.getElementById('modal-indicaciones');
            const modalMedicamentosBody = document.getElementById('modal-medicamentos-body');
            
            modalPaciente.textContent = receta.nombre_paciente;
            modalFecha.textContent = receta.fecha_emision;
            modalIndicaciones.textContent = receta.indicaciones_generales || 'N/A';
            
            // Cargar los medicamentos
            modalMedicamentosBody.innerHTML = '';
            fetch(`api/recetas.php?id_receta=${receta.id_receta}`)
                .then(response => response.json())
                .then(medicamentos => {
                    if (medicamentos.length > 0) {
                        medicamentos.forEach(med => {
                            const row = `
                                <tr>
                                    <td>${med.nombre_medicamento}</td>
                                    <td>${med.dosis}</td>
                                    <td>${med.frecuencia || 'N/A'}</td>
                                    <td>${med.duracion || 'N/A'}</td>
                                </tr>
                            `;
                            modalMedicamentosBody.innerHTML += row;
                        });
                    } else {
                        modalMedicamentosBody.innerHTML = `<tr><td colspan="4" class="text-center">No hay medicamentos registrados para esta receta.</td></tr>`;
                    }
                })
                .catch(error => console.error('Error al cargar medicamentos:', error));

            const recetaModal = new bootstrap.Modal(document.getElementById('recetaModal'));
            recetaModal.show();
        };

        // Manejar el envío del formulario de filtro
        formFiltroRecetas.addEventListener('submit', function(e) {
            e.preventDefault();
            const filtros = {
                id_paciente: filtroPacienteSelect.value
            };
            cargarRecetas(filtros);
        });

        // Limpiar filtros
        window.limpiarFiltrosRecetas = function() {
            formFiltroRecetas.reset();
            cargarRecetas();
        };

        cargarPacientesCombo();
        cargarRecetas();
    });
</script>
