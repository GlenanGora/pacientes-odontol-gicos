<?php
/**
 * Archivo: gestionar.php
 * Ubicación: views/diagnosticos/
 * Descripción: Interfaz para la gestión de diagnósticos y tratamientos predefinidos.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestión de Diagnósticos y Tratamientos</h3>
</div>

<div class="row">
    <!-- Panel para Diagnósticos -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                Catálogo de Diagnósticos
            </div>
            <div class="card-body">
                <form id="form-diagnostico" class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Nuevo diagnóstico" id="nombre_diagnostico" required>
                        <button class="btn btn-primary" type="submit">Agregar</button>
                    </div>
                </form>
                <ul class="list-group" id="lista-diagnosticos">
                    <!-- Los diagnósticos se cargarán aquí -->
                </ul>
            </div>
        </div>
    </div>

    <!-- Panel para Tratamientos -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                Catálogo de Tratamientos
            </div>
            <div class="card-body">
                <form id="form-tratamiento" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Nombre del tratamiento" id="nombre_tratamiento" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" placeholder="Costo base" id="costo_tratamiento" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Agregar</button>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control" placeholder="Descripción (opcional)" id="descripcion_tratamiento" rows="2"></textarea>
                        </div>
                    </div>
                </form>
                <ul class="list-group" id="lista-tratamientos">
                    <!-- Los tratamientos se cargarán aquí -->
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar diagnóstico -->
<div class="modal fade" id="editarDiagnosticoModal" tabindex="-1" aria-labelledby="editarDiagnosticoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarDiagnosticoModalLabel">Editar Diagnóstico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-diagnostico">
                    <input type="hidden" id="edit_id_diagnostico" name="id_diagnostico">
                    <div class="mb-3">
                        <label for="edit_nombre_diagnostico" class="form-label">Nombre del Diagnóstico</label>
                        <input type="text" class="form-control" id="edit_nombre_diagnostico" name="nombre_diagnostico" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar tratamiento -->
<div class="modal fade" id="editarTratamientoModal" tabindex="-1" aria-labelledby="editarTratamientoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarTratamientoModalLabel">Editar Tratamiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-tratamiento">
                    <input type="hidden" id="edit_id_tratamiento" name="id_tratamiento">
                    <div class="mb-3">
                        <label for="edit_nombre_tratamiento" class="form-label">Nombre del Tratamiento</label>
                        <input type="text" class="form-control" id="edit_nombre_tratamiento" name="nombre_tratamiento" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_costo_tratamiento" class="form-label">Costo Base</label>
                        <input type="number" class="form-control" id="edit_costo_tratamiento" name="costo_base" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_descripcion_tratamiento" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion_tratamiento" name="descripcion" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos del DOM
        const formDiagnostico = document.getElementById('form-diagnostico');
        const nombreDiagnosticoInput = document.getElementById('nombre_diagnostico');
        const listaDiagnosticos = document.getElementById('lista-diagnosticos');
        
        const formTratamiento = document.getElementById('form-tratamiento');
        const nombreTratamientoInput = document.getElementById('nombre_tratamiento');
        const costoTratamientoInput = document.getElementById('costo_tratamiento');
        const descripcionTratamientoInput = document.getElementById('descripcion_tratamiento');
        const listaTratamientos = document.getElementById('lista-tratamientos');

        const editarDiagnosticoModal = new bootstrap.Modal(document.getElementById('editarDiagnosticoModal'));
        const formEditarDiagnostico = document.getElementById('form-editar-diagnostico');
        const editIdDiagnosticoInput = document.getElementById('edit_id_diagnostico');
        const editNombreDiagnosticoInput = document.getElementById('edit_nombre_diagnostico');

        const editarTratamientoModal = new bootstrap.Modal(document.getElementById('editarTratamientoModal'));
        const formEditarTratamiento = document.getElementById('form-editar-tratamiento');
        const editIdTratamientoInput = document.getElementById('edit_id_tratamiento');
        const editNombreTratamientoInput = document.getElementById('edit_nombre_tratamiento');
        const editCostoTratamientoInput = document.getElementById('edit_costo_tratamiento');
        const editDescripcionTratamientoInput = document.getElementById('edit_descripcion_tratamiento');

        // Función para cargar los diagnósticos existentes
        function cargarDiagnosticos() {
            fetch('api/diagnosticos.php?action=listar')
                .then(response => response.json())
                .then(diagnosticos => {
                    listaDiagnosticos.innerHTML = '';
                    diagnosticos.forEach(d => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.innerHTML = `
                            ${d.nombre_diagnostico}
                            <div>
                                <button class="btn btn-sm btn-warning me-2" onclick="mostrarModalEditarDiagnostico(${d.id_diagnostico}, '${d.nombre_diagnostico}')">&#9998;</button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarDiagnostico(${d.id_diagnostico})">&#128465;</button>
                            </div>
                        `;
                        listaDiagnosticos.appendChild(li);
                    });
                })
                .catch(error => console.error('Error al cargar diagnósticos:', error));
        }

        // Función para cargar los tratamientos existentes
        function cargarTratamientos() {
            fetch('api/tratamientos.php?action=listar')
                .then(response => response.json())
                .then(tratamientos => {
                    listaTratamientos.innerHTML = '';
                    tratamientos.forEach(t => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.innerHTML = `
                            <div>
                                <strong>${t.nombre_tratamiento}</strong> - S/. ${t.costo_base}
                                <br><small>${t.descripcion || 'Sin descripción'}</small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-warning me-2" onclick="mostrarModalEditarTratamiento(${t.id_tratamiento}, '${t.nombre_tratamiento}', ${t.costo_base}, '${t.descripcion || ''}')">&#9998;</button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarTratamiento(${t.id_tratamiento})">&#128465;</button>
                            </div>
                        `;
                        listaTratamientos.appendChild(li);
                    });
                })
                .catch(error => console.error('Error al cargar tratamientos:', error));
        }

        // Manejar el formulario de agregar diagnóstico
        formDiagnostico.addEventListener('submit', function(e) {
            e.preventDefault();
            const nombre = nombreDiagnosticoInput.value;
            fetch('api/diagnosticos.php?action=agregar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre_diagnostico: nombre })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Diagnóstico agregado con éxito.');
                    nombreDiagnosticoInput.value = '';
                    cargarDiagnosticos();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al agregar diagnóstico:', error));
        });

        // Manejar el formulario de agregar tratamiento
        formTratamiento.addEventListener('submit', function(e) {
            e.preventDefault();
            const nombre = nombreTratamientoInput.value;
            const costo = costoTratamientoInput.value;
            const descripcion = descripcionTratamientoInput.value;
            fetch('api/tratamientos.php?action=agregar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre_tratamiento: nombre, costo_base: costo, descripcion: descripcion })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Tratamiento agregado con éxito.');
                    nombreTratamientoInput.value = '';
                    costoTratamientoInput.value = '';
                    descripcionTratamientoInput.value = '';
                    cargarTratamientos();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al agregar tratamiento:', error));
        });

        // Función global para mostrar el modal de edición de diagnóstico
        window.mostrarModalEditarDiagnostico = function(id, nombre) {
            editIdDiagnosticoInput.value = id;
            editNombreDiagnosticoInput.value = nombre;
            editarDiagnosticoModal.show();
        };

        // Manejar el envío del formulario de edición de diagnóstico
        formEditarDiagnostico.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = editIdDiagnosticoInput.value;
            const nombre = editNombreDiagnosticoInput.value;
            fetch(`api/diagnosticos.php?action=editar&id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre_diagnostico: nombre })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Diagnóstico actualizado con éxito.');
                    editarDiagnosticoModal.hide();
                    cargarDiagnosticos();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al editar diagnóstico:', error));
        });

        // Función global para mostrar el modal de edición de tratamiento
        window.mostrarModalEditarTratamiento = function(id, nombre, costo, descripcion) {
            editIdTratamientoInput.value = id;
            editNombreTratamientoInput.value = nombre;
            editCostoTratamientoInput.value = costo;
            editDescripcionTratamientoInput.value = descripcion;
            editarTratamientoModal.show();
        };

        // Manejar el envío del formulario de edición de tratamiento
        formEditarTratamiento.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = editIdTratamientoInput.value;
            const nombre = editNombreTratamientoInput.value;
            const costo = editCostoTratamientoInput.value;
            const descripcion = editDescripcionTratamientoInput.value;
            fetch(`api/tratamientos.php?action=editar&id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre_tratamiento: nombre, costo_base: costo, descripcion: descripcion })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Tratamiento actualizado con éxito.');
                    editarTratamientoModal.hide();
                    cargarTratamientos();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al editar tratamiento:', error));
        });

        // Función global para eliminar un diagnóstico
        window.eliminarDiagnostico = function(id) {
            if (confirm('¿Está seguro de que desea eliminar este diagnóstico?')) {
                fetch(`api/diagnosticos.php?action=eliminar&id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Diagnóstico eliminado.');
                        cargarDiagnosticos();
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => console.error('Error al eliminar diagnóstico:', error));
            }
        };

        // Función global para eliminar un tratamiento
        window.eliminarTratamiento = function(id) {
            if (confirm('¿Está seguro de que desea eliminar este tratamiento?')) {
                fetch(`api/tratamientos.php?action=eliminar&id=${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Tratamiento eliminado.');
                        cargarTratamientos();
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => console.error('Error al eliminar tratamiento:', error));
            }
        };

        // Cargar datos al iniciar
        cargarDiagnosticos();
        cargarTratamientos();
    });
</script>
