<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if (empty($q)) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT nome FROM categorias WHERE nome LIKE '%$q%' LIMIT 10";
$result = $conn->query($sql);

$categorias = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row['nome'];
    }
}

echo json_encode($categorias);
$conn->close();
?>