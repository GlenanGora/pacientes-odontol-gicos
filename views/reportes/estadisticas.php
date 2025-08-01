<?php
/**
 * Archivo: estadisticas.php
 * Ubicación: views/reportes/
 * Descripción: Muestra los reportes y estadísticas de la clínica.
 * Autor: Gemini
 */
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Reportes y Estadísticas</h3>
    <div class="d-flex align-items-center">
        <label for="filtro-anio" class="form-label me-2 mb-0">Año:</label>
        <select id="filtro-anio" class="form-select w-auto">
            <?php
                $anio_actual = date('Y');
                for ($i = $anio_actual; $i >= 2020; $i--) {
                    echo "<option value='{$i}'>{$i}</option>";
                }
            ?>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                Pacientes por Sexo
            </div>
            <div class="card-body">
                <canvas id="pacientesSexoChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                Estadísticas de Diagnósticos
            </div>
            <div class="card-body">
                <canvas id="diagnosticosChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Procedimientos más realizados (Últimos 12 meses)
    </div>
    <div class="card-body">
        <canvas id="procedimientosMesChart"></canvas>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                Pacientes con Deuda Pendiente
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Plan</th>
                                <th>Monto Pendiente</th>
                            </tr>
                        </thead>
                        <tbody id="pacientes-deuda-body">
                            <!-- Los pacientes con deuda se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                Próximas Citas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Tipo de Cita</th>
                            </tr>
                        </thead>
                        <tbody id="proximas-citas-body">
                            <!-- Las próximas citas se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filtroAnioSelect = document.getElementById('filtro-anio');
        let pacientesSexoChart, diagnosticosChart, procedimientosMesChart;

        // Función para cargar los datos de la API
        function cargarDatosReportes() {
            const anio = filtroAnioSelect.value;
            fetch(`api/reportes.php?anio=${anio}`)
                .then(response => response.json())
                .then(data => {
                    // Gráfico de pacientes por sexo (Barras Horizontales)
                    if (pacientesSexoChart) {
                        pacientesSexoChart.destroy();
                    }
                    const pacientesSexoCtx = document.getElementById('pacientesSexoChart').getContext('2d');
                    pacientesSexoChart = new Chart(pacientesSexoCtx, {
                        type: 'bar',
                        data: {
                            labels: data.pacientes_por_sexo.map(p => p.nombre_sexo),
                            datasets: [{
                                label: 'Pacientes',
                                data: data.pacientes_por_sexo.map(p => p.conteo),
                                backgroundColor: ['#0D6EFD', '#DC3545', '#6C757D'],
                            }]
                        },
                        options: {
                            indexAxis: 'y', // Hace las barras horizontales
                            responsive: true
                        }
                    });

                    // Gráfico de estadísticas de diagnósticos
                    if (diagnosticosChart) {
                        diagnosticosChart.destroy();
                    }
                    const diagnosticosCtx = document.getElementById('diagnosticosChart').getContext('2d');
                    diagnosticosChart = new Chart(diagnosticosCtx, {
                        type: 'doughnut',
                        data: {
                            labels: data.estadisticas_diagnostico.map(d => d.nombre_diagnostico),
                            datasets: [{
                                data: data.estadisticas_diagnostico.map(d => d.conteo),
                                backgroundColor: [
                                    '#0d6efd', '#fd7e14', '#6c757d', '#28a745', '#17a2b8', '#ffc107'
                                ],
                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });

                    // Gráfico de procedimientos más realizados por mes
                    if (procedimientosMesChart) {
                        procedimientosMesChart.destroy();
                    }
                    const procedimientosMesCtx = document.getElementById('procedimientosMesChart').getContext('2d');
                    const procedimientosData = {};
                    data.procedimientos_por_mes.forEach(item => {
                        if (!procedimientosData[item.mes]) {
                            procedimientosData[item.mes] = {};
                        }
                        procedimientosData[item.mes][item.nombre_tratamiento] = item.conteo;
                    });
                    const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                    const datasets = [];
                    const colores = ['#0d6efd', '#28a745', '#fd7e14', '#17a2b8', '#6c757d', '#ffc107', '#6f42c1', '#e83e8c', '#dc3545', '#f8f9fa', '#343a40', '#007bff'];
                    let colorIndex = 0;

                    const todosLosProcedimientos = [...new Set(data.procedimientos_por_mes.map(item => item.nombre_tratamiento))];

                    todosLosProcedimientos.forEach(procedimiento => {
                        const dataP = meses.map((mes, index) => {
                            return procedimientosData[index + 1] ? (procedimientosData[index + 1][procedimiento] || 0) : 0;
                        });
                        datasets.push({
                            label: procedimiento,
                            data: dataP,
                            backgroundColor: colores[colorIndex++ % colores.length],
                            borderColor: colores[colorIndex % colores.length],
                            borderWidth: 1
                        });
                    });

                    procedimientosMesChart = new Chart(procedimientosMesCtx, {
                        type: 'bar',
                        data: {
                            labels: meses,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { stacked: true },
                                y: { stacked: true, beginAtZero: true }
                            }
                        }
                    });

                    // Tabla de pacientes con deuda
                    const pacientesDeudaBody = document.getElementById('pacientes-deuda-body');
                    pacientesDeudaBody.innerHTML = '';
                    if (data.pacientes_con_deuda && data.pacientes_con_deuda.length > 0) {
                        data.pacientes_con_deuda.forEach(p => {
                            const row = `
                                <tr>
                                    <td>${p.nombre_completo}</td>
                                    <td>${p.nombre_diagnostico}</td>
                                    <td class="text-danger">S/. ${p.saldo_pendiente}</td>
                                </tr>
                            `;
                            pacientesDeudaBody.innerHTML += row;
                        });
                    } else {
                        pacientesDeudaBody.innerHTML = '<tr><td colspan="3" class="text-center">No hay pacientes con deuda.</td></tr>';
                    }
                    
                    // Tabla de próximas citas
                    const proximasCitasBody = document.getElementById('proximas-citas-body');
                    proximasCitasBody.innerHTML = '';
                    if (data.proximas_citas && data.proximas_citas.length > 0) {
                        data.proximas_citas.forEach(c => {
                            const row = `
                                <tr>
                                    <td>${c.nombre_paciente}</td>
                                    <td>${c.fecha}</td>
                                    <td>${c.hora}</td>
                                    <td>${c.tipo_cita}</td>
                                </tr>
                            `;
                            proximasCitasBody.innerHTML += row;
                        });
                    } else {
                        proximasCitasBody.innerHTML = '<tr><td colspan="4" class="text-center">No hay próximas citas.</td></tr>';
                    }
                })
                .catch(error => console.error('Error al cargar datos de reportes:', error));
        }

        filtroAnioSelect.addEventListener('change', cargarDatosReportes);

        cargarDatosReportes();
    });
</script>
