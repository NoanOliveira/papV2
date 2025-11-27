<?php
include 'includes/config.php';

$sql = "SELECT * FROM materiais";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Bootstrap Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


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


            <!-- Blank Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <h2 class="mb-4 text-center">📦 Materiais Cadastrados</h2>

                    <?php if ($resultado->num_rows > 0): ?>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Quantidade</th>
                                    <th>Categoria</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($linha = $resultado->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($linha['nome']); ?></td>
                                        <td><?php echo $linha['quantidade']; ?></td>
                                        <td><?php echo htmlspecialchars($linha['categoria']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">Nenhum material encontrado.</div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Blank End -->


            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Your Site Name</a>, All Right Reserved.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
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