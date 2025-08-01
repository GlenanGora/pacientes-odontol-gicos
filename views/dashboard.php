<?php
/**
 * Archivo: dashboard.php
 * Ubicación: views/
 * Descripción: Muestra las estadísticas y reportes básicos de la clínica.
 * Autor: Gemini
 */
?>
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 d-flex justify-content-center">
            <div class="card-body">
                <h5 class="card-title">Pacientes Registrados</h5>
                <p class="h1 mb-0" id="total-pacientes">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 d-flex justify-content-center">
            <div class="card-body">
                <h5 class="card-title">Citas Agendadas Hoy</h5>
                <p class="h1 mb-0" id="citas-hoy">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 d-flex justify-content-center">
            <div class="card-body">
                <h5 class="card-title">Pacientes en Tratamiento</h5>
                <p class="h1 mb-0" id="pacientes-en-tratamiento">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-center h-100 d-flex justify-content-center">
            <div class="card-body">
                <h5 class="card-title">Pacientes Tratados</h5>
                <p class="h1 mb-0" id="pacientes-tratados">0</p>
            </div>
        </div>
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
                Pacientes por Departamento
            </div>
            <div class="card-body">
                <canvas id="pacientesDepartamentoChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Pacientes por Distrito
    </div>
    <div class="card-body">
        <canvas id="pacientesDistritoChart"></canvas>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Estadísticas de Procedimientos (Últimos 30 días)
    </div>
    <div class="card-body">
        <canvas id="procedimientosChart"></canvas>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let pacientesSexoChart, pacientesDepartamentoChart, pacientesDistritoChart, procedimientosChart;

        // Función para cargar los datos del dashboard de la API
        function cargarDatosDashboard() {
            fetch('api/dashboard.php')
                .then(response => response.json())
                .then(data => {
                    // Actualizar contadores
                    document.getElementById('total-pacientes').textContent = data.total_pacientes;
                    document.getElementById('citas-hoy').textContent = data.citas_hoy;
                    document.getElementById('pacientes-en-tratamiento').textContent = data.pacientes_por_estado.find(e => e.nombre_estado === 'Tratamiento')?.conteo || 0;
                    document.getElementById('pacientes-tratados').textContent = data.pacientes_por_estado.find(e => e.nombre_estado === 'Tratado')?.conteo || 0;
                    
                    // Gráfico de pacientes por sexo (Barras Horizontales)
                    if (pacientesSexoChart) { pacientesSexoChart.destroy(); }
                    const pacientesSexoCtx = document.getElementById('pacientesSexoChart').getContext('2d');
                    pacientesSexoChart = new Chart(pacientesSexoCtx, {
                        type: 'bar',
                        data: {
                            labels: data.pacientes_por_sexo.map(p => p.nombre_sexo),
                            datasets: [{
                                label: 'Cantidad',
                                data: data.pacientes_por_sexo.map(p => p.conteo),
                                backgroundColor: ['#0d6efd', '#dc3545', '#6c757d'],
                                borderColor: ['#0d6efd', '#dc3545', '#6c757d'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            scales: { x: { beginAtZero: true } }
                        }
                    });

                    // Gráfico de pacientes por departamento (Barras Horizontales)
                    if (pacientesDepartamentoChart) { pacientesDepartamentoChart.destroy(); }
                    const pacientesDepartamentoCtx = document.getElementById('pacientesDepartamentoChart').getContext('2d');
                    pacientesDepartamentoChart = new Chart(pacientesDepartamentoCtx, {
                        type: 'bar',
                        data: {
                            labels: data.pacientes_por_departamento.map(p => p.nombre),
                            datasets: [{
                                label: 'Cantidad',
                                data: data.pacientes_por_departamento.map(p => p.conteo),
                                backgroundColor: '#28a745',
                                borderColor: '#28a745',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            scales: { x: { beginAtZero: true } }
                        }
                    });
                    
                    // Gráfico de pacientes por distrito (Barras Horizontales)
                    if (pacientesDistritoChart) { pacientesDistritoChart.destroy(); }
                    const pacientesDistritoCtx = document.getElementById('pacientesDistritoChart').getContext('2d');
                    pacientesDistritoChart = new Chart(pacientesDistritoCtx, {
                        type: 'bar',
                        data: {
                            labels: data.pacientes_por_distrito.map(p => p.nombre),
                            datasets: [{
                                label: 'Cantidad',
                                data: data.pacientes_por_distrito.map(p => p.conteo),
                                backgroundColor: '#17a2b8',
                                borderColor: '#17a2b8',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            scales: { x: { beginAtZero: true } }
                        }
                    });
                    
                    // Gráfico de procedimientos más realizados
                    if (procedimientosChart) { procedimientosChart.destroy(); }
                    const procedimientosCtx = document.getElementById('procedimientosChart').getContext('2d');
                    procedimientosChart = new Chart(procedimientosCtx, {
                        type: 'bar',
                        data: {
                            labels: data.procedimientos_populares.map(p => p.nombre_tratamiento),
                            datasets: [{
                                label: 'Procedimientos más realizados',
                                data: data.procedimientos_populares.map(p => p.conteo),
                                backgroundColor: 'rgba(13, 110, 253, 0.5)',
                                borderColor: 'rgba(13, 110, 253, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: { y: { beginAtZero: true } }
                        }
                    });

                })
                .catch(error => console.error('Error al cargar datos del dashboard:', error));
        }

        cargarDatosDashboard();
    });
</script>
