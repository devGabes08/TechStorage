<?php
require 'conn.php';
$u = getCurrentUser($pdo);
echo json_encode(['user'=>$u]);
?>