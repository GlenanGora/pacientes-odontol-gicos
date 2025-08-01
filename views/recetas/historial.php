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

<div class="card mb-4">
    <div class="card-header">
        Filtros de Búsqueda
    </div>
    <div class="card-body">
        <form id="form-filtro-recetas" class="row g-3">
            <div class="col-md-4">
                <label for="filtro-paciente" class="form-label">Nombre del Paciente</label>
                <input type="text" class="form-control" id="filtro-paciente">
            </div>
            <div class="col-md-4">
                <label for="filtro-fecha" class="form-label">Fecha de Emisión</label>
                <input type="date" class="form-control" id="filtro-fecha">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Buscar</button>
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltrosRecetas()">Limpiar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Lista de Recetas
        <div class="d-flex align-items-center">
            <label for="per-page" class="form-label me-2 mb-0">Registros por página:</label>
            <select id="per-page" class="form-select w-auto">
                <option value="50" selected>50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="all">Todos</option>
            </select>
        </div>
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
        
        <!-- Controles de Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <span id="pagination-info"></span>
            <nav>
                <ul class="pagination mb-0" id="pagination-controls">
                    <!-- Los controles de paginación se generarán aquí -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recetasTableBody = document.getElementById('recetas-table-body');
        const formFiltroRecetas = document.getElementById('form-filtro-recetas');
        const filtroPacienteInput = document.getElementById('filtro-paciente');
        const filtroFechaInput = document.getElementById('filtro-fecha');
        const perPageSelect = document.getElementById('per-page');
        const paginationInfoSpan = document.getElementById('pagination-info');
        const paginationControlsNav = document.getElementById('pagination-controls');
        let currentPage = 1;
        let perPage = 50;
        let totalRecords = 0;

        // Función para cargar el historial de recetas
        function cargarRecetas() {
            const filtros = {
                paciente: filtroPacienteInput.value,
                fecha: filtroFechaInput.value,
                page: currentPage,
                limit: perPage
            };
            
            let url = 'api/recetas.php?action=listar&' + new URLSearchParams(filtros).toString();

            fetch(url)
                .then(response => response.json())
                .then(result => {
                    totalRecords = result.total;
                    const recetas = result.data;
                    recetasTableBody.innerHTML = '';
                    if (recetas.length > 0) {
                        recetas.forEach((receta) => {
                            const row = `
                                <tr>
                                    <td>${receta.nombre_paciente}</td>
                                    <td>${receta.fecha_emision}</td>
                                    <td>${receta.nombre_tratamiento || 'N/A'}</td>
                                    <td>${receta.nombre_diagnostico || 'N/A'}</td>
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
                    renderPaginationControls();
                })
                .catch(error => console.error('Error al cargar las recetas:', error));
        }
        
        // Renderizar los controles de paginación
        function renderPaginationControls() {
            paginationControlsNav.innerHTML = '';
            const totalPages = Math.ceil(totalRecords / perPage);
            const startRecord = (currentPage - 1) * perPage + 1;
            const endRecord = Math.min(currentPage * perPage, totalRecords);

            paginationInfoSpan.textContent = `Mostrando ${startRecord} a ${endRecord} de ${totalRecords} registros`;

            if (totalPages > 1) {
                // Botón "Anterior"
                paginationControlsNav.innerHTML += `
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">&#9664;</a>
                    </li>
                `;
                // Botones de número de página
                for (let i = 1; i <= totalPages; i++) {
                    paginationControlsNav.innerHTML += `
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                        </li>
                    `;
                }
                // Botón "Siguiente"
                paginationControlsNav.innerHTML += `
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">&#9654;</a>
                    </li>
                `;
            }
        }

        // Función para cambiar de página
        window.changePage = function(page) {
            if (page >= 1 && page <= Math.ceil(totalRecords / perPage)) {
                currentPage = page;
                cargarRecetas();
            }
        };

        // Manejar el cambio de registros por página
        perPageSelect.addEventListener('change', function() {
            perPage = this.value === 'all' ? totalRecords : parseInt(this.value);
            currentPage = 1; // Volver a la primera página
            cargarRecetas();
        });

        // Manejar el envío del formulario de filtro
        formFiltroRecetas.addEventListener('submit', function(e) {
            e.preventDefault();
            currentPage = 1; // Resetear la página al buscar
            cargarRecetas();
        });

        // Limpiar filtros
        window.limpiarFiltrosRecetas = function() {
            formFiltroRecetas.reset();
            currentPage = 1; // Resetear la página
            perPage = 50; // Valor por defecto
            perPageSelect.value = '50';
            cargarRecetas();
        };

        cargarRecetas();
    });
</script>