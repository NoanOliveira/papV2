<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não autenticado.");
}

include_once __DIR__ . '/../includes/config.php';
$user_id = $_SESSION['usuario_id'];
$userNivel = htmlspecialchars($_SESSION['nivel'] ?? 'Usuário');

// --- ⚙️ Atualizar status se houver ação ---
if (isset($_POST['acao'], $_POST['id'])) {
    $id = intval($_POST['id']);
    $acao = $_POST['acao'] === 'aceitar' ? 'Aprovada' : 'Recusada';

    // Atualiza status da solicitação
    $query = "UPDATE solicitacoes SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $acao, $id);

    if (mysqli_stmt_execute($stmt)) {

    // 🔹 Buscar material e quantidade solicitada
    $solQuery = "SELECT id_material, quantidade FROM solicitacoes WHERE id = ?";
    $solStmt = mysqli_prepare($conn, $solQuery);
    mysqli_stmt_bind_param($solStmt, "i", $id);
    mysqli_stmt_execute($solStmt);
    mysqli_stmt_bind_result($solStmt, $id_material, $quantidadeSolicitada);
    mysqli_stmt_fetch($solStmt);
    mysqli_stmt_close($solStmt);

    // 🔹 Atualiza estoque somente se aprovado
    if ($acao === 'Aprovada') {
        $updateEstoque = "UPDATE materiais SET quantidade = quantidade - ? WHERE id = ?";
        $estStmt = mysqli_prepare($conn, $updateEstoque);
        mysqli_stmt_bind_param($estStmt, "ii", $quantidadeSolicitada, $id_material);
        mysqli_stmt_execute($estStmt);
        mysqli_stmt_close($estStmt);
    }

    // 🔹 Buscar o ID do usuário que fez a solicitação
    $userQuery = "SELECT id_usuario FROM solicitacoes WHERE id = ?";
    $userStmt = mysqli_prepare($conn, $userQuery);
    mysqli_stmt_bind_param($userStmt, "i", $id);
    mysqli_stmt_execute($userStmt);
    mysqli_stmt_bind_result($userStmt, $id_usuario_solicitante);
    mysqli_stmt_fetch($userStmt);
    mysqli_stmt_close($userStmt);

    // 🔹 Criar notificação
    if ($id_usuario_solicitante) {
        $mensagem = ($acao === 'Aprovada')
            ? "✅ Sua solicitação #$id foi aprovada."
            : "❌ Sua solicitação #$id foi recusada.";

        $notifQuery = "INSERT INTO notificacoes (id_solicitacao, id_usuario, mensagem)
                       VALUES (?, ?, ?)";
        $notifStmt = mysqli_prepare($conn, $notifQuery);
        mysqli_stmt_bind_param($notifStmt, "iis", $id, $id_usuario_solicitante, $mensagem);
        mysqli_stmt_execute($notifStmt);
        mysqli_stmt_close($notifStmt);
    }

    $_SESSION['msg'] = "✅ Solicitação #$id marcada como $acao.";

} else {
    $_SESSION['msg'] = "❌ Erro ao atualizar a solicitação.";
}


    mysqli_stmt_close($stmt);
    header("Location: gerenciarSolicitacoes.php");
    exit;
}

// --- 📦 Buscar solicitações ---
$query = "SELECT s.id, s.nome_material, s.quantidade, s.finalidade, s.data_solicitacao, s.status, u.nome AS usuario
          FROM solicitacoes s
          LEFT JOIN usuarios u ON s.id_usuario = u.id
          ORDER BY s.data_solicitacao DESC";
$result = mysqli_query($conn, $query);

// --- 🔔 Buscar notificações do usuário logado ---
$notifQuery = "SELECT * FROM notificacoes WHERE id_usuario = ? AND lida = 0 ORDER BY data_criacao DESC LIMIT 5";

