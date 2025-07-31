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
</div>

<div class="card mb-4">
    <div class="card-header">
        Pacientes por Sexo
    </div>
    <div class="card-body">
        <canvas id="pacientesSexoChart"></canvas>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Procedimientos más realizados
    </div>
    <div class="card-body">
        <canvas id="procedimientosChart"></canvas>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Ingresos por mes
    </div>
    <div class="card-body">
        <canvas id="ingresosChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para cargar los datos de la API
        function cargarDatosReportes() {
            fetch('api/reportes.php')
                .then(response => response.json())
                .then(data => {
                    // Gráfico de pacientes por sexo
                    const pacientesSexoCtx = document.getElementById('pacientesSexoChart').getContext('2d');
                    new Chart(pacientesSexoCtx, {
                        type: 'pie',
                        data: {
                            labels: data.pacientes_por_sexo.map(p => p.nombre_sexo),
                            datasets: [{
                                data: data.pacientes_por_sexo.map(p => p.conteo),
                                backgroundColor: ['#0d6efd', '#fd7e14', '#6c757d'],
                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });

                    // Gráfico de procedimientos más realizados
                    const procedimientosCtx = document.getElementById('procedimientosChart').getContext('2d');
                    new Chart(procedimientosCtx, {
                        type: 'bar',
                        data: {
                            labels: data.procedimientos_populares.map(p => p.nombre_tratamiento),
                            datasets: [{
                                label: 'Cantidad de Procedimientos',
                                data: data.procedimientos_populares.map(p => p.conteo),
                                backgroundColor: 'rgba(13, 110, 253, 0.5)',
                                borderColor: 'rgba(13, 110, 253, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Gráfico de ingresos por mes
                    const ingresosCtx = document.getElementById('ingresosChart').getContext('2d');
                    new Chart(ingresosCtx, {
                        type: 'line',
                        data: {
                            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                            datasets: [{
                                label: 'Ingresos',
                                data: data.ingresos_anuales,
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                })
                .catch(error => console.error('Error al cargar datos de reportes:', error));
        }

        cargarDatosReportes();
    });
</script>
