    <?php
// salvar_visita.php
session_start();

// --- CONFIGURE AQUI suas credenciais MySQL ---
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'gestao_db';

// Conexão segura
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Erro de conexão ao banco: ' . $conn->connect_error];
    header('Location: visita_estudo.php');
    exit;
}

// Função simples de sanitização (já vamos usar prepared statements)
function get_post($name) {
    return isset($_POST[$name]) ? trim($_POST[$name]) : null;
}

// Recebe campos
$curso = get_post('curso');
$ciclo = get_post('ciclo');
$ano_curricular = get_post('ano_curricular');
$num_formandos = (int)get_post('num_formandos');
$num_formadores = (int)get_post('num_formadores');
$total = (int)get_post('total');

$area = get_post('area');
$local_visita = get_post('local_visita');
$localidade = get_post('localidade');
$data_realizacao = get_post('data_realizacao');

$justificacao = get_post('justificacao');
$objetivos = get_post('objetivos');

$ano_despacho = get_post('ano_despacho');
$assinatura = get_post('assinatura');

// Validação mínima
if (empty($curso)) {
    $_SESSION['flash'] = ['type' => 'warning', 'msg' => 'O campo "Curso" é obrigatório.'];
    header('Location: visita_estudo.php');
    exit;
}

// Inserção com prepared statement
$sql = "INSERT INTO visitas_estudo
    (curso, ciclo, ano_curricular, num_formandos, num_formadores, total,
     area, local_visita, localidade, data_realizacao,
     justificacao, objetivos, ano_despacho, assinatura, criado_em)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Erro ao preparar query: ' . $conn->error];
    header('Location: visita_estudo.php');
    exit;
}

$stmt->bind_param(
    'ssiiiissssssss',
    $curso, $ciclo, $ano_curricular,
    $num_formandos, $num_formadores, $total,
    $area, $local_visita, $localidade, $data_realizacao,
    $justificacao, $objetivos, $ano_despacho, $assinatura
);

if ($stmt->execute()) {
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Formulário salvo com sucesso.'];
} else {
    $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Erro ao salvar: ' . $stmt->error];
}

$stmt->close();
$conn->close();

header('Location: visita_estudo.php');
exit;
