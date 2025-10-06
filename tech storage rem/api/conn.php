<?php
header('Content-Type: application/json; charset=utf-8');
$host = '127.0.0.1';
$db   = 'tech_storage_leve';
$user = 'root'; // change if needed
$pass = '';     // change if needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

function getJsonBody() {
    $input = file_get_contents('php://input');
    $d = json_decode($input, true);
    if ($d === null) return $_POST;
    return $d;
}

function getCurrentUser($pdo){
    if (!isset($_SESSION['id_usuario'])) return null;
    $stmt = $pdo->prepare('SELECT id_usuario, nome_completo, email, papel, status FROM usuarios WHERE id_usuario = ?');
    $stmt->execute([$_SESSION['id_usuario']]);
    return $stmt->fetch() ?: null;
}

function requireAuth(){
    if (!isset($_SESSION['id_usuario'])) { http_response_code(401); echo json_encode(['error'=>'Not authenticated']); exit; }
}

function requireRole($pdo, $roles){
    requireAuth();
    $u = getCurrentUser($pdo);
    if (!$u || !in_array($u['papel'], $roles)) { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
}
?>