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
                    </div>
                </form>
                <ul class="list-group" id="lista-tratamientos">
                    <!-- Los tratamientos se cargarán aquí -->
                </ul>
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
        const listaTratamientos = document.getElementById('lista-tratamientos');

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
                            <button class="btn btn-sm btn-danger" onclick="eliminarDiagnostico(${d.id_diagnostico})">&#128465;</button>
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
                            ${t.nombre_tratamiento} - S/. ${t.costo_base}
                            <button class="btn btn-sm btn-danger" onclick="eliminarTratamiento(${t.id_tratamiento})">&#128465;</button>
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
            fetch('api/tratamientos.php?action=agregar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre_tratamiento: nombre, costo_base: costo })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Tratamiento agregado con éxito.');
                    nombreTratamientoInput.value = '';
                    costoTratamientoInput.value = '';
                    cargarTratamientos();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al agregar tratamiento:', error));
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
