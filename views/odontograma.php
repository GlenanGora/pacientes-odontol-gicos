<?php
/**
 * Archivo: odontograma.php
 * Ubicación: views/
 * Descripción: Muestra un odontograma interactivo para el paciente.
 * Autor: Gemini
 */

$id_paciente = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$id_paciente) {
    echo '<div class="alert alert-danger">Error: ID de paciente no especificado.</div>';
    exit();
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Odontograma del Paciente: <span id="paciente-nombre-odontograma"></span></h3>
    <div>
        <a href="?page=pacientes/historia_clinica&id=<?php echo $id_paciente; ?>" class="btn btn-secondary me-2">
            &#8592; Volver al Paciente
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" id="btn-adulto">Adulto</button>
            <button type="button" class="btn btn-primary" id="btn-nino">Niño</button>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Leyenda de Estados
    </div>
    <div class="card-body d-flex flex-wrap">
        <div class="me-4"><span class="color-box bg-success"></span> Sano</div>
        <div class="me-4"><span class="color-box bg-danger"></span> Caries</div>
        <div class="me-4"><span class="color-box bg-primary"></span> Obturado</div>
        <div class="me-4"><span class="color-box bg-warning"></span> Corona</div>
        <div class="me-4"><span class="color-box bg-secondary"></span> Ausente</div>
        <div class="me-4"><span class="color-box bg-danger-extra"></span> A extraer</div>
        <div class="me-4"><span class="color-box bg-purple"></span> Endodoncia</div>
        <div class="me-4"><span class="color-box bg-info-extra"></span> Implante</div>
    </div>
</div>

<div class="card mb-4 text-center">
    <div class="card-body">
        <h4>Maxilar Superior</h4>
        <div id="odontograma-superior" class="odontograma-row mb-4">
            <!-- Los dientes se cargarán aquí -->
        </div>
        <h4>Maxilar Inferior</h4>
        <div id="odontograma-inferior" class="odontograma-row">
            <!-- Los dientes se cargarán aquí -->
        </div>
    </div>
</div>

<div class="d-grid gap-2 mb-4">
    <button type="button" class="btn btn-primary" id="btn-guardar-odontograma">Guardar Cambios</button>
</div>

<div class="card">
    <div class="card-header">
        Historial de Odontogramas
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Procedimiento Asociado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="odontograma-historial-body">
                    <!-- El historial de odontogramas se cargará aquí -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .odontograma-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .diente-container {
        position: relative;
        width: 40px;
        height: 60px;
        margin: 5px;
        cursor: pointer;
        border: 1px solid #ccc;
        border-radius: 5px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding: 5px;
        transition: transform 0.2s;
    }
    .diente-container:hover {
        transform: scale(1.1);
    }
    .diente-numero {
        font-weight: bold;
    }
    .diente-estado {
        width: 100%;
        height: 15px;
        border-radius: 3px;
    }
    .color-box {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 3px;
        margin-right: 5px;
    }
    .bg-danger-extra { background-color: #dc3545; }
    .bg-purple { background-color: #6f42c1; }
    .bg-info-extra { background-color: #17a2b8; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const idPaciente = '<?php echo $id_paciente; ?>';
        const odontogramaSuperiorDiv = document.getElementById('odontograma-superior');
        const odontogramaInferiorDiv = document.getElementById('odontograma-inferior');
        const btnAdulto = document.getElementById('btn-adulto');
        const btnNino = document.getElementById('btn-nino');
        const btnGuardar = document.getElementById('btn-guardar-odontograma');

        const estadoColores = {
            'Sano': 'bg-success',
            'Caries': 'bg-danger',
            'Obturado': 'bg-primary',
            'Corona': 'bg-warning',
            'Ausente': 'bg-secondary',
            'A extraer': 'bg-danger-extra',
            'Endodoncia': 'bg-purple',
            'Implante': 'bg-info-extra'
        };

        const dientesAdulto = {
            superior: [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28],
            inferior: [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38]
        };

        const dientesNino = {
            superior: [55, 54, 53, 52, 51, 61, 62, 63, 64, 65],
            inferior: [85, 84, 83, 82, 81, 71, 72, 73, 74, 75]
        };

        let tipoDenticion = 'adulto';
        let odontogramaData = {};

        function generarOdontograma(denticion) {
            odontogramaSuperiorDiv.innerHTML = '';
            odontogramaInferiorDiv.innerHTML = '';
            
            const superior = denticion === 'adulto' ? dientesAdulto.superior : dientesNino.superior;
            const inferior = denticion === 'adulto' ? dientesAdulto.inferior : dientesNino.inferior;
            
            superior.forEach(diente => {
                const dienteHtml = `
                    <div class="diente-container" data-diente="${diente}">
                        <div class="diente-numero">${diente}</div>
                        <div class="diente-estado"></div>
                    </div>
                `;
                odontogramaSuperiorDiv.innerHTML += dienteHtml;
            });
            
            inferior.forEach(diente => {
                const dienteHtml = `
                    <div class="diente-container" data-diente="${diente}">
                        <div class="diente-numero">${diente}</div>
                        <div class="diente-estado"></div>
                    </div>
                `;
                odontogramaInferiorDiv.innerHTML += dienteHtml;
            });
        }
        
        function cargarOdontogramaInicial() {
            fetch(`api/odontograma.php?id_paciente=${idPaciente}`)
                .then(response => response.json())
                .then(data => {
                    odontogramaData = data.odontograma_data || {};
                    tipoDenticion = data.tipo_denticion || 'adulto';
                    
                    if (tipoDenticion === 'adulto') {
                        btnAdulto.classList.add('active');
                        btnNino.classList.remove('active');
                    } else {
                        btnNino.classList.add('active');
                        btnAdulto.classList.remove('active');
                    }
                    
                    generarOdontograma(tipoDenticion);
                    aplicarEstados(odontogramaData);
                })
                .catch(error => {
                    console.error('Error al cargar odontograma inicial:', error);
                    generarOdontograma('adulto');
                });
        }

        function aplicarEstados(data) {
            document.querySelectorAll('.diente-container').forEach(dienteDiv => {
                const dienteNumero = dienteDiv.dataset.diente;
                const estadoDiv = dienteDiv.querySelector('.diente-estado');
                const estado = data[dienteNumero] || 'Sano';
                
                estadoDiv.className = 'diente-estado';
                estadoDiv.classList.add(estadoColores[estado]);
            });
        }
        
        function seleccionarDiente(e) {
            const dienteContainer = e.target.closest('.diente-container');
            if (!dienteContainer) return;
            
            const dienteNumero = dienteContainer.dataset.diente;
            const estadoActual = odontogramaData[dienteNumero] || 'Sano';
            const estados = Object.keys(estadoColores);
            const estadoIndex = estados.indexOf(estadoActual);
            const nuevoEstado = estados[(estadoIndex + 1) % estados.length];
            
            odontogramaData[dienteNumero] = nuevoEstado;
            aplicarEstados(odontogramaData);
        }

        odontogramaSuperiorDiv.addEventListener('click', seleccionarDiente);
        odontogramaInferiorDiv.addEventListener('click', seleccionarDiente);

        btnAdulto.addEventListener('click', () => {
            tipoDenticion = 'adulto';
            btnAdulto.classList.add('active');
            btnNino.classList.remove('active');
            generarOdontograma('adulto');
            aplicarEstados(odontogramaData);
        });

        btnNino.addEventListener('click', () => {
            tipoDenticion = 'nino';
            btnNino.classList.add('active');
            btnAdulto.classList.remove('active');
            generarOdontograma('nino');
            aplicarEstados(odontogramaData);
        });
        
        btnGuardar.addEventListener('click', function() {
            const odontogramaParaGuardar = {
                id_paciente: idPaciente,
                tipo_denticion: tipoDenticion,
                odontograma_data: JSON.stringify(odontogramaData)
            };
            
            fetch('api/odontograma.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(odontogramaParaGuardar)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Odontograma guardado con éxito.');
                    // Opcional: Recargar la vista
                } else {
                    alert('Error al guardar el odontograma: ' + result.message);
                }
            })
            .catch(error => console.error('Error al guardar odontograma:', error));
        });

        cargarOdontogramaInicial();
    });
</script>
