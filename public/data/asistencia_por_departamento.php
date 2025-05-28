<?php
require_once __DIR__ .'/../../config/database.php';

header('Content-Type: application/json');

$sql = "SELECT departamento, COUNT(*) as cantidad 
        FROM eventos 
        GROUP BY departamento";
$result = $pdo->query($sql);
$data = $result->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>