<?php
session_start();

// Remove todas as variáveis de sessão
$_SESSION = [];

// Apaga cookie de sessão (se existir)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destroi a sessão
session_destroy();

// Redireciona para o login
header('Location: pages/login.php');
exit;
?>