<?php
// Iniciar a sessão para acessar dados do usuário
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    die("Usuário não autenticado.");
}

// Incluir arquivo de configuração para conectar ao banco de dados
include 'includes/config.php';

// Recuperar o ID do usuário da sessão (supondo que tenha sido armazenado ao fazer login)
$user_id = $_SESSION['user_id'];

// Preparar a consulta para pegar o nível do usuário
$sql = "SELECT nivel FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);

// Verificar se a consulta foi preparada corretamente
if ($stmt === false) {
    die('Erro na preparação da consulta: ' . $conn->error);
}

// Vincular o parâmetro (user_id) à consulta e executar
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Obter o resultado
$stmt->bind_result($nivel);

// Verificar se o usuário foi encontrado e se tem um nível
if ($stmt->fetch()) {
    // Enviar o resultado para o console do navegador
    echo "<script>console.log('Nivel do usuário: " . $nivel . "');</script>";
} else {
    // Caso o usuário não exista ou não tenha um nível atribuído
    echo "<script>console.log('Usuário não encontrado ou sem nível definido.');</script>";
}

// Fechar a consulta e a conexão com o banco de dados
$stmt->close();
$conn->close();
?>