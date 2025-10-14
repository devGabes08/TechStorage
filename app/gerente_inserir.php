<?php
    require 'conexao.php'
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("INSERT INTO gerentes (nome, email, senha, criado_em) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $nome, $email, $senha);
    $stmt->execute();
    $resposta = ['ok' => true];
    echo json_encode($resposta);

?>