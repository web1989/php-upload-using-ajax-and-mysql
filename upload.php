<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $conexao = new mysqli("localhost", "root", "root", "imagens");

        if ($conexao->connect_error) {
            die("Conexão falhou: " . $conexao->connect_error);
        }

        $arquivos = $_FILES['file'];
        $count = count($arquivos);
        $sucessos = 0;
        $erros = 0;

        for ($i = 0; $i < $count; $i++) {
            if ($arquivos['error'][$i] === 0) {
                $arquivo_nome = $arquivos['name'][$i];
                $arquivo_extensao = pathinfo($arquivo_nome, PATHINFO_EXTENSION);
                $id_unico = uniqid(); // Gere um ID único
                $novo_nome_arquivo = $id_unico . '.' . $arquivo_extensao;
                $caminho_arquivo = 'uploads/' . $novo_nome_arquivo;

                if (move_uploaded_file($arquivos['tmp_name'][$i], $caminho_arquivo)) {
                    // Use o nome original do arquivo (sem a extensão) como descrição no banco de dados
                    $nome_original_sem_extensao = pathinfo($arquivo_nome, PATHINFO_FILENAME);

                    $inserir = "INSERT INTO imagens (file, name) VALUES ('$novo_nome_arquivo', '$nome_original_sem_extensao')";
                    if ($conexao->query($inserir) === TRUE) {
                        $sucessos++;
                    } else {
                        $erros++;
                    }
                } else {
                    $erros++;
                }
            } else {
                $erros++;
            }
        }

        if ($sucessos > 0) {
            echo "Total de imagens enviadas com sucesso: " . $sucessos;
        }

        if ($erros > 0) {
            echo "Total de erros no envio de imagens: " . $erros;
        }

        $conexao->close();
    } else {
        echo "Nenhuma imagem enviada.";
    }
} else {
    echo "Método não suportado.";
}
