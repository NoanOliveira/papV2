<?php
require_once __DIR__ . '/includes/config.php';

$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';

if ($nome === '') {
    echo "<div class='alert alert-warning'>Nome da categoria inválido.</div>";
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO categorias (nome) VALUES (?)");
    if (!$stmt) {
        echo "<div class='alert alert-danger'>Erro ao preparar a inserção.</div>";
        exit;
    }

    $stmt->bind_param("s", $nome);
    $ok = $stmt->execute();
    $stmt->close();

    if ($ok) {
        echo "<div class='alert alert-success'>Categoria <strong>{$nome}</strong> adicionada com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger'>Erro ao adicionar a categoria.</div>";
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erro interno: " . htmlspecialchars($e->getMessage()) . "</div>";
}
