<?php
require 'conn.php'; requireRole($pdo, ['ADMIN','GESTOR']);
$data = getJsonBody(); $id = intval($data['id_produto'] ?? 0); if(!$id){ http_response_code(422); echo json_encode(['error'=>'id_produto required']); exit; }
$fields=[]; $params=[]; foreach(['sku','nome','descricao','id_unidade','ativo'] as $f){ if(isset($data[$f])){ $fields[]="$f = ?"; $params[]=$data[$f]; } }
if(!$fields){ http_response_code(400); echo json_encode(['error'=>'no fields']); exit; } $params[]=$id; $sql='UPDATE produtos SET '.implode(',', $fields).' WHERE id_produto = ?';
try{ $stmt=$pdo->prepare($sql); $stmt->execute($params); echo json_encode(['success'=>true]); } catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error']); }
?>