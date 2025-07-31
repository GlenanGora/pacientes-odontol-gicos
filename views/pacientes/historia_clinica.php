<?php
/**
 * Archivo: historia_clinica.php
 * Ubicación: views/pacientes/
 * Descripción: Muestra el historial clínico completo de un paciente, incluyendo planes de tratamiento, procedimientos y pagos.
 * Autor: Gemini
 */

// Se obtiene el ID del paciente de la URL
$id_paciente = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

if (!$id_paciente) {
    echo '<div class="alert alert-danger">Error: ID de paciente no especificado.</div>';
    exit();
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Historia Clínica del Paciente: <span id="paciente-nombre-completo"></span></h3>
    <a href="?page=pacientes/listar" class="btn btn-secondary">
        <span class="me-2">&#8592;</span> Volver a la Lista
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                Datos del Paciente
            </div>
            <div class="card-body" id="datos-paciente">
                <!-- Los datos del paciente se cargarán aquí con JavaScript -->
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                Historial Médico
                <button class="btn btn-warning btn-sm text-white" id="btn-editar-historial">
                    &#9998; Editar
                </button>
            </div>
            <div class="card-body" id="historial-medico">
                <!-- El historial médico se cargará aquí con JavaScript -->
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        Planes de Tratamiento y Procedimientos
        <button class="btn btn-primary btn-sm" id="btn-nuevo-tratamiento">
            &#10133; Nuevo Plan
        </button>
    </div>
    <div class="card-body">
        <div class="accordion" id="planes-tratamiento-accordion">
            <!-- Los planes de tratamiento se cargarán aquí con JavaScript -->
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Historial de Pagos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha de Pago</th>
                        <th>Plan de Tratamiento</th>
                        <th>Monto</th>
                        <th>Método de Pago</th>
                        <th>Tipo de Pago</th>
                    </tr>
                </thead>
                <tbody id="historial-pagos-body">
                    <!-- Los pagos se cargarán aquí con JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para editar el historial médico -->
<div class="modal fade" id="editarHistorialModal" tabindex="-1" aria-labelledby="editarHistorialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarHistorialModalLabel">Editar Historial Médico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-historial">
                    <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                    <div class="mb-3">
                        <label for="enfermedades" class="form-label">Enfermedades Preexistentes</label>
                        <textarea class="form-control" id="enfermedades" name="enfermedades_preexistentes" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="alergias" class="form-label">Alergias</label>
                        <textarea class="form-control" id="alergias" name="alergias" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="medicacion" class="form-label">Medicación Actual</label>
                        <textarea class="form-control" id="medicacion" name="medicacion_actual" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="habitos" class="form-label">Hábitos</label>
                        <textarea class="form-control" id="habitos" name="habitos" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar un nuevo plan de tratamiento (RF3.1, RF3.2) -->
<div class="modal fade" id="nuevoTratamientoModal" tabindex="-1" aria-labelledby="nuevoTratamientoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevoTratamientoModalLabel">Registrar Nuevo Plan de Tratamiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-nuevo-tratamiento">
                    <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                    <div class="mb-3">
                        <label for="diagnostico" class="form-label">Diagnóstico</label>
                        <select class="form-select" id="diagnostico" name="id_diagnostico" required>
                            <option value="">Seleccione un diagnóstico</option>
                            <!-- Diagnósticos se cargarán aquí -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Crear Plan de Tratamiento</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar un nuevo procedimiento (RF3.3) -->
<div class="modal fade" id="nuevoProcedimientoModal" tabindex="-1" aria-labelledby="nuevoProcedimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevoProcedimientoModalLabel">Registrar Nuevo Procedimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-nuevo-procedimiento">
                    <input type="hidden" name="id_plan_tratamiento" id="id_plan_tratamiento">
                    <div class="mb-3">
                        <label for="tratamiento" class="form-label">Tratamiento</label>
                        <select class="form-select" id="tratamiento" name="id_tratamiento" required>
                            <option value="">Seleccione un tratamiento</option>
                            <!-- Tratamientos se cargarán aquí -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notas_evolucion" class="form-label">Notas de Evolución</label>
                        <textarea class="form-control" id="notas_evolucion" name="notas_evolucion" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar Procedimiento</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const idPaciente = '<?php echo $id_paciente; ?>';
        const pacienteNombreCompletoSpan = document.getElementById('paciente-nombre-completo');
        const datosPacienteDiv = document.getElementById('datos-paciente');
        const historialMedicoDiv = document.getElementById('historial-medico');
        const planesTratamientoAccordion = document.getElementById('planes-tratamiento-accordion');
        const historialPagosBody = document.getElementById('historial-pagos-body');
        const btnNuevoTratamiento = document.getElementById('btn-nuevo-tratamiento');
        const nuevoTratamientoModal = new bootstrap.Modal(document.getElementById('nuevoTratamientoModal'));
        const formNuevoTratamiento = document.getElementById('form-nuevo-tratamiento');
        const diagnosticoSelect = document.getElementById('diagnostico');

        const nuevoProcedimientoModal = new bootstrap.Modal(document.getElementById('nuevoProcedimientoModal'));
        const formNuevoProcedimiento = document.getElementById('form-nuevo-procedimiento');
        const idPlanTratamientoInput = document.getElementById('id_plan_tratamiento');
        const tratamientoSelect = document.getElementById('tratamiento');
        
        // Elementos para el modal de edición de historial médico
        const btnEditarHistorial = document.getElementById('btn-editar-historial');
        const editarHistorialModal = new bootstrap.Modal(document.getElementById('editarHistorialModal'));
        const formEditarHistorial = document.getElementById('form-editar-historial');
        const enfermedadesTextarea = document.getElementById('enfermedades');
        const alergiasTextarea = document.getElementById('alergias');
        const medicacionTextarea = document.getElementById('medicacion');
        const habitosTextarea = document.getElementById('habitos');


        // Función para cargar todos los datos de un paciente
        function cargarHistorialPaciente() {
            fetch(`api/historia_clinica.php?id_paciente=${idPaciente}`)
                .then(response => response.json())
                .then(data => {
                    const paciente = data.paciente;
                    const historialMedico = data.historial_medico;
                    const planesTratamiento = data.planes_tratamiento;
                    const pagos = data.pagos;

                    // Cargar datos del paciente
                    if (paciente) {
                        pacienteNombreCompletoSpan.textContent = `${paciente.nombres} ${paciente.apellidos}`;
                        datosPacienteDiv.innerHTML = `
                            <p><strong>DNI:</strong> ${paciente.numero_documento}</p>
                            <p><strong>Fecha de Nacimiento:</strong> ${paciente.fecha_nacimiento}</p>
                            <p><strong>Teléfono:</strong> ${paciente.telefono || 'N/A'}</p>
                            <p><strong>Correo:</strong> ${paciente.correo_electronico || 'N/A'}</p>
                            <p><strong>Estado:</strong> ${paciente.nombre_estado || 'N/A'}</p>
                        `;
                    }

                    // Cargar historial médico (RF1.5)
                    if (historialMedico) {
                        historialMedicoDiv.innerHTML = `
                            <p><strong>Enfermedades Preexistentes:</strong> ${historialMedico.enfermedades_preexistentes || 'N/A'}</p>
                            <p><strong>Alergias:</strong> ${historialMedico.alergias || 'N/A'}</p>
                            <p><strong>Medicación Actual:</strong> ${historialMedico.medicacion_actual || 'N/A'}</p>
                            <p><strong>Hábitos:</strong> ${historialMedico.habitos || 'N/A'}</p>
                        `;
                        // Rellenar el formulario del modal de edición
                        enfermedadesTextarea.value = historialMedico.enfermedades_preexistentes || '';
                        alergiasTextarea.value = historialMedico.alergias || '';
                        medicacionTextarea.value = historialMedico.medicacion_actual || '';
                        habitosTextarea.value = historialMedico.habitos || '';

                    } else {
                        historialMedicoDiv.innerHTML = '<p>No se ha registrado historial médico.</p>';
                    }

                    // Cargar planes de tratamiento (RF3)
                    planesTratamientoAccordion.innerHTML = '';
                    if (planesTratamiento && planesTratamiento.length > 0) {
                        planesTratamiento.forEach((plan, index) => {
                            const collapseId = `collapsePlan${index}`;
                            const headingId = `headingPlan${index}`;
                            const procedimientosHtml = plan.procedimientos.map(proc => `
                                <li><strong>${proc.nombre_tratamiento}</strong> - ${proc.fecha_realizacion} - Notas: ${proc.notas_evolucion || 'N/A'}</li>
                            `).join('');
                            const planHtml = `
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="${headingId}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                                            Plan de Tratamiento #${plan.id_plan_tratamiento} (${plan.estado_plan}) - Creado el ${plan.fecha_creacion}
                                        </button>
                                    </h2>
                                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}" data-bs-parent="#planes-tratamiento-accordion">
                                        <div class="accordion-body">
                                            <h5>Procedimientos Realizados:</h5>
                                            <ul>${procedimientosHtml}</ul>
                                            <button class="btn btn-sm btn-success mt-2" onclick="mostrarModalProcedimiento(${plan.id_plan_tratamiento})">Registrar Procedimiento</button>
                                            <button class="btn btn-sm btn-info text-white mt-2" onclick="generarPresupuesto(${plan.id_plan_tratamiento})">Generar Presupuesto</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            planesTratamientoAccordion.innerHTML += planHtml;
                        });
                    } else {
                        planesTratamientoAccordion.innerHTML = '<p>No hay planes de tratamiento para este paciente.</p>';
                    }

                    // Cargar historial de pagos (RF6.1)
                    historialPagosBody.innerHTML = '';
                    if (pagos && pagos.length > 0) {
                        pagos.forEach(pago => {
                            const row = `
                                <tr>
                                    <td>${pago.fecha_pago}</td>
                                    <td>${pago.id_plan_tratamiento || 'Pago puntual'}</td>
                                    <td>S/. ${pago.monto}</td>
                                    <td>${pago.metodo_pago}</td>
                                    <td>${pago.tipo_pago}</td>
                                </tr>
                            `;
                            historialPagosBody.innerHTML += row;
                        });
                    } else {
                        historialPagosBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay pagos registrados.</td></tr>';
                    }
                })
                .catch(error => console.error('Error al cargar historial del paciente:', error));
        }

        // Cargar diagnósticos y tratamientos para los modales
        function cargarCombosModales() {
            fetch('api/diagnosticos.php?action=listar')
                .then(response => response.json())
                .then(diagnosticos => {
                    diagnosticoSelect.innerHTML = `<option value="">Seleccione un diagnóstico</option>`;
                    diagnosticos.forEach(d => {
                        diagnosticoSelect.innerHTML += `<option value="${d.id_diagnostico}">${d.nombre_diagnostico}</option>`;
                    });
                })
                .catch(error => console.error('Error al cargar diagnósticos:', error));
            
            fetch('api/tratamientos.php?action=listar')
                .then(response => response.json())
                .then(tratamientos => {
                    tratamientoSelect.innerHTML = `<option value="">Seleccione un tratamiento</option>`;
                    tratamientos.forEach(t => {
                        tratamientoSelect.innerHTML += `<option value="${t.id_tratamiento}">${t.nombre_tratamiento} (S/. ${t.costo_base})</option>`;
                    });
                })
                .catch(error => console.error('Error al cargar tratamientos:', error));
        }

        // Evento para mostrar el modal de edición de historial médico
        btnEditarHistorial.addEventListener('click', function() {
            editarHistorialModal.show();
        });

        // Manejar el envío del formulario de edición de historial
        formEditarHistorial.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formEditarHistorial);
            const data = Object.fromEntries(formData.entries());

            fetch('api/historia_clinica.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Historial médico actualizado con éxito.');
                    editarHistorialModal.hide();
                    cargarHistorialPaciente(); // Recargar el historial
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al actualizar historial médico:', error));
        });

        // Evento para mostrar el modal de nuevo tratamiento
        btnNuevoTratamiento.addEventListener('click', function() {
            nuevoTratamientoModal.show();
        });
        
        // Manejar el envío del formulario de nuevo tratamiento
        formNuevoTratamiento.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formNuevoTratamiento);
            const data = Object.fromEntries(formData.entries());
            
            fetch('api/atenciones.php?action=crear_plan_tratamiento', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Plan de tratamiento creado con éxito.');
                    nuevoTratamientoModal.hide();
                    formNuevoTratamiento.reset();
                    cargarHistorialPaciente(); // Recargar el historial
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al crear plan de tratamiento:', error));
        });

        // Función global para mostrar el modal de nuevo procedimiento
        window.mostrarModalProcedimiento = function(idPlan) {
            idPlanTratamientoInput.value = idPlan;
            nuevoProcedimientoModal.show();
        };

        // Manejar el envío del formulario de nuevo procedimiento
        formNuevoProcedimiento.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formNuevoProcedimiento);
            const data = Object.fromEntries(formData.entries());

            fetch('api/atenciones.php?action=registrar_procedimiento', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Procedimiento registrado con éxito.');
                    nuevoProcedimientoModal.hide();
                    formNuevoProcedimiento.reset();
                    cargarHistorialPaciente(); // Recargar el historial
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al registrar procedimiento:', error));
        });

        // Función global para generar presupuesto
        window.generarPresupuesto = function(idPlan) {
            // Lógica para generar el presupuesto
            alert(`Funcionalidad para generar presupuesto para el Plan #${idPlan} no implementada.`);
        };


        cargarHistorialPaciente();
        cargarCombosModales();
    });
</script>
