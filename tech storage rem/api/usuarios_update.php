<?php
require 'conn.php';
requireRole($pdo, ['ADMIN']);
$data = getJsonBody();
$id = intval($data['id_usuario'] ?? 0); if(!$id){ http_response_code(422); echo json_encode(['error'=>'id_usuario required']); exit; }
$fields=[]; $params=[];
if(isset($data['status'])){ $fields[]='status = ?'; $params[] = $data['status']; }
if(isset($data['papel'])){ $fields[]='papel = ?'; $params[] = $data['papel']; }
if(!$fields){ http_response_code(400); echo json_encode(['error'=>'no fields']); exit; }
$params[] = $id; $sql = 'UPDATE usuarios SET '.implode(',', $fields).' WHERE id_usuario = ?';
try{ $stmt=$pdo->prepare($sql); $stmt->execute($params); echo json_encode(['success'=>true]); } catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error']); }
?>