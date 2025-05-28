<?php
require_once __DIR__ .'/../config/database.php';

// Obtener lista de eventos para el selector
$sql = "SELECT id_evento, nombre FROM eventos ORDER BY nombre";
$result = $pdo->query($sql);
$eventos = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Eventos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <header class="dashboard-header">
            <h1><i class="fas fa-chart-line"></i> Dashboard de Eventos</h1>
            <div class="selector-container">
                <select id="evento" class="modern-select">
                    <option value="">Seleccione un evento</option>
                    <?php foreach ($eventos as $evento): ?>
                    <option value="<?= $evento['id_evento'] ?>"><?= htmlspecialchars($evento['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button id="verDashboard" class="modern-button">
                    <i class="fas fa-search"></i> Ver Dashboard
                </button>
            </div>
        </header>

        <main class="dashboard-grid">
            <div class="metric-card card-total">
                <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="card-content">
                    <span class="card-title">Total de Eventos</span>
                    <span class="card-value" id="total-eventos">0</span>
                </div>
            </div>

            <div class="metric-card card-active">
                <div class="card-icon"><i class="fas fa-bolt"></i></div>
                <div class="card-content">
                    <span class="card-title">Eventos Activos</span>
                    <span class="card-value" id="eventos-activos">0</span>
                </div>
            </div>

            <div class="metric-card card-attendees">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <div class="card-content">
                    <span class="card-title">Promedio de Asistentes</span>
                    <span class="card-value" id="promedio-asistentes">0</span>
                </div>
            </div>
            <div class="metric-card card-revenue">
                <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="card-content">
                    <span class="card-title">Ingresos Proyectados</span>
                    <span class="card-value" id="ingresos-proyectados">$0</span>
                </div>
            </div>
            <div class="featured-card card-popular">
                <div class="card-content">
                    <span class="card-title">Evento más Popular</span>
                    <h2 class="featured-value" id="evento-popular"></h2>
                    <div class="popular-badge">
                        <i class="fas fa-star"></i> Más asistencia
                    </div>
                </div>
            </div>

            
        </main>
    </div>

    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-bundle.min.js"></script>
    <script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-data-adapter.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>

<style>
:root {
    --primary-color:rgb(73, 238, 67);
    --secondary-color: #3f37c9;
    --accent-color: #4cc9f0;
    --success-color: #4ad66d;
    --warning-color: #f8961e;
    --danger-color: #f94144;
    --dark-color: #212529;
    --light-color: #f8f9fa;
    --text-dark: #2b2d42;
    --text-light: #8d99ae;
}

body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    background-color: #f5f7fa;
    color: var(--text-dark);
    margin: 0;
    padding: 0;
    line-height: 1.6;
    
}

.dashboard-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-header {
    margin-bottom: 30px;
    text-align: center;
}

.dashboard-header h1 {
    color: var(--primary-color);
    font-size: 2.2rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.selector-container {
    display: flex;
    gap: 15px;
    max-width: 800px;
    margin: 0 auto;
    flex-wrap: wrap;
    justify-content: center;
}

.modern-select {
    flex: 1;
    min-width: 250px;
    padding: 12px 15px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background-color: white;
    font-size: 1rem;
    color: var(--text-dark);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.modern-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
}

.modern-button {
    padding: 12px 25px;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.modern-button:hover {
    background-color: var(--secondary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.metric-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 4px solid transparent;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

.card-icon {
    font-size: 1.8rem;
    color: white;
    background: var(--primary-color);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.card-content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.card-title {
    font-size: 0.95rem;
    color: var(--text-light);
    font-weight: 500;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-dark);
}

.featured-card {
    grid-column: span 2;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 12px;
    padding: 25px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 10px 15px rgba(67, 97, 238, 0.2);
    width: 1150px;
}

.featured-card .card-title {
    color: rgba(255,255,255,0.8);
    font-size: 1rem;
}

.featured-value {
    font-size: 2rem;
    margin: 10px 0;
    font-weight: 700;
}

.popular-badge {
    background: rgba(255,255,255,0.2);
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    align-self: flex-start;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Colores específicos para cada card */
.card-total { border-left-color: var(--primary-color); }
.card-total .card-icon { background: var(--primary-color); }

.card-active { border-left-color: var(--success-color); }
.card-active .card-icon { background: var(--success-color); }

.card-attendees { border-left-color: var(--accent-color); }
.card-attendees .card-icon { background: var(--accent-color); }

.card-revenue { border-left-color: var(--warning-color); }
.card-revenue .card-icon { background: var(--warning-color); }

/* Responsive */
@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .featured-card {
        grid-column: span 1;
    }
    
    .selector-container {
        flex-direction: column;
    }
    
    .modern-select, .modern-button {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .dashboard-header h1 {
        font-size: 1.8rem;
    }
    
    .card-value {
        font-size: 1.5rem;
    }
    
    .featured-value {
        font-size: 1.6rem;
    }
}
</style>