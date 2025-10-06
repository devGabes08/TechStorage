<?php
require 'conn.php'; requireRole($pdo, ['ADMIN','GESTOR']);
$data = getJsonBody(); $sku = trim($data['sku'] ?? ''); $nome = trim($data['nome'] ?? ''); $id_unidade = intval($data['id_unidade'] ?? 0);
if(!$sku || !$nome || !$id_unidade){ http_response_code(422); echo json_encode(['error'=>'sku,nome,id_unidade required']); exit; }
$stmt = $pdo->prepare('INSERT INTO produtos (sku,nome,descricao,id_unidade) VALUES (?,?,?,?)');
try{ $stmt->execute([$sku,$nome,$data['descricao'] ?? null,$id_unidade]); echo json_encode(['success'=>true,'id_produto'=>$pdo->lastInsertId()]); } catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error','detail'=>$e->getMessage()]); }
?>