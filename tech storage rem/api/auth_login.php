<?php
require 'conn.php';
$data = getJsonBody();
$email = trim($data['email'] ?? $data['username'] ?? '');
$senha = $data['password'] ?? $data['senha'] ?? '';
if (!$email || !$senha) { http_response_code(422); echo json_encode(['error'=>'email and password required']); exit; }
$stmt = $pdo->prepare('SELECT id_usuario, nome_completo, email, senha_hash, salt, papel, status FROM usuarios WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$u = $stmt->fetch();
if (!$u) { http_response_code(401); echo json_encode(['error'=>'invalid credentials']); exit; }
if ($u['status'] !== 'ATIVO') { http_response_code(403); echo json_encode(['error'=>'user inactive']); exit; }
// verify using password_verify against senha_hash (assume password_hash used)
// if salt is used separately, you can incorporate it; here we assume senha_hash was created with password_hash
if (!password_verify($senha, $u['senha_hash'])) { http_response_code(401); echo json_encode(['error'=>'invalid credentials']); exit; }
session_regenerate_id(true);
$_SESSION['id_usuario'] = $u['id_usuario'];
echo json_encode(['success'=>true, 'user'=>['id_usuario'=>$u['id_usuario'],'nome_completo'=>$u['nome_completo'],'email'=>$u['email'],'papel'=>$u['papel']]]);
?>