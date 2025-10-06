<?php
require 'conn.php';
requireRole($pdo, ['ADMIN','GESTOR']);
$data = getJsonBody();
$id_armazem = intval($data['id_armazem'] ?? 0);
$codigo_local = trim($data['codigo_local'] ?? '');
$tipo = $data['tipo'] ?? 'OUTRO';
if(!$id_armazem || !$codigo_local){ http_response_code(422); echo json_encode(['error'=>'id_armazem and codigo_local required']); exit; }
$stmt = $pdo->prepare('INSERT INTO locais_armazenagem (id_armazem,codigo_local,tipo) VALUES (?,?,?)');
try{ $stmt->execute([$id_armazem,$codigo_local,$tipo]); echo json_encode(['success'=>true,'id_local'=>$pdo->lastInsertId()]); } catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error','detail'=>$e->getMessage()]); }
?>