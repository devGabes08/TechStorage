<?php
require 'conn.php'; requireAuth();
$stmt = $pdo->query('SELECT p.*, u.sigla AS unidade_sigla FROM produtos p JOIN unidades_medida u ON p.id_unidade = u.id_unidade ORDER BY p.id_produto DESC');
echo json_encode(['produtos'=>$stmt->fetchAll()]);
?>