<?php
require 'conn.php'; requireRole($pdo, ['ADMIN']);
$data = getJsonBody(); $id = intval($data['id_produto'] ?? 0); if(!$id){ http_response_code(422); echo json_encode(['error'=>'id required']); exit; }
$stmt = $pdo->prepare('DELETE FROM produtos WHERE id_produto = ?');
try{ $stmt->execute([$id]); echo json_encode(['success'=>true]); } catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error']); }
?>