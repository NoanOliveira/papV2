<?php
include 'includes/config.php';

header('Content-Type: text/html; charset=utf-8');

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);

    // Prepara a query
    $stmt = $conn->prepare("DELETE FROM materiais WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('✅ Material removido com sucesso!');
                window.location.href = 'pages/listarMateriais.php';
              </script>";
    } else {
        echo "<script>
                alert('❌ Erro ao remover o material.');
                window.location.href = 'pages/listarMateriais.php';
              </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>
            alert('⚠️ Nenhum material selecionado.');
            window.location.href = 'pages/listarMateriais.php';
          </script>";
}
?>
