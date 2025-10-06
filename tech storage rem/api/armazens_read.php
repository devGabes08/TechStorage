<?php
require 'conn.php';
requireAuth();
$stmt = $pdo->query('SELECT * FROM armazens ORDER BY id_armazem DESC');
echo json_encode(['armazens'=>$stmt->fetchAll()]);
?>