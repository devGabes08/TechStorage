<?php
require 'conn.php'; requireAuth();
$stmt = $pdo->query('SELECT * FROM unidades_medida ORDER BY id_unidade DESC');
echo json_encode(['unidades'=>$stmt->fetchAll()]);
?>