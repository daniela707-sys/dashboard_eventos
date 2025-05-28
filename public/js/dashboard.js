document.addEventListener('DOMContentLoaded', function() {
    // Cargar estadísticas generales
    cargarEstadisticasGenerales();
    
    // Si estamos en dashboard.php con un ID de evento
    const urlParams = new URLSearchParams(window.location.search);
    const idEvento = urlParams.get('id');
    
    if (idEvento) {
        cargarGraficosEvento(idEvento);
    }
    
    // Manejar el botón de ver dashboard
    const verDashboardBtn = document.getElementById('verDashboard');
    if (verDashboardBtn) {
        verDashboardBtn.addEventListener('click', function() {
        const eventoId = document.getElementById('evento').value;
        if (eventoId) {
            window.location.href = `dashboard.php?id=${eventoId}`;
        } else {
            window.location.href = 'dashboard.php';
        }
        });
    }
});

function cargarEstadisticasGenerales() {
    fetch('data/estadisticas_generales.php')
        .then(response => response.json())
        .then(data => {
            if (document.getElementById('total-eventos')) document.getElementById('total-eventos').textContent = data.total_eventos;
            if (document.getElementById('eventos-activos')) document.getElementById('eventos-activos').textContent = data.eventos_activos;
            if (document.getElementById('promedio-asistentes')) document.getElementById('promedio-asistentes').textContent = data.promedio_asistentes;
            if (document.getElementById('evento-popular')) document.getElementById('evento-popular').textContent = data.evento_popular;
            if (document.getElementById('ingresos-proyectados')) document.getElementById('ingresos-proyectados').textContent = 
                `${(data.ingresos_proyectados ? data.ingresos_proyectados.toLocaleString() : '0')}`;
            
            // Cargar gráficos generales
            cargarGraficoDepartamentos();
            cargarGraficoEstadosEventos();
            cargarGraficoPreciosEventos();
        })
        .catch(error => console.error('Error cargando estadísticas:', error));
}

function cargarGraficosEvento(idEvento) {
    cargarGraficoAsistenciaPorHora(idEvento);
    cargarGraficoAforoVsRegistrados(idEvento);
}

function cargarGraficoAsistenciaPorHora(idEvento) {
    fetch(`data/asistencia_por_hora.php?id_evento=${idEvento}`)
        .then(response => response.json())
        .then(data => {
            console.log('Datos asistencia por hora:', data);
            
            if (!data.datos || data.datos.length === 0) {
                document.getElementById('asistencia-por-hora-chart').innerHTML = 
                    '<div class="no-data">No hay datos de asistencia por hora para este evento</div>';
                return;
            }
            
            // Crear el gráfico
            const chart = anychart.line();
            chart.line(data.datos);
            chart.title('Asistencia por Hora');
            chart.xAxis().title('Hora del día');
            chart.yAxis().title('Número de registros');
            
            // Mostrar hora pico
            if (data.hora_pico) {
                const horaPicoInfo = document.createElement('div');
                horaPicoInfo.className = 'hora-pico-info';
                horaPicoInfo.innerHTML = `<strong>Hora pico:</strong> ${data.hora_pico.hora} (${data.hora_pico.cantidad} asistentes)`;
                document.getElementById('asistencia-por-hora-chart').appendChild(horaPicoInfo);
            }
            
            chart.container('asistencia-por-hora-chart');
            chart.draw();
        })
        .catch(error => console.error('Error cargando gráfico de asistencia:', error));
}

function cargarGraficoDepartamentos() {
    fetch('data/asistencia_por_departamento.php')
        .then(response => response.json())
        .then(data => {
            console.log('Datos departamentos:', data);
            
            // Convertir a formato AnyChart
            const chartData = data.map(item => {
                return {x: item.departamento || 'Sin departamento', value: parseInt(item.cantidad)};
            });
            
            const chart = anychart.pie(chartData);
            chart.title('Eventos por Departamento');
            chart.container('eventos-por-departamento-chart');
            chart.draw();
        })
        .catch(error => console.error('Error cargando gráfico de departamentos:', error));
}

function cargarGraficoAforoVsRegistrados(idEvento) {
    fetch(`data/eventos_data.php`)
        .then(response => response.json())
        .then(data => {
            // Filtrar solo el evento actual
            const evento = data.find(e => e.id_evento == idEvento);
            if (!evento) return;
            
            const chart = anychart.column();
            
            const seriesData = [
                { x: 'Aforo Máximo', value: evento.aforo_maximo },
                { x: 'Registrados', value: evento.aforo_maximo - evento.cupos_disponibles }
            ];
            
            chart.column(seriesData);
            chart.title('Aforo vs Registrados');
            chart.container('aforo-vs-registrados-chart');
            chart.draw();
        })
        .catch(error => console.error('Error cargando gráfico de aforo:', error));
}

function cargarGraficoEstadosEventos() {
    fetch('data/eventos_data.php')
        .then(response => response.json())
        .then(data => {
            // Contar eventos por estado
            const conteoEstados = {};
            data.forEach(evento => {
                conteoEstados[evento.estado] = (conteoEstados[evento.estado] || 0) + 1;
            });
            
            // Convertir a formato AnyChart
            const chartData = Object.keys(conteoEstados).map(estado => {
                return { x: estado, value: conteoEstados[estado] };
            });
            
            const chart = anychart.pie(chartData);
            chart.innerRadius('40%'); // Para hacerlo tipo dona
            chart.title('Estados de Eventos');
            chart.container('estados-eventos-chart');
            chart.draw();
        })
        .catch(error => console.error('Error cargando gráfico de estados:', error));
}

function cargarGraficoPreciosEventos() {
    fetch('data/eventos_data.php')
        .then(response => response.json())
        .then(data => {
            // Filtrar solo eventos pagos
            const precios = data
                .filter(evento => evento.precio_entrada > 0)
                .map(evento => evento.precio_entrada);
            
            if (precios.length === 0) return;
            
            // Crear un gráfico de columnas en lugar de histograma
            const chart = anychart.column();
            chart.title('Distribución de Precios');
            chart.container('precios-eventos-chart');
            
            // Agrupar precios en rangos
            const rangos = {};
            const maxPrecio = Math.max(...precios);
            const minPrecio = Math.min(...precios);
            const binCount = Math.min(10, Math.ceil(Math.sqrt(precios.length)));
            const binSize = Math.ceil((maxPrecio - minPrecio) / binCount);
            
            precios.forEach(precio => {
                const bin = Math.floor((precio - minPrecio) / binSize);
                const rangoInicio = minPrecio + bin * binSize;
                const rangoFin = rangoInicio + binSize;
                const rangoLabel = `${rangoInicio}-${rangoFin}`;
                
                if (!rangos[rangoLabel]) {
                    rangos[rangoLabel] = 0;
                }
                rangos[rangoLabel]++;
            });
            
            // Convertir a formato para gráfico de columnas
            const chartData = Object.keys(rangos).map(rango => {
                return { x: rango, value: rangos[rango] };
            });
            
            chart.data(chartData);
            chart.xAxis().title('Rango de Precios');
            chart.yAxis().title('Número de Eventos');
            chart.draw();
        })
        .catch(error => console.error('Error cargando gráfico de precios:', error));
}