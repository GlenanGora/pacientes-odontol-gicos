<?php
/**
 * Archivo: emitir_receta.php
 * Ubicación: views/recetas/
 * Descripción: Formulario para emitir una nueva receta médica.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Emitir Nueva Receta</h3>
    <a href="?page=recetas/historial" class="btn btn-secondary">
        <span class="me-2">&#8592;</span> Volver al Historial
    </a>
</div>

<div class="card">
    <div class="card-header">
        Datos de la Receta
    </div>
    <div class="card-body">
        <form id="form-receta">
            <input type="hidden" name="id_paciente" id="receta_id_paciente">
            <input type="hidden" name="id_procedimiento_realizado" id="receta_id_procedimiento_realizado_hidden">
            <div class="mb-3">
                <label class="form-label">Paciente</label>
                <input type="text" class="form-control" id="receta_nombre_paciente" disabled>
            </div>
            <div class="mb-3">
                <label for="indicaciones" class="form-label">Indicaciones Generales</label>
                <textarea class="form-control" id="indicaciones" name="indicaciones_generales" rows="3"></textarea>
            </div>

            <hr>
            <h5>Medicamentos</h5>
            <div id="medicamentos-container">
                <div class="row g-2 mb-2 medicamento-row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="medicamentos[0][nombre]" placeholder="Nombre" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="medicamentos[0][dosis]" placeholder="Dosis" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="medicamentos[0][frecuencia]" placeholder="Frecuencia">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <input type="text" class="form-control me-2" name="medicamentos[0][duracion]" placeholder="Duración">
                        <button type="button" class="btn btn-danger" onclick="eliminarMedicamento(this)">
                            &#10006;
                        </button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm mb-3" id="btn-agregar-medicamento">
                &#10133; Agregar Medicamento
            </button>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Emitir Receta</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formReceta = document.getElementById('form-receta');
        const medicamentosContainer = document.getElementById('medicamentos-container');
        const btnAgregarMedicamento = document.getElementById('btn-agregar-medicamento');
        let medicamentoIndex = 1;

        // Función para cargar los pacientes en el combo
        function cargarPacientes() {
            // Se corrige el fetch para que apunte a la API de pacientes
            fetch('api/pacientes.php')
                .then(response => response.json())
                .then(data => {
                    if (data.data) {
                        data.data.forEach(p => {
                            pacienteSelect.innerHTML += `<option value="${p.id_paciente}">${p.nombre_completo}</option>`;
                        });
                    }
                })
                .catch(error => console.error('Error al cargar pacientes:', error));
        }

        btnAgregarMedicamento.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'row g-2 mb-2 medicamento-row';
            newRow.innerHTML = `
                <div class="col-md-3">
                    <input type="text" class="form-control" name="medicamentos[${medicamentoIndex}][nombre]" placeholder="Nombre" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="medicamentos[${medicamentoIndex}][dosis]" placeholder="Dosis" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="medicamentos[${medicamentoIndex}][frecuencia]" placeholder="Frecuencia">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <input type="text" class="form-control me-2" name="medicamentos[${medicamentoIndex}][duracion]" placeholder="Duración">
                    <button type="button" class="btn btn-danger" onclick="eliminarMedicamento(this)">
                        &#10006;
                    </button>
                </div>
            `;
            medicamentosContainer.appendChild(newRow);
            medicamentoIndex++;
        });

        window.eliminarMedicamento = function(button) {
            const row = button.closest('.medicamento-row');
            if (row) {
                row.remove();
            }
        };

        formReceta.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formReceta);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Lógica de serialización de medicamentos
            const medicamentos = [];
            document.querySelectorAll('.medicamento-row').forEach(row => {
                const inputs = row.querySelectorAll('input');
                medicamentos.push({
                    nombre: inputs[0].value,
                    dosis: inputs[1].value,
                    frecuencia: inputs[2].value,
                    duracion: inputs[3].value
                });
            });
            data.medicamentos = medicamentos;

            fetch('api/recetas.php?action=emitir', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Receta emitida con éxito.');
                    formReceta.reset();
                    // Opcional: Redireccionar al historial o imprimir
                } else {
                    alert('Error al emitir la receta: ' + result.message);
                }
            })
            .catch(error => console.error('Error al emitir la receta:', error));
        });

        // La función de cargar pacientes se elimina porque la información del paciente se pasa
        // a través de la URL.
        //cargarPacientes();
    });
</script>