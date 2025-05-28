<?php
require_once __DIR__ .'/../../config/database.php';

header('Content-Type: application/json');

$sql = "SELECT id_evento, nombre, descripcion, ubicacion, aforo_maximo, 
        cupos_disponibles, estado, precio_entrada FROM eventos";
$result = $pdo->query($sql);
$data = $result->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>