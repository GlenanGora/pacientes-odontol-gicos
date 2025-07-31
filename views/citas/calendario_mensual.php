<?php
/**
 * Archivo: calendario_mensual.php
 * Ubicación: views/citas/
 * Descripción: Muestra las citas en un formato de calendario mensual.
 * Autor: Gemini
 */
?>
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-secondary" id="prev-month-btn">&#9664; Mes Anterior</button>
            <h5 class="mb-0" id="current-month-title"></h5>
            <button class="btn btn-outline-secondary" id="next-month-btn">Mes Siguiente &#9654;</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th>Dom</th>
                        <th>Lun</th>
                        <th>Mar</th>
                        <th>Mié</th>
                        <th>Jue</th>
                        <th>Vie</th>
                        <th>Sáb</th>
                    </tr>
                </thead>
                <tbody id="calendario-mensual-body">
                    <!-- Los días del mes se cargarán aquí con JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentMonth = new Date();
        const calendarioMensualBody = document.getElementById('calendario-mensual-body');
        const currentMonthTitle = document.getElementById('current-month-title');
        const prevMonthBtn = document.getElementById('prev-month-btn');
        const nextMonthBtn = document.getElementById('next-month-btn');

        function formatMonth(date) {
            const options = { year: 'numeric', month: 'long' };
            return date.toLocaleDateString('es-ES', options);
        }

        function cargarCalendario(date) {
            currentMonthTitle.textContent = formatMonth(date);
            calendarioMensualBody.innerHTML = '';

            const startOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
            const endOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            let startDay = startOfMonth.getDay(); // 0-6 (domingo-sábado)
            
            let html = '<tr>';
            
            // Celdas vacías para los días antes del primer día del mes
            for (let i = 0; i < startDay; i++) {
                html += '<td></td>';
            }

            for (let i = 1; i <= endOfMonth.getDate(); i++) {
                const dayDate = new Date(date.getFullYear(), date.getMonth(), i);
                const day = dayDate.getDay();
                
                html += `
                    <td style="vertical-align: top; overflow: auto; height: 120px;">
                        <strong>${i}</strong>
                        <div id="citas-dia-${i}" class="citas-container"></div>
                    </td>
                `;
                
                if (day === 6) { // Si es sábado, cerrar la fila y abrir una nueva
                    html += '</tr><tr>';
                }
            }

            // Celdas vacías para los días después del último día del mes
            let endDay = endOfMonth.getDay();
            for (let i = endDay; i < 6; i++) {
                html += '<td></td>';
            }

            html += '</tr>';
            calendarioMensualBody.innerHTML = html;

            cargarCitasMensuales(date.getFullYear(), date.getMonth());
        }

        function cargarCitasMensuales(year, month) {
            const url = `api/citas.php?mes=${year}-${month + 1}`;
            fetch(url)
                .then(response => response.json())
                .then(citas => {
                    citas.forEach(cita => {
                        const dia = new Date(cita.fecha + 'T' + cita.hora).getDate();
                        const diaContainer = document.getElementById(`citas-dia-${dia}`);
                        if (diaContainer) {
                            diaContainer.innerHTML += `
                                <div class="alert alert-info py-1 px-2 mb-1" role="alert" style="font-size: 0.8rem; cursor: pointer;"
                                    onclick='mostrarDetallesCita(${JSON.stringify(cita)})'>
                                    ${cita.hora} - ${cita.nombre_paciente}
                                </div>
                            `;
                        }
                    });
                })
                .catch(error => console.error('Error al cargar citas mensuales:', error));
        }

        prevMonthBtn.addEventListener('click', () => {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
            cargarCalendario(currentMonth);
        });

        nextMonthBtn.addEventListener('click', () => {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
            cargarCalendario(currentMonth);
        });
        
        cargarCalendario(currentMonth);

        window.mostrarDetallesCita = function(cita) {
            const citaModal = new bootstrap.Modal(document.getElementById('citaModal'));
            document.getElementById('modal-paciente').textContent = cita.nombre_paciente;
            document.getElementById('modal-tipo-cita').textContent = cita.tipo_cita;
            document.getElementById('modal-duracion').textContent = cita.duracion;
            document.getElementById('modal-estado').textContent = cita.estado;
            document.getElementById('modal-fecha-hora').textContent = `${cita.fecha} a las ${cita.hora}`;
            
            document.getElementById('btn-cancelar-cita').onclick = () => {
                cancelarCita(cita.id_cita);
            };

            citaModal.show();
        };

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
                        cargarCalendario(currentMonth); // Recargar el calendario
                    } else {
                        alert('Error al cancelar la cita: ' + result.message);
                    }
                })
                .catch(error => console.error('Error al cancelar la cita:', error));
            }
        }
    });
</script>

<style>
    .citas-container {
        height: 100%;
        min-height: 50px;
        overflow-y: auto;
    }
</style>