<?php
include 'conn.php';

$data = json_decode(file_get_contents("php://input"));

if (isset($data->username) && isset($data->password)) {
    $username = $conn->real_escape_string($data->username);
    $password = password_hash($data->password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (username, password) VALUES ('$username', '$password')";
    
    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Cadastro realizado com sucesso"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao cadastrar: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Dados inválidos"]);
}
?>