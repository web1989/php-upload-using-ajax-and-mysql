<?php
$conexao = new mysqli("localhost", "root", "root", "imagens");

if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
}

$selecionar = "SELECT * FROM imagens ORDER BY created_at DESC";
$resultado = $conexao->query($selecionar);

$imagens = array();

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $imagens[] = $row;
    }
}

$conexao->close();

echo json_encode($imagens);
?>
