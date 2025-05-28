<?php
require_once __DIR__ .'/../config/database.php';

$id_evento = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener información básica del evento
$evento = [];
if ($id_evento > 0) {
    $sql = "SELECT * FROM eventos WHERE id_evento = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_evento]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener estadísticas generales
$sql_asistentes = "SELECT COUNT(*) as total_asistentes, 
                  SUM(asistio) as asistentes_reales 
                  FROM registro_asistencia_evento 
                  WHERE id_evento = ?";
$stmt = $pdo->prepare($sql_asistentes);
$stmt->execute([$id_evento]);
$estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Análisis - <?= htmlspecialchars($evento['nombre'] ?? 'Evento') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1><i class="fas fa-chart-pie"></i> Dashboard de Análisis</h1>
            <h2 class="event-title"><?= htmlspecialchars($evento['nombre'] ?? 'Seleccione un evento') ?></h2>
        </header>

        <?php if ($evento): ?>
        <section class="event-info-section">
            <div class="event-card">
                <div class="event-details">
                    <div class="detail-item">
                        <i class="fas fa-info-circle"></i>
                        <p><?= htmlspecialchars($evento['descripcion']) ?></p>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-calendar-alt"></i>
                        <p><?= date('d/m/Y H:i', strtotime($evento['fecha_inicio'])) ?> - <?= date('H:i', strtotime($evento['fecha_fin'])) ?></p>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p><?= htmlspecialchars($evento['ubicacion']) ?></p>
                    </div>
                </div>
                
                <div class="event-stats">
                    <div class="stat-card">
                        <div class="stat-icon capacity">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-title">Aforo máximo</span>
                            <span class="stat-value"><?= number_format($evento['aforo_maximo']) ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon registered">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-title">Registrados</span>
                            <span class="stat-value"><?= number_format($estadisticas['total_asistentes']) ?></span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon attendance">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-title">Asistencia real</span>
                            <span class="stat-value"><?= number_format($estadisticas['asistentes_reales']) ?></span>
                            <span class="stat-percentage">
                                <?= $estadisticas['total_asistentes'] > 0 ? 
                                    round(($estadisticas['asistentes_reales']/$estadisticas['total_asistentes'])*100, 1) : 0 
                                ?>%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="analytics-section">
            <div class="chart-container">
                <div class="chart-header">
                    <h3><i class="fas fa-clock"></i> Asistencia por hora</h3>
                    <div class="chart-actions">
                        <button class="chart-action-btn"><i class="fas fa-download"></i></button>
                        <button class="chart-action-btn"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="chart" id="asistencia-por-hora-chart">
                    <div class="no-data">
                        <i class="fas fa-database"></i>
                        <p>No hay datos de asistencia por hora para este evento</p>
                    </div>
                </div>
            </div>
            
            <div class="chart-grid">
                <div class="chart-container half-width">
                    <div class="chart-header">
                        <h3><i class="fas fa-map-marked-alt"></i> Eventos por Departamento</h3>
                    </div>
                    <div class="chart" id="eventos-por-departamento-chart">
                        <!-- Gráfico se insertará aquí -->
                    </div>
                </div>
                
                <div class="chart-container half-width">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-bar"></i> Aforo vs Registrados</h3>
                    </div>
                    <div class="chart" id="aforo-vs-registrados-chart">
                        <!-- Gráfico se insertará aquí -->
                    </div>
                </div>
                
                <div class="chart-container half-width">
                    <div class="chart-header">
                        <h3><i class="fas fa-tags"></i> Precios de Eventos</h3>
                    </div>
                    <div class="chart" id="precios-eventos-chart">
                        <!-- Gráfico se insertará aquí -->
                    </div>
                </div>
                
                <div class="chart-container half-width">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> Estados de Eventos</h3>
                    </div>
                    <div class="chart" id="estados-eventos-chart">
                        <!-- Gráfico se insertará aquí -->
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-bundle.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-data-adapter.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>

<style>
:root {
    --primary-color:rgb(67, 238, 67);
    --secondary-color: #3a0ca3;
    --success-color: #4cc9f0;
    --warning-color: #f8961e;
    --danger-color: #f72585;
    --dark-color: #212529;
    --light-color: #f8f9fa;
    --text-dark: #2b2d42;
    --text-light: #8d99ae;
    --border-radius: 12px;
    --box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

body {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background-color: #f8fafc;
    color: var(--text-dark);
    margin: 0;
    padding: 20px;
    line-height: 1.5;
}

.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    text-align: center;
    margin-bottom: 30px;
}

.dashboard-header h1 {
    color: var(--primary-color);
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.event-title {
    color: var(--dark-color);
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 0;
}

.event-info-section {
    margin-bottom: 30px;
}

.event-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    margin-bottom: 25px;
}

.event-details {
    padding: 25px;
    border-bottom: 1px solid #f1f3f5;
}

.detail-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 15px;
}

.detail-item i {
    color: var(--primary-color);
    font-size: 1.1rem;
    margin-top: 3px;
}

.detail-item p {
    margin: 0;
    color: var(--text-dark);
    font-size: 1rem;
}

.event-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 10px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: white;
}

.stat-icon.capacity { background: var(--primary-color); }
.stat-icon.registered { background: var(--warning-color); }
.stat-icon.attendance { background: var(--success-color); }

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-title {
    font-size: 0.85rem;
    color: var(--text-light);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 3px 0;
}

.stat-percentage {
    font-size: 0.85rem;
    color: var(--success-color);
    font-weight: 600;
}

.analytics-section {
    margin-top: 30px;
}

.chart-container {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin-bottom: 25px;
    overflow: hidden;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 25px;
    border-bottom: 1px solid #f1f3f5;
}

.chart-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-header h3 i {
    color: var(--primary-color);
}

.chart-actions {
    display: flex;
    gap: 8px;
}

.chart-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    background: white;
    color: var(--text-light);
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chart-action-btn:hover {
    background: #f8f9fa;
    color: var(--primary-color);
}

.chart {
    height: 350px;
    padding: 15px;
    position: relative;
}

.no-data {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-style: italic;
    gap: 10px;
}

.no-data i {
    font-size: 2.5rem;
    opacity: 0.5;
}

.chart-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.half-width {
    grid-column: span 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-header h1 {
        font-size: 1.8rem;
    }
    
    .event-title {
        font-size: 1.3rem;
    }
    
    .chart-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-card {
        padding: 12px;
    }
    
    .stat-value {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    body {
        padding: 15px;
    }
    
    .event-stats {
        grid-template-columns: 1fr;
    }
    
    .chart {
        height: 280px;
    }
}
</style>