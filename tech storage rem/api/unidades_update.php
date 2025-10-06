<?php
require 'conn.php'; requireRole($pdo, ['ADMIN','GESTOR']);
$data = getJsonBody(); $id = intval($data['id_unidade'] ?? 0); if(!$id){ http_response_code(422); echo json_encode(['error'=>'id_unidade required']); exit; }
$fields=[]; $params=[]; if(isset($data['sigla'])){ $fields[]='sigla = ?'; $params[]=$data['sigla']; } if(isset($data['descricao'])){ $fields[]='descricao = ?'; $params[]=$data['descricao']; }
if(!$fields){ http_response_code(400); echo json_encode(['error'=>'no fields']); exit; } $params[]=$id; $sql='UPDATE unidades_medida SET '.implode(',', $fields).' WHERE id_unidade = ?';
try{ $stmt=$pdo->prepare($sql); $stmt->execute($params); echo json_encode(['success'=>true]); } catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error']); }
?>