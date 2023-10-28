<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        // Exclusão individual
        $id = $_POST['id'];
        $resultado = excluirImagem($id);
        if ($resultado === "success") {
            echo "success";
        } else {
            echo $resultado; // Retorna a mensagem de erro
        }
    } elseif (isset($_POST['ids']) && is_array($_POST['ids'])) {
        // Exclusão em massa
        $ids = $_POST['ids'];
        $exclusaoBemSucedida = true;

        foreach ($ids as $id) {
            $resultado = excluirImagem($id);
            if ($resultado !== "success") {
                $exclusaoBemSucedida = false;
            }
        }

        if ($exclusaoBemSucedida) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "Nenhum ID fornecido para exclusão.";
    }
} else {
    echo "Método não suportado.";
}

function excluirImagem($id) {
    // Conecte ao banco de dados (configure sua conexão)
    $conexao = new mysqli("localhost", "root", "root", "imagens");

    if ($conexao->connect_error) {
        die("Conexão falhou: " . $conexao->connect_error);
    }

    $selecionar_arquivo = "SELECT file FROM imagens WHERE id = $id";
    $resultado = $conexao->query($selecionar_arquivo);

    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $arquivo_nome = $row['file'];

        $deletar = "DELETE FROM imagens WHERE id = $id";
        if ($conexao->query($deletar) === TRUE) {
            // Exclua o arquivo físico
            $caminho_arquivo = 'uploads/' . $arquivo_nome;
            if (file_exists($caminho_arquivo)) {
                unlink($caminho_arquivo);
            }

            $conexao->close();
            return "success"; // Exclusão bem-sucedida
        } else {
            return "Erro: " . $deletar . "<br>" . $conexao->error; // Mensagem de erro
        }
    } else {
        $conexao->close();
        return "Imagem não encontrada."; // Mensagem de erro
    }
}
?>
