<?php
include '../includes/config.php';
session_start();

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $nivel = $_POST['nivel'] = "professor"; // "funcionario" ou "professor"

    if ($nome && $email && $senha && $nivel) {
        // Verifica se o e-mail já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $mensagem = '<div class="alert alert-warning text-center"> Este e-mail já está cadastrado!</div>';
        } else {
            // Criptografa a senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Insere no banco
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $email, $senhaHash, $nivel);

            if ($stmt->execute()) {
                $mensagem = '<div class="alert alert-success text-center"> Conta criada com sucesso! <a href="pages/login.php">Fazer login</a></div>';
            } else {
                $mensagem = '<div class="alert alert-danger text-center"> Erro ao criar conta. Tente novamente.</div>';
            }
        }
    } else {
        $mensagem = '<div class="alert alert-danger text-center"> Preencha todos os campos!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Gestor - Registro</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="img/favicon.ico" rel="icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<style>
    body {
        background-image: url('img/fundo2.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
    }
</style>


<body>

            <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        
        <!-- Spinner End -->

<div class="container-xxl position-relative d-flex p-0">

    <!-- Register Start -->
    <div class="container-fluid">
        <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="bg-light rounded p-4 p-sm-5 my-4 mx-3">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <a href="#" class="">
                            <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>Gestor</h3>
                        </a>
                        <h3>Register</h3>
                    </div>

                    <!-- Mostra mensagens -->
                    <?= $mensagem ?>

                    <!-- FORMULÁRIO DE REGISTRO -->
                    <form method="POST" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingText" name="nome" placeholder="nome" required>
                            <label for="floatingText">Nome completo</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" required>
                            <label for="floatingInput">exemplo@algo.com</label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" class="form-control" id="floatingPassword" name="senha" placeholder="Password" required>
                            <label for="floatingPassword">Senha</label>
                        </div>

                        <div class="form-floating mb-4">
                            <select class="form-select" id="floatingNivel" name="nivel" required>
                                <option value="" disabled selected>Selecione o nível</option>
                                <option value="funcionario">Funcionário</option>
                                <option value="professor">Professor</option>
                            </select>
                            <label for="floatingNivel">Nível de acesso</label>
                        </div>

                        <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Criar Conta</button>
                        <p class="text-center mb-0">Já tem uma conta? <a href="pages/login.php">Entrar</a></p>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!-- Register End -->
</div>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>
