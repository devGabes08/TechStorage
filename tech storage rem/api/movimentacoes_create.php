<?php
require 'conn.php';
requireAuth();
$data = getJsonBody();
$tipo = $data['tipo'] ?? '';
$id_produto = intval($data['id_produto'] ?? 0);
$id_origem = isset($data['id_local_origem']) && $data['id_local_origem'] !== '' ? intval($data['id_local_origem']) : null;
$id_destino = isset($data['id_local_destino']) && $data['id_local_destino'] !== '' ? intval($data['id_local_destino']) : null;
$quant = floatval($data['quantidade'] ?? 0);
$obs = $data['observacao'] ?? null;
$realizado_por = $_SESSION['id_usuario'] ?? null;

if (!$tipo || !$id_produto || $quant <= 0) { http_response_code(422); echo json_encode(['error'=>'tipo,id_produto,quantidade required']); exit; }


$pdo->beginTransaction();
try {
 
    if ($tipo === 'ENTRADA') {
        if (!$id_destino) throw new Exception('id_local_destino required for ENTRADA');
      
        $stmt = $pdo->prepare('SELECT quantidade_atual FROM estoques WHERE id_produto = ? AND id_local = ? FOR UPDATE');
        $stmt->execute([$id_produto, $id_destino]);
        $row = $stmt->fetch();
        if ($row) {
            $new = $row['quantidade_atual'] + $quant;
            $stmt = $pdo->prepare('UPDATE estoques SET quantidade_atual = ? WHERE id_produto = ? AND id_local = ?');
            $stmt->execute([$new, $id_produto, $id_destino]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO estoques (id_produto, id_local, quantidade_atual) VALUES (?,?,?)');
            $stmt->execute([$id_produto, $id_destino, $quant]);
        }
    } elseif ($tipo === 'SAIDA') {
        if (!$id_origem) throw new Exception('id_local_origem required for SAIDA');
        $stmt = $pdo->prepare('SELECT quantidade_atual FROM estoques WHERE id_produto = ? AND id_local = ? FOR UPDATE');
        $stmt->execute([$id_produto, $id_origem]);
        $row = $stmt->fetch();
        if (!$row || $row['quantidade_atual'] < $quant) throw new Exception('Insufficient stock');
        $new = $row['quantidade_atual'] - $quant;
        $stmt = $pdo->prepare('UPDATE estoques SET quantidade_atual = ? WHERE id_produto = ? AND id_local = ?');
        $stmt->execute([$new, $id_produto, $id_origem]);
    } elseif ($tipo === 'TRANSFERENCIA') {
        if (!$id_origem || !$id_destino) throw new Exception('origin and destination required for TRANSFERENCIA');
     
        $stmt = $pdo->prepare('SELECT quantidade_atual FROM estoques WHERE id_produto = ? AND id_local = ? FOR UPDATE');
        $stmt->execute([$id_produto, $id_origem]);
        $row = $stmt->fetch();
        if (!$row || $row['quantidade_atual'] < $quant) throw new Exception('Insufficient stock at origin');
        $newOrig = $row['quantidade_atual'] - $quant;
        $stmt = $pdo->prepare('UPDATE estoques SET quantidade_atual = ? WHERE id_produto = ? AND id_local = ?');
        $stmt->execute([$newOrig, $id_produto, $id_origem]);
     
        $stmt = $pdo->prepare('SELECT quantidade_atual FROM estoques WHERE id_produto = ? AND id_local = ? FOR UPDATE');
        $stmt->execute([$id_produto, $id_destino]);
        $row2 = $stmt->fetch();
        if ($row2) {
            $newDest = $row2['quantidade_atual'] + $quant;
            $stmt = $pdo->prepare('UPDATE estoques SET quantidade_atual = ? WHERE id_produto = ? AND id_local = ?');
            $stmt->execute([$newDest, $id_produto, $id_destino]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO estoques (id_produto, id_local, quantidade_atual) VALUES (?,?,?)');
            $stmt->execute([$id_produto, $id_destino, $quant]);
        }
    } elseif ($tipo === 'AJUSTE') {
      
        if (!$id_destino) throw new Exception('id_local_destino required for AJUSTE');
        $stmt = $pdo->prepare('SELECT quantidade_atual FROM estoques WHERE id_produto = ? AND id_local = ? FOR UPDATE');
        $stmt->execute([$id_produto, $id_destino]);
        $row = $stmt->fetch();
        if ($row) {
            $stmt = $pdo->prepare('UPDATE estoques SET quantidade_atual = ? WHERE id_produto = ? AND id_local = ?');
            $stmt->execute([$quant, $id_produto, $id_destino]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO estoques (id_produto, id_local, quantidade_atual) VALUES (?,?,?)');
            $stmt->execute([$id_produto, $id_destino, $quant]);
        }
    } else {
        throw new Exception('Invalid tipo');
    }

   
    $stmt = $pdo->prepare('INSERT INTO movimentacoes_estoque (tipo,id_produto,id_local_origem,id_local_destino,quantidade,realizado_por,observacao) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$tipo,$id_produto,$id_origem,$id_destino,$quant,$realizado_por,$obs]);

    $pdo->commit();
    echo json_encode(['success'=>true,'id_mov'=>$pdo->lastInsertId()]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode(['error'=>'movement failed','detail'=>$e->getMessage()]);
}
?>