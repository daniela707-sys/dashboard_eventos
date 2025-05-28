<?php
require_once __DIR__ .'/../../config/database.php';

header('Content-Type: application/json');

$id_evento = isset($_GET['id_evento']) ? (int)$_GET['id_evento'] : 0;

// Consulta SQL para obtener registros por hora
$sql = "SELECT 
          HOUR(fecha_registro) as hora, 
          COUNT(*) as cantidad
        FROM registro_asistencia_evento 
        WHERE id_evento = ? 
        GROUP BY HOUR(fecha_registro) 
        ORDER BY hora";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_evento]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si no hay datos, devolver un array vacío
if (empty($data)) {
    echo json_encode([
        'datos' => [],
        'hora_pico' => null
    ]);
    exit;
}

// Encontrar la hora pico
$horaPico = null;
$maxAsistencia = 0;

foreach ($data as $registro) {
    if ((int)$registro['cantidad'] > $maxAsistencia) {
        $maxAsistencia = (int)$registro['cantidad'];
        $horaPico = [
            'hora' => $registro['hora'],
            'cantidad' => $registro['cantidad']
        ];
    }
}

// Formatear datos para el gráfico
$datosGrafico = [];
foreach ($data as $registro) {
    $esPico = $registro['hora'] == $horaPico['hora'];
    $datosGrafico[] = [
        'x' => sprintf("%02d:00", $registro['hora']),
        'value' => (int)$registro['cantidad'],
        'normal' => [
            'fill' => $esPico ? '#e74c3c' : '#3498db'
        ]
    ];
}

echo json_encode([
    'datos' => $datosGrafico,
    'hora_pico' => [
        'hora' => sprintf("%02d:00", $horaPico['hora']),
        'cantidad' => $horaPico['cantidad']
    ]
]);
?>