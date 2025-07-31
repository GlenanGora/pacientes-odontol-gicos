<?php
/**
 * Archivo: dashboard.php
 * Ubicación: views/
 * Descripción: Muestra las estadísticas y reportes básicos de la clínica.
 * Autor: Gemini
 */
?>
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Pacientes Registrados</h5>
                <p class="h1" id="total-pacientes">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Citas Agendadas Hoy</h5>
                <p class="h1" id="citas-hoy">0</p>
            </div>
        </div>
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

<div class="card mb-4">
    <div class="card-header">
        Ingresos vs. Gastos (Anual)
    </div>
    <div class="card-body">
        <canvas id="ingresosGastosChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener datos del dashboard de la API (se creará en el siguiente paso)
        fetch('api/dashboard.php')
            .then(response => response.json())
            .then(data => {
                // Actualizar contadores
                document.getElementById('total-pacientes').textContent = data.total_pacientes;
                document.getElementById('citas-hoy').textContent = data.citas_hoy;
                
                // Gráfico de procedimientos más realizados
                const procedimientosCtx = document.getElementById('procedimientosChart').getContext('2d');
                new Chart(procedimientosCtx, {
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
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                
                // Gráfico de ingresos vs. gastos
                const ingresosGastosCtx = document.getElementById('ingresosGastosChart').getContext('2d');
                new Chart(ingresosGastosCtx, {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [{
                            label: 'Ingresos',
                            data: data.ingresos_anuales,
                            borderColor: 'rgba(40, 167, 69, 1)',
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            tension: 0.4,
                            fill: true
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
            .catch(error => console.error('Error al cargar datos del dashboard:', error));
    });
</script>
