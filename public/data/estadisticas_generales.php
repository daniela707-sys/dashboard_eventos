<?php
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Obtener total de eventos
$sql_total = "SELECT COUNT(*) as total FROM eventos";
$result_total = $pdo->query($sql_total);
$total_eventos = $result_total->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener eventos activos
$sql_activos = "SELECT COUNT(*) as activos FROM eventos WHERE estado = 'Activo'";
$result_activos = $pdo->query($sql_activos);
$eventos_activos = $result_activos->fetch(PDO::FETCH_ASSOC)['activos'];

// Obtener promedio de asistentes REAL
$sql_promedio = "SELECT AVG(conteo) as promedio FROM (
    SELECT COUNT(*) as conteo FROM registro_asistencia_evento 
    WHERE asistio = 1 GROUP BY id_evento
) as subquery";
$promedio_asistentes = round($pdo->query($sql_promedio)->fetch()['promedio']);

// Obtener evento más popular REAL
$sql_popular = "SELECT e.nombre FROM eventos e
    LEFT JOIN registro_asistencia_evento r ON e.id_evento = r.id_evento AND r.asistio = 1
    GROUP BY e.id_evento ORDER BY COUNT(r.id_registro) DESC LIMIT 1";
$evento_popular = $pdo->query($sql_popular)->fetch()['nombre'];

// Calcular ingresos proyectados REALES
$sql_ingresos = "SELECT SUM(r.asistentes * e.precio_entrada) as ingresos FROM (
    SELECT id_evento, COUNT(*) as asistentes FROM registro_asistencia_evento 
    WHERE asistio = 1 GROUP BY id_evento
) r JOIN eventos e ON r.id_evento = e.id_evento";
$ingresos_proyectados = $pdo->query($sql_ingresos)->fetch()['ingresos'];

// Preparar respuesta
$response = [
    'total_eventos' => $total_eventos,
    'eventos_activos' => $eventos_activos,
    'promedio_asistentes' => $promedio_asistentes,
    'evento_popular' => $evento_popular,
    'ingresos_proyectados' => floatval($ingresos_proyectados)
];

echo json_encode($response);
?>
