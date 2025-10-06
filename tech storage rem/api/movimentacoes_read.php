<?php
require 'conn.php'; requireAuth();
$stmt = $pdo->query('SELECT m.*, p.sku, p.nome AS produto_nome, u.nome_completo AS usuario_nome FROM movimentacoes_estoque m JOIN produtos p ON m.id_produto = p.id_produto JOIN usuarios u ON m.realizado_por = u.id_usuario ORDER BY m.id_mov DESC');
echo json_encode(['movimentacoes'=>$stmt->fetchAll()]);
?>