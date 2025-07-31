<?php
/**
 * Archivo: agendar.php
 * Ubicación: views/citas/
 * Descripción: Formulario para agendar una nueva cita.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Agendar Nueva Cita</h3>
    <a href="?page=citas/listar" class="btn btn-secondary">
        <span class="me-2">&#8592;</span> Volver al Calendario
    </a>
</div>

<div class="card">
    <div class="card-header">
        Datos de la Cita
    </div>
    <div class="card-body">
        <form id="form-agendar-cita" class="row g-3">
            <div class="col-md-12">
                <label for="id_paciente" class="form-label">Paciente</label>
                <select id="id_paciente" name="id_paciente" class="form-select" required>
                    <option value="">Seleccione un paciente...</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>
            <div class="col-md-6">
                <label for="hora" class="form-label">Hora</label>
                <select id="hora" name="hora" class="form-select" required>
                    <option value="">Seleccione una hora...</option>
                    <!-- Las opciones de hora se generarán con JavaScript -->
                </select>
            </div>
            <div class="col-md-6">
                <label for="duracion" class="form-label">Duración (minutos)</label>
                <select id="duracion" name="duracion" class="form-select" required>
                    <option value="15">15 minutos</option>
                    <option value="30" selected>30 minutos</option>
                    <option value="45">45 minutos</option>
                    <option value="60">60 minutos</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="tipo_cita" class="form-label">Tipo de Cita</label>
                <input type="text" class="form-control" id="tipo_cita" name="tipo_cita" required>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary">Agendar Cita</button>
                <a href="?page=citas/listar" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-agendar-cita');
        const idPacienteSelect = document.getElementById('id_paciente');
        const horaSelect = document.getElementById('hora');

        // Función para cargar los pacientes en el combo
        function cargarPacientesCombo() {
            fetch('api/citas.php?pacientes=true')
                .then(response => response.json())
                .then(pacientes => {
                    pacientes.forEach(paciente => {
                        const option = document.createElement('option');
                        option.value = paciente.id;
                        option.textContent = paciente.nombre;
                        idPacienteSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar pacientes:', error));
        }

        // Función para generar las opciones de hora con intervalos de 30 minutos
        function generarHorasDisponibles() {
            const startHour = 9; // 9:00 AM
            const endHour = 18;  // 6:00 PM
            for (let h = startHour; h <= endHour; h++) {
                for (let m = 0; m < 60; m += 30) {
                    const hour = String(h).padStart(2, '0');
                    const minute = String(m).padStart(2, '0');
                    const timeString = `${hour}:${minute}`;
                    const option = document.createElement('option');
                    option.value = timeString;
                    option.textContent = timeString;
                    horaSelect.appendChild(option);
                }
            }
        }
        
        // Manejar el envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            fetch('api/citas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Cita agendada con éxito.');
                    window.location.href = '?page=citas/listar'; // Redireccionar al calendario
                } else {
                    alert('Error al agendar la cita: ' + result.message);
                }
            })
            .catch(error => console.error('Error al enviar el formulario:', error));
        });

        cargarPacientesCombo();
        generarHorasDisponibles();
    });
</script>