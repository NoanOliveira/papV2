<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não autenticado.");
}

include_once __DIR__ . '/includes/config.php';

$user_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("UPDATE notificacoes SET lida = 1 WHERE id_usuario = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

echo "ok";
?>