<?php
function criarNotificacao($conn, $mensagem, $id_usuario = null, $id_solicitacao = null)
{
    // Se for notificação para um usuário específico
    if ($id_usuario !== null) {
        $sql = "INSERT INTO notificacoes (id_solicitacao, id_usuario, mensagem, lida, data_criacao)
                VALUES (?, ?, ?, 0, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $id_solicitacao, $id_usuario, $mensagem);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        // Caso contrário, envia para todos os funcionários/admins
        $sql = "SELECT id FROM usuarios WHERE nivel IN ('funcionario', 'admin')";
        $res = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            $uid = $row['id'];
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO notificacoes (id_solicitacao, id_usuario, mensagem, lida, data_criacao)
                 VALUES (?, ?, ?, 0, NOW())"
            );
            mysqli_stmt_bind_param($stmt, "iis", $id_solicitacao, $uid, $mensagem);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}
?>
