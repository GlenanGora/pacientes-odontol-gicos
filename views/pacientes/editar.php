<?php
/**
 * Archivo: editar.php
 * Ubicación: views/pacientes/
 * Descripción: Formulario para editar los datos de un paciente existente.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Editar Paciente</h3>
    <a href="?page=pacientes/listar" class="btn btn-secondary">
        <span class="me-2">&#8592;</span> Volver a la Lista
    </a>
</div>

<div class="card">
    <div class="card-header">
        Datos del Paciente
    </div>
    <div class="card-body">
        <form id="form-editar-paciente" class="row g-3">
            <input type="hidden" id="id_paciente" name="id_paciente">
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
                <button type="submit" class="btn btn-primary">Actualizar Paciente</button>
                <a href="?page=pacientes/listar" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-editar-paciente');
        const idPacienteInput = document.getElementById('id_paciente');
        const idDocumentoTipoSelect = document.getElementById('id_documento_tipo');
        const idSexoSelect = document.getElementById('id_sexo');
        const idEstadoSelect = document.getElementById('id_estado');
        const idDepartamentoSelect = document.getElementById('id_departamento');
        const idProvinciaSelect = document.getElementById('id_provincia');
        const idDistritoSelect = document.getElementById('id_distrito');

        // Función para obtener el ID del paciente de la URL
        function getPacienteIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id');
        }

        // Función para cargar los datos de los combos
        function cargarCombosIniciales() {
            return fetch('api/pacientes.php?combo_data=true')
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

        // Función para cargar los datos del paciente para edición
        function cargarDatosPaciente(id) {
            fetch(`api/pacientes.php?id=${id}`)
                .then(response => response.json())
                .then(paciente => {
                    if (paciente) {
                        // Rellenar los campos del formulario
                        document.getElementById('nombres').value = paciente.nombres;
                        document.getElementById('apellidos').value = paciente.apellidos;
                        document.getElementById('fecha_nacimiento').value = paciente.fecha_nacimiento;
                        document.getElementById('numero_documento').value = paciente.numero_documento;
                        document.getElementById('telefono').value = paciente.telefono;
                        document.getElementById('correo_electronico').value = paciente.correo_electronico;
                        document.getElementById('direccion').value = paciente.direccion;
                        document.getElementById('observaciones_generales').value = paciente.observaciones_generales;
                        document.getElementById('contacto_emergencia_nombre').value = paciente.contacto_emergencia_nombre;
                        document.getElementById('contacto_emergencia_telefono').value = paciente.contacto_emergencia_telefono;

                        // Seleccionar los valores correctos en los combos
                        idPacienteInput.value = paciente.id_paciente;
                        idDocumentoTipoSelect.value = paciente.id_documento_tipo;
                        idSexoSelect.value = paciente.id_sexo;
                        idEstadoSelect.value = paciente.id_estado;
                        
                        // Cargar y seleccionar la ubicación
                        idDepartamentoSelect.value = paciente.id_departamento;
                        
                        // Cargar provincias y seleccionar la correcta
                        fetch(`api/pacientes.php?departamento_id=${paciente.id_departamento}`)
                            .then(response => response.json())
                            .then(provincias => {
                                idProvinciaSelect.innerHTML = '<option value="">Seleccione...</option>';
                                provincias.forEach(item => {
                                    const selected = (item.id == paciente.id_provincia) ? 'selected' : '';
                                    idProvinciaSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.nombre}</option>`;
                                });
                                // Cargar distritos y seleccionar el correcto
                                fetch(`api/pacientes.php?provincia_id=${paciente.id_provincia}`)
                                    .then(response => response.json())
                                    .then(distritos => {
                                        idDistritoSelect.innerHTML = '<option value="">Seleccione...</option>';
                                        distritos.forEach(item => {
                                            const selected = (item.id == paciente.id_distrito) ? 'selected' : '';
                                            idDistritoSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.nombre}</option>`;
                                        });
                                    });
                            });

                    } else {
                        alert('Paciente no encontrado.');
                        window.location.href = '?page=pacientes/listar';
                    }
                })
                .catch(error => console.error('Error al cargar los datos del paciente:', error));
        }

        // Manejar el envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            fetch('api/pacientes.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Paciente actualizado con éxito.');
                    window.location.href = '?page=pacientes/listar'; // Redireccionar a la lista
                } else {
                    alert('Error al actualizar el paciente: ' + result.message);
                }
            })
            .catch(error => console.error('Error al enviar el formulario:', error));
        });

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

        // Cargar los combos y luego los datos del paciente
        const pacienteId = getPacienteIdFromUrl();
        if (pacienteId) {
            cargarCombosIniciales().then(() => {
                cargarDatosPaciente(pacienteId);
            });
        } else {
            alert('ID de paciente no especificado.');
            window.location.href = '?page=pacientes/listar';
        }
    });
</script>
