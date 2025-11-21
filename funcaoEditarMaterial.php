<?php
require_once __DIR__ . '/includes/config.php';

/**
 * Atualiza um material no banco de dados.
 *
 * @param int $id
 * @param string $nome
 * @param string $categoria
 * @param int $quantidade
 * @return array
 */
function atualizarMaterial($id, $nome, $categoria, $quantidade)
{
    global $conn;

    if (empty($id) || empty($nome) || empty($categoria) || !is_numeric($quantidade)) {
        return [
            'sucesso' => false,
            'mensagem' => '⚠️ Dados inválidos fornecidos.'
        ];
    }

    $sql = "UPDATE materiais SET nome = ?, categoria = ?, quantidade = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return [
            'sucesso' => false,
            'mensagem' => '❌ Erro ao preparar SQL: ' . $conn->error
        ];
    }

    $stmt->bind_param("ssii", $nome, $categoria, $quantidade, $id);
    $ok = $stmt->execute();

    if ($ok && $stmt->affected_rows > 0) {
        $mensagem = "✅ Material atualizado com sucesso!";
    } else {
        $mensagem = "⚠️ Nenhuma alteração realizada (verifique se o material existe).";
    }

    $stmt->close();
    $conn->close();

    return [
        'sucesso' => $ok,
        'mensagem' => $mensagem
    ];
}
