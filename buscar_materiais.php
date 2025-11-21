<?php
include_once __DIR__ . '/includes/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$termo = isset($_GET['termo']) ? trim($_GET['termo']) : '';

if ($id > 0) {
    // Busca por ID único
    $sql = "SELECT id, nome, quantidade, categoria FROM materiais WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data ?: ["erro" => "Material não encontrado"], JSON_UNESCAPED_UNICODE);
    $stmt->close();
    $conn->close();
    exit;
}

// Busca por termo (caso queira manter autocomplete)
if ($termo !== '') {
    $sql = "SELECT id, nome, quantidade, categoria 
            FROM materiais 
            WHERE nome LIKE ? OR categoria LIKE ? 
            ORDER BY nome ASC LIMIT 10";
    $stmt = $conn->prepare($sql);
    $like = '%' . $termo . '%';
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();

    $result = $stmt->get_result();
    $materiais = [];
    while ($row = $result->fetch_assoc()) {
        $materiais[] = $row;
    }

    echo json_encode($materiais, JSON_UNESCAPED_UNICODE);
    $stmt->close();
    $conn->close();
    exit;
}

// Nenhum parâmetro válido
echo json_encode(["erro" => "Nenhum parâmetro fornecido"]);
$conn->close();
?>
