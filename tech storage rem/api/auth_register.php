<?php
require 'conn.php';
$data = getJsonBody();
$nome = trim($data['nome_completo'] ?? $data['nome'] ?? '');
$email = trim($data['email'] ?? '');
$senha = $data['password'] ?? $data['senha'] ?? '';
$papel = strtoupper(trim($data['papel'] ?? 'FUNCIONARIO'));
if (!$nome || !$email || !$senha) { http_response_code(422); echo json_encode(['error'=>'nome,email,password required']); exit; }
// check existing
$stmt = $pdo->prepare('SELECT id_usuario FROM usuarios WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) { http_response_code(409); echo json_encode(['error'=>'email exists']); exit; }
// create salt + hash
$salt = bin2hex(random_bytes(16));
$hash = password_hash($senha, PASSWORD_DEFAULT);
// insert (if papel provided and session user is admin, allow custom papel)
$allowed_papel = ['ADMIN','GESTOR','FUNCIONARIO'];
if (!in_array($papel, $allowed_papel)) $papel = 'FUNCIONARIO';
// if not logged or not admin, force FUNCIONARIO
if (!isset($_SESSION['id_usuario'])) $papel = 'FUNCIONARIO';
else { $cur = getCurrentUser($pdo); if (!$cur || $cur['papel'] !== 'ADMIN') $papel = 'FUNCIONARIO'; }
$stmt = $pdo->prepare('INSERT INTO usuarios (nome_completo, email, senha_hash, salt, papel) VALUES (?, ?, ?, ?, ?)');
try { $stmt->execute([$nome, $email, $hash, $salt, $papel]); echo json_encode(['success'=>true,'id_usuario'=>$pdo->lastInsertId()]); }
catch (PDOException $e) { http_response_code(500); echo json_encode(['error'=>'db error','detail'=>$e->getMessage()]); }
?>