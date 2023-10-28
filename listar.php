<?php

$conexao = new mysqli("localhost", "root", "root", "imagens");

if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
}

// Use uma declaração preparada para a consulta SELECT
$selecionarStmt = $conexao->prepare("SELECT * FROM imagens ORDER BY created_at DESC");
$selecionarStmt->execute();
$resultado = $selecionarStmt->get_result();

$imagens = array();

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $imagens[] = $row;
    }
}

$selecionarStmt->close();
$conexao->close();

echo json_encode($imagens);
?>
