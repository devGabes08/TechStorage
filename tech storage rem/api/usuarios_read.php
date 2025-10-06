<?php
require 'conn.php';
requireRole($pdo, ['ADMIN','GESTOR']);
$stmt = $pdo->query('SELECT id_usuario,nome_completo,email,papel,status,criado_em FROM usuarios ORDER BY id_usuario DESC');
echo json_encode(['usuarios'=>$stmt->fetchAll()]);
?>