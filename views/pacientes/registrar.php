<?php
/**
 * Archivo: registrar.php
 * Ubicación: views/pacientes/
 * Descripción: Formulario para registrar un nuevo paciente.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Registrar Nuevo Paciente</h3>
    <a href="?page=pacientes/listar" class="btn btn-secondary">
        <span class="me-2">&#8592;</span> Volver a la Lista
    </a>
</div>

<div class="card">
    <div class="card-header">
        Datos del Paciente
    </div>
    <div class="card-body">
        <form id="form-registro-paciente" class="row g-3">
            <div class="col-md-6">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" class="form-control" id="nombres" name="nombres" required>
            </div>
            <div class="col-md-6">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
            </div>
            <div class="col-md-4">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <div class="col-md-4">
                <label for="id_documento_tipo" class="form-label">Tipo de Documento</label>
                <select id="id_documento_tipo" name="id_documento_tipo" class="form-select" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="numero_documento" class="form-label">Número de Documento</label>
                <input type="text" class="form-control" id="numero_documento" name="numero_documento" required>
            </div>
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" pattern="[0-9]{9,10}" title="Debe ser un número de 9 o 10 dígitos">
            </div>
            <div class="col-md-6">
                <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo_electronico" name="correo_electronico">
            </div>
            <div class="col-md-6">
                <label for="id_sexo" class="form-label">Sexo</label>
                <select id="id_sexo" name="id_sexo" class="form-select" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="id_estado" class="form-label">Estado</label>
                <select id="id_estado" name="id_estado" class="form-select" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="id_departamento" class="form-label">Departamento</label>
                <select id="id_departamento" name="id_departamento" class="form-select" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="id_provincia" class="form-label">Provincia</label>
                <select id="id_provincia" name="id_provincia" class="form-select" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="id_distrito" class="form-label">Distrito</label>
                <select id="id_distrito" name="id_distrito" class="form-select" required>
                    <option value="">Seleccione...</option>
                </select>
            </div>
            <div class="col-12">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion">
            </div>

            <hr class="mt-4">
            <h5>Contacto de Emergencia</h5>
            <div class="col-md-6">
                <label for="contacto_emergencia_nombre" class="form-label">Nombre del Contacto</label>
                <input type="text" class="form-control" id="contacto_emergencia_nombre" name="contacto_emergencia_nombre">
            </div>
            <div class="col-md-6">
                <label for="contacto_emergencia_telefono" class="form-label">Teléfono del Contacto</label>
                <input type="text" class="form-control" id="contacto_emergencia_telefono" name="contacto_emergencia_telefono" pattern="[0-9]{9,10}" title="Debe ser un número de 9 o 10 dígitos">
            </div>

            <hr class="mt-4">
            <div class="col-12">
                <label for="observaciones_generales" class="form-label">Observaciones Generales</label>
                <textarea class="form-control" id="observaciones_generales" name="observaciones_generales" rows="3"></textarea>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary">Registrar Paciente</button>
                <a href="?page=pacientes/listar" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-registro-paciente');
        const idDocumentoTipoSelect = document.getElementById('id_documento_tipo');
        const idSexoSelect = document.getElementById('id_sexo');
        const idEstadoSelect = document.getElementById('id_estado');
        const idDepartamentoSelect = document.getElementById('id_departamento');
        const idProvinciaSelect = document.getElementById('id_provincia');
        const idDistritoSelect = document.getElementById('id_distrito');

        // Función para cargar los datos de los combos
        function cargarCombosIniciales() {
            fetch('api/pacientes.php?combo_data=true')
                .then(response => response.json())
                .then(data => {
                    if (data.documento_tipos) {
                        data.documento_tipos.forEach(item => {
                            idDocumentoTipoSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                        });
                    }
                    if (data.sexos) {
                        data.sexos.forEach(item => {
                            idSexoSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                        });
                    }
                    if (data.estados_paciente) {
                        data.estados_paciente.forEach(item => {
                            idEstadoSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                        });
                    }
                    if (data.departamentos) {
                        data.departamentos.forEach(item => {
                            idDepartamentoSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                        });
                    }
                })
                .catch(error => console.error('Error al cargar datos para combos:', error));
        }

        // Lógica para cargar provincias por departamento
        idDepartamentoSelect.addEventListener('change', function() {
            const departamentoId = this.value;
            idProvinciaSelect.innerHTML = '<option value="">Seleccione...</option>';
            idDistritoSelect.innerHTML = '<option value="">Seleccione...</option>';
            if (departamentoId) {
                fetch(`api/pacientes.php?departamento_id=${departamentoId}`)
                    .then(response => response.json())
                    .then(provincias => {
                        provincias.forEach(item => {
                            idProvinciaSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                        });
                    })
                    .catch(error => console.error('Error al cargar provincias:', error));
            }
        });

        // Lógica para cargar distritos por provincia
        idProvinciaSelect.addEventListener('change', function() {
            const provinciaId = this.value;
            idDistritoSelect.innerHTML = '<option value="">Seleccione...</option>';
            if (provinciaId) {
                fetch(`api/pacientes.php?provincia_id=${provinciaId}`)
                    .then(response => response.json())
                    .then(distritos => {
                        distritos.forEach(item => {
                            idDistritoSelect.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                        });
                    })
                    .catch(error => console.error('Error al cargar distritos:', error));
            }
        });

        // Manejar el envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            fetch('api/pacientes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Paciente registrado con éxito.');
                    window.location.href = '?page=pacientes/listar'; // Redireccionar a la lista
                } else {
                    alert('Error al registrar el paciente: ' + result.message);
                }
            })
            .catch(error => console.error('Error al enviar el formulario:', error));
        });

        // Cargar los combos al cargar la página
        cargarCombosIniciales();
    });
</script>