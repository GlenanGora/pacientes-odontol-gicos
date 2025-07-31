<?php
/**
 * Archivo: listar.php
 * Ubicación: views/pacientes/
 * Descripción: Muestra la lista de pacientes registrados con paginación.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestión de Pacientes</h3>
    <a href="?page=pacientes/registrar" class="btn btn-primary">
        <span class="me-2">&#10133;</span> Nuevo Paciente
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        Filtros de Búsqueda
    </div>
    <div class="card-body">
        <form id="form-filtro" class="row g-3">
            <div class="col-md-3">
                <label for="filtro-nombre" class="form-label">Nombre o DNI</label>
                <input type="text" class="form-control" id="filtro-nombre">
            </div>
            <div class="col-md-3">
                <label for="filtro-estado" class="form-label">Estado</label>
                <select id="filtro-estado" class="form-select">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Buscar</button>
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">Limpiar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        Lista de Pacientes
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
                        <th>Nombres y Apellidos</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Última Atención</th>
                        <th>Último Tratamiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="pacientes-table-body">
                    <!-- Los datos de los pacientes se cargarán aquí con JavaScript -->
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

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar a este paciente? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-delete-paciente">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pacientesTableBody = document.getElementById('pacientes-table-body');
        const filtroForm = document.getElementById('form-filtro');
        const filtroNombre = document.getElementById('filtro-nombre');
        const filtroEstado = document.getElementById('filtro-estado');
        const perPageSelect = document.getElementById('per-page');
        const paginationInfoSpan = document.getElementById('pagination-info');
        const paginationControlsNav = document.getElementById('pagination-controls');
        let pacienteIdToDelete = null;
        let currentPage = 1;
        let perPage = 50;
        let totalRecords = 0;

        // Cargar datos para el combo de estados
        function cargarCombos() {
            fetch('api/pacientes.php?combo_data=true')
                .then(response => response.json())
                .then(data => {
                    if (data.estados_paciente) {
                        data.estados_paciente.forEach(estado => {
                            const option = document.createElement('option');
                            option.value = estado.id;
                            option.textContent = estado.nombre;
                            filtroEstado.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error al cargar datos para combos:', error));
        }

        // Cargar la lista de pacientes
        function cargarPacientes() {
            const filtros = {
                nombre: filtroNombre.value,
                estado: filtroEstado.value,
                page: currentPage,
                limit: perPage
            };
            
            let url = 'api/pacientes.php?' + new URLSearchParams(filtros).toString();

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Respuesta de red no fue ok');
                    }
                    return response.json();
                })
                .then(result => {
                    totalRecords = result.total;
                    const pacientes = result.data;
                    pacientesTableBody.innerHTML = '';
                    if (pacientes.length > 0) {
                        pacientes.forEach(paciente => {
                            const row = `
                                <tr>
                                    <td>${paciente.nombre_completo}</td>
                                    <td>${paciente.telefono || 'N/A'}</td>
                                    <td>${paciente.estado}</td>
                                    <td>${paciente.fecha_ultima_atencion || 'N/A'}</td>
                                    <td>${paciente.ultimo_tratamiento || 'N/A'}</td>
                                    <td>
                                        <a href="?page=pacientes/editar&id=${paciente.id_paciente}" class="btn btn-sm btn-info text-white me-2" title="Editar">&#9998;</a>
                                        <a href="?page=pacientes/historia_clinica&id=${paciente.id_paciente}" class="btn btn-sm btn-primary me-2" title="Historia Clínica">&#128220;</a>
                                        <button type="button" class="btn btn-sm btn-danger" title="Eliminar" onclick="showDeleteModal(${paciente.id_paciente})">&#128465;</button>
                                    </td>
                                </tr>
                            `;
                            pacientesTableBody.innerHTML += row;
                        });
                    } else {
                        pacientesTableBody.innerHTML = `<tr><td colspan="6" class="text-center">No se encontraron pacientes.</td></tr>`;
                    }
                    renderPaginationControls();
                })
                .catch(error => console.error('Error al cargar pacientes:', error));
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
                cargarPacientes();
            }
        };

        // Manejar el cambio de registros por página
        perPageSelect.addEventListener('change', function() {
            perPage = this.value === 'all' ? totalRecords : parseInt(this.value);
            currentPage = 1; // Volver a la primera página
            cargarPacientes();
        });

        // Función para mostrar el modal de eliminación
        window.showDeleteModal = function(id) {
            pacienteIdToDelete = id;
            const myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            myModal.show();
        };

        // Manejar la eliminación del paciente
        document.getElementById('btn-delete-paciente').addEventListener('click', function() {
            if (pacienteIdToDelete) {
                fetch(`api/pacientes.php?id=${pacienteIdToDelete}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cargarPacientes(); // Recargar la tabla
                        const myModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                        myModal.hide();
                    } else {
                        alert('Error al eliminar paciente.');
                    }
                })
                .catch(error => console.error('Error al eliminar:', error));
            }
        });

        // Manejar el envío del formulario de filtro
        filtroForm.addEventListener('submit', function(e) {
            e.preventDefault();
            currentPage = 1; // Resetear la página al buscar
            cargarPacientes();
        });

        // Limpiar filtros
        window.limpiarFiltros = function() {
            filtroForm.reset();
            currentPage = 1; // Resetear la página
            perPage = 50; // Valor por defecto
            perPageSelect.value = '50';
            cargarPacientes();
        };

        cargarCombos();
        cargarPacientes();
    });
</script>
