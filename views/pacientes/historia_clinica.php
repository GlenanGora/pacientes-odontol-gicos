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
                        <label for="costo-procedimiento" class="form-label">Costo del Procedimiento</label>
                        <input type="number" class="form-control" id="costo-procedimiento" name="costo_personalizado" min="0" step="0.01" required>
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

<!-- Modal para editar procedimiento -->
<div class="modal fade" id="editarProcedimientoModal" tabindex="-1" aria-labelledby="editarProcedimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarProcedimientoModalLabel">Editar Procedimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-procedimiento">
                    <input type="hidden" name="id_procedimiento_realizado" id="edit_id_procedimiento_realizado">
                    <div class="mb-3">
                        <label for="edit_costo_procedimiento" class="form-label">Costo del Procedimiento</label>
                        <input type="number" class="form-control" id="edit_costo_procedimiento" name="costo_personalizado" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notas_evolucion" class="form-label">Notas de Evolución</label>
                        <textarea class="form-control" id="edit_notas_evolucion" name="notas_evolucion" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para generar presupuesto -->
<div class="modal fade" id="presupuestoModal" tabindex="-1" aria-labelledby="presupuestoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="presupuestoModalLabel">Presupuesto del Plan de Tratamiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="presupuesto-detalle">
                    <!-- El presupuesto se cargará aquí con JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-imprimir-presupuesto">Imprimir</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar pago -->