$notifStmt = mysqli_prepare($conn, $notifQuery);
mysqli_stmt_bind_param($notifStmt, "i", $user_id);
mysqli_stmt_execute($notifStmt);
$resultNotif = mysqli_stmt_get_result($notifStmt);
$notifCount = mysqli_num_rows($resultNotif);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Solicitações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-xxl position-relative bg-white d-flex p-0">

    <!-- Sidebar -->
    <div class="sidebar pe-4 pb-3">
        <nav class="navbar bg-light navbar-light">
            <a href="home.php" class="navbar-brand mx-4 mb-3">
                <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
            </a>
            <div class="d-flex align-items-center ms-4 mb-4">
                <div class="position-relative">
                    <img class="rounded-circle" src="../img/user.jpg" alt="" style="width: 40px; height: 40px;">
                </div>
                <div class="ms-3">
                    <h6 class="mb-0"><?= htmlspecialchars($_SESSION['nome']) ?></h6>
                    <span><?= $userNivel ?></span>
                </div>
            </div>
            <div class="navbar-nav w-100">
                <a href="home.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-laptop me-2"></i>Pages
                    </a>
                    <div class="dropdown-menu bg-transparent border-0">
                        <a href="login.php" class="dropdown-item">Login</a>
                        <a href="register.php" class="dropdown-item">Register</a>
                        <a href="../404.html" class="dropdown-item">404 Error</a>
                        <a href="visitaEstudo.php" class="dropdown-item">Visita de Estudo</a>
                        <a href="listarMateriais.php" class="dropdown-item">Lista Materiais</a>
                        <a href="gerenciarSolicitacoes.php" class="dropdown-item">Solicitações</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <!-- Content -->
    <div class="content">

        <!-- Navbar -->
        <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
            <a href="#" class="sidebar-toggler flex-shrink-0"><i class="fa fa-bars"></i></a>
            <div class="navbar-nav align-items-center ms-auto">

                <!-- 🔔 Notificações -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle position-relative" data-bs-toggle="dropdown">
                        <i class="fa fa-bell me-lg-2"></i>
                        <?php if ($notifCount > 0): ?>
                            <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                                <?= $notifCount ?>
                            </span>
                        <?php endif; ?>
                        <span class="d-none d-lg-inline-flex">Notificações</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0" style="width: 300px;">
                        <?php if ($notifCount > 0): ?>
                            <?php while ($n = mysqli_fetch_assoc($resultNotif)): ?>
                                <a href="#" class="dropdown-item">
                                    <h6 class="fw-normal mb-0"><?= htmlspecialchars($n['mensagem']) ?></h6>
                                    <small><?= date('d/m/Y H:i', strtotime($n['data_criacao'])) ?></small>
                                </a>
                                <hr class="dropdown-divider">
                            <?php endwhile; ?>
                            <a href="todasNotificacoes.php" class="dropdown-item text-center">Ver todas</a>
                        <?php else: ?>
                            <a href="#" class="dropdown-item text-center text-muted">Nenhuma notificação</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 👤 Perfil -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <img class="rounded-circle me-lg-2" src="../img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <span class="d-none d-lg-inline-flex"><?= htmlspecialchars($_SESSION['nome']) ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                        <a href="#" class="dropdown-item">Meu Perfil</a>
                        <a href="#" class="dropdown-item">Configurações</a>
                        <a href="../logout.php" class="dropdown-item">Sair</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Conteúdo principal -->
        <div class="container py-4">
            <h2 class="mb-4 text-center">📦 Solicitações de Materiais</h2>

            <?php
            if (isset($_SESSION['msg'])) {
                echo "<div class='alert alert-info text-center'>{$_SESSION['msg']}</div>";
                unset($_SESSION['msg']);
            }
            ?>

            <div class="card shadow">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="col">
            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($row['nome_material']) ?></h5>

                    <p class="mb-1"><strong>Quantidade:</strong> <?= $row['quantidade'] ?></p>
                    <p class="mb-1"><strong>Finalidade:</strong> <?= htmlspecialchars($row['finalidade']) ?></p>
                    <p class="mb-1"><strong>Solicitado por:</strong> <?= htmlspecialchars($row['usuario'] ?? 'Desconhecido') ?></p>
                    <p class="mb-1"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($row['data_solicitacao'])) ?></p>

                    <div class="mt-3">

    <?php if ($row['status'] === 'Pendente') : ?>

        <!-- Botão que abre o modal -->
        <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#modalAcao<?= $row['id'] ?>">
            ⚠️ Ação necessária
        </button>

        <!-- Modal -->
        <div class="modal fade" id="modalAcao<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Gerir Solicitação #<?= $row['id'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>O que deseja fazer com esta solicitação?</p>
                    </div>

                    <div class="modal-footer">
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button name="acao" value="aceitar" class="btn btn-success">
                                ✅ Aprovar
                            </button>
                        </form>

                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button name="acao" value="recusar" class="btn btn-danger">
                                ❌ Recusar
                            </button>
                        </form>

                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>

                </div>
            </div>
        </div>

    <?php else : ?>

        <?php
        $statusClass = match ($row['status']) {
            'Aprovada' => 'badge bg-success',
            'Recusada' => 'badge bg-danger',
            default => 'badge bg-warning text-dark',
        };
        ?>
        <span class="<?= $statusClass ?> px-3 py-2 w-100 d-block text-center">
            <?= $row['status'] ?>
        </span>

    <?php endif; ?>

</div>

                </div>

            </div> 
        </div>
    <?php endwhile; ?>
</div>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 🔔 ZERAR NOTIFICAÇÕES QUANDO CLICAR NO SINO
    const bellLink = document.querySelector('.nav-link .fa-bell')?.parentElement;

    if (bellLink) {
        bellLink.addEventListener('click', function () {
            console.log("Clique no sino detectado!");
            
            fetch('../zerarNotificacoes.php')
                .then(() => {
                    const badge = document.querySelector('.badge.bg-danger');
                    if (badge) badge.remove();
                });
        });
    } else {
        console.error("Não encontrei o sino no DOM");
    }
</script>
</body>
</html>
