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
        $formatos_permitidos = array('jpg', 'jpeg', 'png', 'svg');
        $erros_nao_permitidos = array();

        for ($i = 0; $i < $count; $i++) {
            if ($arquivos['error'][$i] === 0) {
                $arquivo_nome = $arquivos['name'][$i];
                $arquivo_extensao = pathinfo($arquivo_nome, PATHINFO_EXTENSION);

                // Verifique se a extensão do arquivo está na lista de formatos permitidos
                if (in_array(strtolower($arquivo_extensao), $formatos_permitidos)) {
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
                    // Adicione o arquivo não permitido à lista de erros
                    $erros_nao_permitidos[] = $arquivo_nome;
                }
            } else {
                $erros++;
            }
        }

        if ($sucessos > 0) {
            die("Total de imagens enviadas com sucesso: " . $sucessos);
        }

        if ($erros > 0) {
            die("Total de erros no envio de imagens: " . $erros);
        }

        if (!empty($erros_nao_permitidos)) {
            die("Arquivos não permitidos: " . implode(', ', $erros_nao_permitidos));
        }

        $conexao->close();
    } else {
        die("Nenhuma imagem enviada.");
    }
} else {
    die("Método não suportado.");
}
