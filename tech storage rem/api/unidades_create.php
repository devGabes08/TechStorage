<?php
require 'conn.php'; requireRole($pdo, ['ADMIN','GESTOR']);
$data = getJsonBody(); $sigla = trim($data['sigla'] ?? ''); $descricao = trim($data['descricao'] ?? '');
if(!$sigla || !$descricao){ http_response_code(422); echo json_encode(['error'=>'sigla and descricao required']); exit; }
$stmt = $pdo->prepare('INSERT INTO unidades_medida (sigla,descricao) VALUES (?,?)');
try{ $stmt->execute([$sigla,$descricao]); echo json_encode(['success'=>true,'id_unidade'=>$pdo->lastInsertId()]); } catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error','detail'=>$e->getMessage()]); }
?>