<?php
require 'conn.php';
requireRole($pdo, ['ADMIN','GESTOR']);
$data = getJsonBody();
$id = intval($data['id_armazem'] ?? 0);
if (!$id) { http_response_code(422); echo json_encode(['error'=>'id_armazem required']); exit; }
$fields = []; $params = [];
if (isset($data['nome'])){ $fields[]='nome = ?'; $params[]=$data['nome']; }
if (isset($data['codigo'])){ $fields[]='codigo = ?'; $params[]=$data['codigo']; }
if (isset($data['ativo'])){ $fields[]='ativo = ?'; $params[] = intval($data['ativo']); }
if (!$fields){ http_response_code(400); echo json_encode(['error'=>'no fields']); exit; }
$params[] = $id; $sql = 'UPDATE armazens SET '.implode(',', $fields).' WHERE id_armazem = ?';
try { $stmt = $pdo->prepare($sql); $stmt->execute($params); echo json_encode(['success'=>true]); } catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error']); }
?>