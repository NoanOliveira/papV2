<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/funcaoEditarMaterial.php';

header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nome = $_POST['novo_nome'] ?? null;
    $categoria = $_POST['nova_categoria'] ?? null;
    $quantidade = $_POST['nova_quantidade'] ?? null;

    $resultado = atualizarMaterial($id, $nome, $categoria, $quantidade);

    // Mostra alerta e redireciona, igual ao removerMaterial.php
    echo "<script>
        alert('" . addslashes($resultado['mensagem']) . "');
        window.location.href = 'pages/listarMateriais.php';
    </script>";
    exit;
}

echo "<script>
    alert('❌ Requisição inválida.');
    window.location.href = 'pages/listarMateriais.php';
</script>";
exit;