<div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagoModalLabel">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-pago">
                    <input type="hidden" id="pago_id_paciente" name="id_paciente" value="<?php echo $id_paciente; ?>">
                    <input type="hidden" id="pago_id_procedimiento_realizado" name="id_procedimiento_realizado">
                    <div class="mb-3">
                        <label for="pago-monto" class="form-label">Monto</label>
                        <input type="number" class="form-control" id="pago-monto" name="monto" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="pago-metodo" class="form-label">Método de Pago</label>
                        <select class="form-select" id="pago-metodo" name="metodo_pago" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pago-tipo" class="form-label">Tipo de Pago</label>
                        <select class="form-select" id="pago-tipo" name="tipo_pago" required>
                            <option value="adelanto">Adelanto</option>
                            <option value="pago final">Pago Final</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar Pago</button>
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
        const costoProcedimientoInput = document.getElementById('costo-procedimiento');
        
        // Elementos para el modal de edición de historial médico
        const btnEditarHistorial = document.getElementById('btn-editar-historial');
        const editarHistorialModal = new bootstrap.Modal(document.getElementById('editarHistorialModal'));
        const formEditarHistorial = document.getElementById('form-editar-historial');
        const enfermedadesTextarea = document.getElementById('enfermedades');
        const alergiasTextarea = document.getElementById('alergias');
        const medicacionTextarea = document.getElementById('medicacion');
        const habitosTextarea = document.getElementById('habitos');

        // Elementos para el modal de presupuesto
        const presupuestoModal = new bootstrap.Modal(document.getElementById('presupuestoModal'));
        const presupuestoDetalleDiv = document.getElementById('presupuesto-detalle');
        const btnImprimirPresupuesto = document.getElementById('btn-imprimir-presupuesto');

        // Elementos para el modal de pago
        const pagoModal = new bootstrap.Modal(document.getElementById('pagoModal'));
        const formPago = document.getElementById('form-pago');
        const pagoIdProcedimientoRealizadoInput = document.getElementById('pago_id_procedimiento_realizado');
        const pagoMontoInput = document.getElementById('pago-monto');

        // Elementos para el modal de edición de procedimiento
        const editarProcedimientoModal = new bootstrap.Modal(document.getElementById('editarProcedimientoModal'));
        const formEditarProcedimiento = document.getElementById('form-editar-procedimiento');
        const editIdProcedimientoRealizadoInput = document.getElementById('edit_id_procedimiento_realizado');
        const editCostoProcedimientoInput = document.getElementById('edit_costo_procedimiento');
        const editNotasEvolucionInput = document.getElementById('edit_notas_evolucion');


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
                            let procedimientosHtml = '';
                            let costoTotalPlan = 0;

                            if (plan.procedimientos) {
                                plan.procedimientos.forEach(proc => {
                                    costoTotalPlan += parseFloat(proc.costo_personalizado);
                                    let pagosProc = 0;
                                    if (proc.pagos) {
                                        proc.pagos.forEach(p => {
                                            pagosProc += parseFloat(p.monto);
                                        });
                                    }
                                    const saldoPendienteProc = proc.costo_personalizado - pagosProc;
                                    procedimientosHtml += `
                                        <li>
                                            <strong>${proc.nombre_tratamiento}</strong> - Costo: S/. ${proc.costo_personalizado} - Notas: ${proc.notas_evolucion || 'N/A'} - Pagado: S/. ${pagosProc.toFixed(2)} - Saldo: S/. ${saldoPendienteProc.toFixed(2)}
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-warning ms-2" onclick="mostrarModalEditarProcedimiento(${proc.id_procedimiento_realizado}, ${proc.costo_personalizado}, '${proc.notas_evolucion || ''}')">&#9998;</button>
                                                <button class="btn btn-sm btn-danger ms-2" onclick="eliminarProcedimiento(${proc.id_procedimiento_realizado})">&#128465;</button>
                                                <button class="btn btn-sm btn-primary ms-2" ${saldoPendienteProc <= 0 ? 'disabled' : ''} onclick="mostrarModalPago(${proc.id_procedimiento_realizado})">&#128179; Registrar Pago</button>
                                            </div>
                                        </li>
                                    `;
                                });
                            }
                            const planHtml = `
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="${headingId}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                                            Plan #${plan.id_plan_tratamiento} (${plan.estado_plan}) - Total: S/. ${costoTotalPlan.toFixed(2)}
                                        </button>
                                    </h2>
                                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}" data-bs-parent="#planes-tratamiento-accordion">
                                        <div class="accordion-body">
                                            <div class="d-flex justify-content-end mb-3">
                                                <button class="btn btn-sm btn-danger me-2" onclick="eliminarPlan(${plan.id_plan_tratamiento})">&#128465; Eliminar Plan</button>
                                                <button class="btn btn-sm btn-success me-2" onclick="mostrarModalProcedimiento(${plan.id_plan_tratamiento})">&#10133; Procedimiento</button>
                                                <button class="btn btn-sm btn-info text-white me-2" onclick="generarPresupuesto(${plan.id_plan_tratamiento})">&#128220; Presupuesto</button>
                                            </div>
                                            <h5>Procedimientos Realizados:</h5>
                                            <ul>${procedimientosHtml || '<li>No hay procedimientos registrados.</li>'}</ul>
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
                                    <td>${pago.id_plan_tratamiento ? `Plan #${pago.id_plan_tratamiento}` : 'Pago puntual'}</td>
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
        
        // Función global para mostrar el modal de editar procedimiento
        window.mostrarModalEditarProcedimiento = function(idProc, costo, notas) {
            editIdProcedimientoRealizadoInput.value = idProc;
            editCostoProcedimientoInput.value = costo;
            editNotasEvolucionInput.value = notas;
            editarProcedimientoModal.show();
        };

        // Evento para cargar el costo del tratamiento seleccionado
        tratamientoSelect.addEventListener('change', function() {
            const idTratamiento = this.value;
            if (idTratamiento) {
                fetch(`api/tratamientos.php?action=obtener&id=${idTratamiento}`)
                    .then(response => response.json())
                    .then(tratamiento => {
                        costoProcedimientoInput.value = tratamiento.costo_base;
                    })
                    .catch(error => console.error('Error al obtener costo del tratamiento:', error));
            }
        });

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
        
        // Manejar el envío del formulario de edición de procedimiento
        formEditarProcedimiento.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formEditarProcedimiento);
            const data = Object.fromEntries(formData.entries());

            fetch('api/atenciones.php?action=editar_procedimiento', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Procedimiento actualizado con éxito.');
                    editarProcedimientoModal.hide();
                    cargarHistorialPaciente();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al editar procedimiento:', error));
        });
        
        // Función global para eliminar un procedimiento
        window.eliminarProcedimiento = function(idProc) {
            if (confirm('¿Está seguro de que desea eliminar este procedimiento?')) {
                fetch(`api/atenciones.php?action=eliminar_procedimiento&id=${idProc}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Procedimiento eliminado con éxito.');
                        cargarHistorialPaciente();
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => console.error('Error al eliminar procedimiento:', error));
            }
        };

        // Función global para eliminar un plan
        window.eliminarPlan = function(idPlan) {
            if (confirm('¿Está seguro de que desea eliminar este plan de tratamiento? Se eliminarán todos los procedimientos y pagos asociados.')) {
                fetch(`api/atenciones.php?action=eliminar_plan_tratamiento&id=${idPlan}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Plan de tratamiento eliminado con éxito.');
                        cargarHistorialPaciente();
                    } else {
                        alert('Error: ' + result.message);
                    }
                })
                .catch(error => console.error('Error al eliminar el plan:', error));
            }
        };

        // Función global para mostrar el modal de pago
        window.mostrarModalPago = function(idProc, saldo) {
            if (saldo <= 0) {
                alert('La deuda de este procedimiento ya ha sido cancelada.');
                return;
            }
            pagoIdProcedimientoRealizadoInput.value = idProc;
            pagoModal.show();
        };
        
        // Manejar el envío del formulario de pago
        formPago.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formPago);
            const data = Object.fromEntries(formData.entries());
            
            fetch('api/atenciones.php?action=registrar_pago', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('La respuesta de la red no fue correcta.');
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    alert('Pago registrado con éxito.');
                    pagoModal.hide();
                    formPago.reset();
                    cargarHistorialPaciente(); // Recargar el historial
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => console.error('Error al registrar pago:', error));
        });

        // Función global para generar presupuesto
        window.generarPresupuesto = function(idPlan) {
            fetch(`api/atenciones.php?action=generar_presupuesto&id_plan_tratamiento=${idPlan}`)
                .then(response => response.json())
                .then(presupuesto => {
                    const clinica = presupuesto.clinica;
                    const paciente = presupuesto.paciente;
                    const tratamientos = presupuesto.tratamientos;
                    const pagos = presupuesto.pagos;
                    let costoTotal = 0;
                    let pagosTotales = 0;

                    let tratamientosHtml = '';
                    tratamientos.forEach(t => {
                        costoTotal += parseFloat(t.costo_personalizado);
                        tratamientosHtml += `
                            <tr>
                                <td>${t.nombre_tratamiento}</td>
                                <td>S/. ${t.costo_personalizado}</td>
                            </tr>
                        `;
                    });

                    let pagosHtml = '';
                    pagos.forEach(p => {
                        pagosTotales += parseFloat(p.monto);
                        pagosHtml += `
                            <tr>
                                <td>${p.fecha_pago}</td>
                                <td>S/. ${p.monto}</td>
                                <td>${p.metodo_pago}</td>
                            </tr>
                        `;
                    });

                    const saldoPendiente = costoTotal - pagosTotales;

                    presupuestoDetalleDiv.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Clínica</h5>
                                <p><strong>Nombre:</strong> ${clinica.nombre}</p>
                                <p><strong>Dirección:</strong> ${clinica.direccion}</p>
                                <p><strong>Teléfono:</strong> ${clinica.telefono}</p>
                                <p><strong>Doctor(a):</strong> ${clinica.nombre_doctor}</p>
                            </div>
                            <div class="col-md-6">
                                <h5>Paciente</h5>
                                <p><strong>Nombre:</strong> ${paciente.nombres} ${paciente.apellidos}</p>
                                <p><strong>DNI:</strong> ${paciente.numero_documento}</p>
                                <p><strong>Teléfono:</strong> ${paciente.telefono || 'N/A'}</p>
                                <p><strong>Correo:</strong> ${paciente.correo_electronico || 'N/A'}</p>
                            </div>
                        </div>
                        <h5 class="mt-4">Detalle del Tratamiento (Plan #${idPlan})</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tratamiento</th>
                                    <th>Costo</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tratamientosHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="1" class="text-end">Costo Total:</th>
                                    <th>S/. ${costoTotal.toFixed(2)}</th>
                                </tr>
                            </tfoot>
                        </table>
                        <h5 class="mt-4">Historial de Pagos</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${pagosHtml || '<tr><td colspan="3" class="text-center">No hay pagos registrados.</td></tr>'}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="1" class="text-end">Total Pagado:</th>
                                    <th>S/. ${pagosTotales.toFixed(2)}</th>
                                </tr>
                                <tr>
                                    <th colspan="1" class="text-end">Saldo Pendiente:</th>
                                    <th>S/. ${saldoPendiente.toFixed(2)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    `;
                    presupuestoModal.show();
                })
                .catch(error => {
                    console.error('Error al generar el presupuesto:', error);
                    alert('Error al generar el presupuesto.');
                });
        };


        cargarHistorialPaciente();
        cargarCombosModales();
    });
</script>