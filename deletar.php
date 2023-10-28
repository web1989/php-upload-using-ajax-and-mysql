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

    // Use uma declaração preparada
    $excluirStmt = $conexao->prepare("SELECT file FROM imagens WHERE id = ?");
    $excluirStmt->bind_param("i", $id);
    $excluirStmt->execute();
    $excluirStmt->store_result();

    if ($excluirStmt->num_rows > 0) {
        $excluirStmt->bind_result($arquivo_nome);
        $excluirStmt->fetch();

        // Use outra declaração preparada para excluir o registro
        $deletarStmt = $conexao->prepare("DELETE FROM imagens WHERE id = ?");
        $deletarStmt->bind_param("i", $id);

        if ($deletarStmt->execute()) {
            // Exclua o arquivo físico
            $caminho_arquivo = 'uploads/' . $arquivo_nome;
            if (file_exists($caminho_arquivo)) {
                unlink($caminho_arquivo);
            }

            $deletarStmt->close();
            $excluirStmt->close();
            $conexao->close();
            return "success"; // Exclusão bem-sucedida
        } else {
            $deletarStmt->close();
            $excluirStmt->close();
            $conexao->close();
            return "Erro ao excluir o registro: " . $conexao->error; // Mensagem de erro
        }
    } else {
        $excluirStmt->close();
        $conexao->close();
        return "Imagem não encontrada."; // Mensagem de erro
    }
}
?>
