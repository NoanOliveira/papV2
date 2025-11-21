<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
$id_usuario = $_SESSION['usuario_id']; 

// Verifica se veio via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega e sanitiza os dados
    $idMaterial = intval($_POST['idMaterial']);
    $nome_material = mysqli_real_escape_string($conn, $_POST['nome_material']);
    $quantidade = intval($_POST['quantidade']);
    $finalidade = mysqli_real_escape_string($conn, $_POST['finalidade']);

    // Verifica se há um usuário logado (opcional)
    $idUsuario = $_SESSION['usuario_id'] ?? null;

    if (!$idUsuario) {
    $_SESSION['msg'] = "❌ Usuário não autenticado.";
    header("Location: login.php");
    exit;
}



    if (empty($idMaterial) || empty($quantidade) || empty($finalidade)) {
        $_SESSION['msg'] = "⚠️ Preencha todos os campos obrigatórios.";
        header("Location: ../index.php");
        exit;
        }

    // Insere na tabela de solicitações (exemplo)
    $query = "INSERT INTO solicitacoes (id_material, nome_material, quantidade, finalidade, id_usuario, data_solicitacao, status)
              VALUES (?, ?, ?, ?, ?, NOW(), 'Pendente')";
    
    require_once __DIR__ . '/../includes/notificar.php';


criarNotificacao($conn, "Novo pedido de material: {$nome_material} ({$quantidade} unid.)", null);


    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isisi", $idMaterial, $nome_material, $quantidade, $finalidade, $idUsuario);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['msg'] = "✅ Solicitação enviada com sucesso!";
    } else {
        $_SESSION['msg'] = "❌ Erro ao enviar solicitação: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    // Redireciona de volta para a página principal (ou outra)
    header("Location: home.php");
    exit;
} else {
    // Acesso direto sem POST
    header("Location: listarMateriais.php");
    exit;
}
?>
