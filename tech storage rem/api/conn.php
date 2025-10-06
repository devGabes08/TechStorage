<?php
$host = "localhost";
$user = "root";     
$pass = "";          
$dbname = "tech_storage_leve"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>