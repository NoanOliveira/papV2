<?php
// Conexão com o banco de dados
require_once __DIR__ . '/includes/config.php'; // Inclua a configuração de conexão com o banco de dados

$msg = ""; // Mensagem para ser exibida após a tentativa de adicionar material

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $quantidade = $_POST['quantidade'];

    if (empty($nome) || empty($categoria) || $quantidade < 0) {
        header("Location: pages/listarMateriais.php?msg=erroDadosInvalidos");
        exit;
    }

    // Verifica se a categoria já existe
    $sql_check = "SELECT id FROM categorias WHERE nome = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $categoria);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        // Categoria não existe, insere uma nova
        $sql_add_cat = "INSERT INTO categorias (nome) VALUES (?)";
        $stmt_add_cat = $conn->prepare($sql_add_cat);
        $stmt_add_cat->bind_param("s", $categoria);
        $stmt_add_cat->execute();
        $stmt_add_cat->close();
    }

    $stmt_check->close();

    

    // Inserir material
    $sql = "INSERT INTO materiais (nome, categoria, quantidade) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nome, $categoria, $quantidade);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: pages/listarMateriais.php?msg=sucesso");
        exit;
    } else {
        $erro = urlencode($stmt->error); // protege o erro para URL
        $stmt->close();
        header("Location: pages/listarMateriais.php?msg=erro&detalhe=$erro");
        exit;
    }


}
?>