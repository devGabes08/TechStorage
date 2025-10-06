<?php
require 'conn.php'; requireAuth();
$stmt = $pdo->query('SELECT l.*, a.nome as armazem_nome FROM locais_armazenagem l JOIN armazens a ON l.id_armazem = a.id_armazem ORDER BY l.id_local DESC');
echo json_encode(['locais'=>$stmt->fetchAll()]);
?>