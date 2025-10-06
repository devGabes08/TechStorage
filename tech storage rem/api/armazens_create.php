<?php
require 'conn.php';
requireRole($pdo, ['ADMIN','GESTOR']);
$data = getJsonBody();
$nome = trim($data['nome'] ?? '');
$codigo = trim($data['codigo'] ?? '');
if (!$nome || !$codigo) { http_response_code(422); echo json_encode(['error'=>'nome and codigo required']); exit; }
$stmt = $pdo->prepare('INSERT INTO armazens (nome,codigo) VALUES (?,?)');
try { $stmt->execute([$nome,$codigo]); echo json_encode(['success'=>true,'id_armazem'=>$pdo->lastInsertId()]); }
catch(PDOException $e){ http_response_code(500); echo json_encode(['error'=>'db error','detail'=>$e->getMessage()]); }
?>