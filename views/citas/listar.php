<?php
/**
 * Archivo: listar.php
 * Ubicación: views/citas/
 * Descripción: Muestra el calendario de citas con una vista diaria y un selector para vista mensual.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Gestión de Citas</h3>
    <div class="d-flex align-items-center">
        <a href="?page=citas/agendar" class="btn btn-primary me-3">
            <span class="me-2">&#10133;</span> Agendar Nueva Cita
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-secondary active" id="btn-vista-dia">Vista por Día</button>
            <button type="button" class="btn btn-secondary" id="btn-vista-mes">Vista por Mes</button>
        </div>
    </div>
</div>

<div id="vista-diaria">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-outline-secondary" id="prev-day-btn">&#9664; Día Anterior</button>
                <h5 class="mb-0" id="current-date-title"></h5>
                <button class="btn btn-outline-secondary" id="next-day-btn">Día Siguiente &#9654;</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Hora</th>
                            <th>Citas Agendadas</th>
                        </tr>
                    </thead>
                    <tbody id="citas-table-body">
                        <!-- Las citas se cargarán aquí con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="vista-mensual" style="display: none;">
    <!-- La vista mensual se cargará dinámicamente aquí -->
</div>


<!-- Modal para detalles de la cita -->
<div class="modal fade" id="citaModal" tabindex="-1" aria-labelledby="citaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="citaModalLabel">Detalles de la Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Paciente:</strong> <span id="modal-paciente"></span></p>
                <p><strong>Tipo de Cita:</strong> <span id="modal-tipo-cita"></span></p>
                <p><strong>Duración:</strong> <span id="modal-duracion"></span> minutos</p>
                <p><strong>Estado:</strong> <span id="modal-estado"></span></p>
                <p><strong>Fecha y Hora:</strong> <span id="modal-fecha-hora"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="btn-cancelar-cita">Cancelar Cita</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentDate = new Date();
        const citasTableBody = document.getElementById('citas-table-body');
        const currentDateTitle = document.getElementById('current-date-title');
        const prevDayBtn = document.getElementById('prev-day-btn');
        const nextDayBtn = document.getElementById('next-day-btn');

        const btnVistaDia = document.getElementById('btn-vista-dia');
        const btnVistaMes = document.getElementById('btn-vista-mes');
        const vistaDiariaDiv = document.getElementById('vista-diaria');
        const vistaMensualDiv = document.getElementById('vista-mensual');

        // Función para formatear la fecha
        function formatFecha(date) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('es-ES', options);
        }

        // Función para cargar las citas del día seleccionado
        function cargarCitas(date) {
            const formattedDate = date.toISOString().split('T')[0];
            currentDateTitle.textContent = formatFecha(date);
            
            // Llenar las horas del día de 9 a 18
            citasTableBody.innerHTML = '';
            for (let i = 9; i <= 18; i++) {
                const hour = String(i).padStart(2, '0') + ':00';
                citasTableBody.innerHTML += `
                    <tr>
                        <td class="bg-light">${hour}</td>
                        <td id="hora-${i}"></td>
                    </tr>
                `;
            }

            // Llamada a la API para obtener citas (se creará en el siguiente paso)
            fetch(`api/citas.php?fecha=${formattedDate}`)
                .then(response => response.json())
                .then(citas => {
                    citas.forEach(cita => {
                        const citaHora = new Date(cita.fecha + ' ' + cita.hora);
                        const horaSlot = citaHora.getHours();
                        const slotElement = document.getElementById(`hora-${horaSlot}`);

                        if (slotElement) {
                            const citaHtml = `
                                <div class="alert alert-info py-2 px-3 mb-1" role="alert" style="cursor:pointer;"
                                    onclick='mostrarDetallesCita(${JSON.stringify(cita)})'>
                                    <strong>${cita.hora}</strong> - ${cita.nombre_paciente} (${cita.tipo_cita})
                                </div>
                            `;
                            slotElement.innerHTML += citaHtml;
                        }
                    });
                })
                .catch(error => console.error('Error al cargar las citas:', error));
        }

        // Función para cargar la vista mensual
        function cargarVistaMensual() {
            fetch('views/citas/calendario_mensual.php')
                .then(response => response.text())
                .then(html => {
                    vistaMensualDiv.innerHTML = html;
                })
                .catch(error => console.error('Error al cargar la vista mensual:', error));
        }

        // Manejar la navegación entre días
        prevDayBtn.addEventListener('click', () => {
            currentDate.setDate(currentDate.getDate() - 1);
            cargarCitas(currentDate);
        });

        nextDayBtn.addEventListener('click', () => {
            currentDate.setDate(currentDate.getDate() + 1);
            cargarCitas(currentDate);
        });

        // Manejar el cambio de vista
        btnVistaDia.addEventListener('click', () => {
            btnVistaDia.classList.add('active');
            btnVistaMes.classList.remove('active');
            vistaDiariaDiv.style.display = 'block';
            vistaMensualDiv.style.display = 'none';
        });

        btnVistaMes.addEventListener('click', () => {
            btnVistaMes.classList.add('active');
            btnVistaDia.classList.remove('active');
            vistaDiariaDiv.style.display = 'none';
            vistaMensualDiv.style.display = 'block';
            cargarVistaMensual();
        });


        // Función para mostrar los detalles de la cita en el modal
        window.mostrarDetallesCita = function(cita) {
            const modalTitle = document.getElementById('citaModalLabel');
            const modalPaciente = document.getElementById('modal-paciente');
            const modalTipoCita = document.getElementById('modal-tipo-cita');
            const modalDuracion = document.getElementById('modal-duracion');
            const modalEstado = document.getElementById('modal-estado');
            const modalFechaHora = document.getElementById('modal-fecha-hora');

            modalTitle.textContent = `Cita con ${cita.nombre_paciente}`;
            modalPaciente.textContent = cita.nombre_paciente;
            modalTipoCita.textContent = cita.tipo_cita;
            modalDuracion.textContent = cita.duracion;
            modalEstado.textContent = cita.estado;
            modalFechaHora.textContent = `${formatFecha(new Date(cita.fecha))} a las ${cita.hora}`;

            // Configurar el botón de cancelar
            const btnCancelar = document.getElementById('btn-cancelar-cita');
            btnCancelar.onclick = () => {
                cancelarCita(cita.id_cita);
            };

            const citaModal = new bootstrap.Modal(document.getElementById('citaModal'));
            citaModal.show();
        };

        // Función para cancelar la cita
        function cancelarCita(idCita) {
            if (confirm('¿Está seguro de que desea cancelar esta cita?')) {
                fetch('api/citas.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id_cita: idCita, estado: 'cancelada' }),
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Cita cancelada con éxito.');
                        const citaModal = bootstrap.Modal.getInstance(document.getElementById('citaModal'));
                        citaModal.hide();
                        cargarCitas(currentDate); // Recargar citas
                    } else {
                        alert('Error al cancelar la cita: ' + result.message);
                    }
                })
                .catch(error => console.error('Error al cancelar la cita:', error));
            }
        }

        // Carga las citas al cargar la página por primera vez
        cargarCitas(currentDate);
    });
</script>
