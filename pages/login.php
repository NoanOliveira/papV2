<?php
include '../includes/config.php'; // Conexão com a base de dados
session_start();

// Verifica se o usuário já está logado
if (isset($_SESSION['usuario_id'])) {
    // Se já estiver logado, redireciona para o dashboard
    header('Location: home.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização e validação dos dados do formulário
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    // Verifica se o email é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido!";
    } else {
        // Consulta a base de dados
        $stmt = $conn->prepare("SELECT id, nome, senha, nivel FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();

            // Verifica a senha
            if (password_verify($senha, $user['senha'])) {
                // Cria a sessão para o usuário
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nome'] = $user['nome'];
                $_SESSION['nivel'] = $user['nivel'];

                // Redireciona para o dashboard
                header('Location: home.php');
                exit;
            } else {
                $erro = "Senha incorreta!";
            }
        } else {
            $erro = "Usuário não encontrado!";
        }
    }
}
?>

<!-- Exibição da mensagem de erro, se houver -->
<?php if (!empty($erro)): ?>
    <div style="color:red;"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Bootstrap Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
</head>

<style>
    body {
        background-image: url('../img/fundo2.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
    }
</style>

<body>

    <div class="container-xxl position-relative d-flex p-0">

        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <!-- Spinner End -->

        <!-- Login Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="home.php" class="">
                                <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>Gestor</h3>
                            </a>
                            <h3>Login</h3>
                        </div>

                        <!-- Mostra mensagem de erro -->
                        <?php if (!empty($erro)): ?>
                            <div class="alert alert-danger text-center py-2">
                                <?= htmlspecialchars($erro) ?>
                            </div>
                        <?php endif; ?>

                        <!-- FORMULÁRIO DE LOGIN -->
                        <form method="POST" action="">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="floatingInput" name="email"
                                    placeholder="name@example.com" required>
                                <label for="floatingInput">Email:</label>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="floatingPassword" name="senha"
                                    placeholder="Password" required>
                                <label for="floatingPassword">Password:</label>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                    <label class="form-check-label" for="exampleCheck1">Lembrar-se</label>
                                </div>
                                <a href="#">Esqueceu a senha?</a>
                            </div>

                            <button type="submit" class="btn btn-primary py-3 w-100 mb-4">login</button>
                            <p class="text-center mb-0">Não tem conta?
                                <a href="register.php">Crie uma conta</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login End -->

    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/chart/chart.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
</body>

</html>
<?php $conn->close(); ?>