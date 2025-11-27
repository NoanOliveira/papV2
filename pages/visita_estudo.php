<?php
// visita_estudo.php
session_start();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Formulário - Visita de Estudo</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card { max-width: 1100px; margin: 0 auto; }
    .logo { max-height: 80px; object-fit: contain; }
    label.required::after { content: " *"; color: #d00; }
  </style>
</head>
<body class="bg-light py-4">
<div class="container">
  <div class="card shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="mb-0">Formulário de Visita de Estudo</h3>
        <small class="text-muted">Preencha os campos abaixo e clique em salvar.</small>
      </div>
      <!-- USANDO A IMAGEM ENVIADA: /mnt/data/fotoExemplo.jpg -->
      <div><img src="/mnt/data/fotoExemplo.jpg" alt="logo" class="logo"></div>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>"><?php echo htmlspecialchars($flash['msg']); ?></div>
    <?php endif; ?>

    <form action="salvar_visita.php" method="post" class="row g-3">
      <!-- 1. Participantes -->
      <div class="col-12">
        <h5>1. Participantes</h5>
      </div>

      <div class="col-md-8">
        <label class="form-label required">Curso Profissional Técnico de</label>
        <input type="text" name="curso" class="form-control" required maxlength="255">
      </div>

      <div class="col-md-4">
        <label class="form-label">Ciclo de Formação</label>
        <input type="text" name="ciclo" class="form-control" maxlength="100">
      </div>

      <div class="col-md-4">
        <label class="form-label">Ano Curricular</label>
        <input type="text" name="ano_curricular" class="form-control" maxlength="20">
      </div>

      <div class="col-md-4">
        <label class="form-label">Nº de Formandos</label>
        <input type="number" name="num_formandos" class="form-control" min="0">
      </div>

      <div class="col-md-4">
        <label class="form-label">Nº de Formadores (Acompanhantes)</label>
        <input type="number" name="num_formadores" class="form-control" min="0">
      </div>

      <div class="col-md-4">
        <label class="form-label">Total</label>
        <input type="number" name="total" class="form-control" min="0">
      </div>

      <hr class="my-3">

      <!-- 2. Âmbito -->
      <div class="col-12"><h5>2. Âmbito</h5></div>

      <div class="col-md-6">
        <label class="form-label">Área/Disciplina</label>
        <input type="text" name="area" class="form-control" maxlength="255">
      </div>

      <div class="col-md-6">
        <label class="form-label">Instituição / Local a visitar</label>
        <input type="text" name="local_visita" class="form-control" maxlength="255">
      </div>

      <div class="col-md-6">
        <label class="form-label">Localidade</label>
        <input type="text" name="localidade" class="form-control" maxlength="255">
      </div>

      <div class="col-md-6">
        <label class="form-label">Data / Período de realização</label>
        <input type="text" name="data_realizacao" class="form-control" placeholder="ex: 2025-11-28 ou 2025-11-28 a 2025-11-29">
      </div>

      <hr class="my-3">

      <!-- 3. Justificação -->
      <div class="col-12"><h5>3. Justificação</h5></div>
      <div class="col-12">
        <textarea name="justificacao" rows="4" class="form-control" maxlength="4000"></textarea>
      </div>

      <hr class="my-3">

      <!-- 4. Objectivos -->
      <div class="col-12"><h5>4. Objectivos</h5></div>
      <div class="col-12">
        <textarea name="objetivos" rows="6" class="form-control" maxlength="4000"></textarea>
      </div>

      <hr class="my-3">

      <!-- 5. Despacho -->
      <div class="col-12"><h5>5. Despacho da Direção Técnico-Pedagógica</h5></div>

      <div class="col-md-3">
        <label class="form-label">Ano</label>
        <input type="text" name="ano_despacho" class="form-control" maxlength="10">
      </div>

      <div class="col-md-6">
        <label class="form-label">Assinatura</label>
        <input type="text" name="assinatura" class="form-control" maxlength="255">
      </div>

      <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary w-100">Salvar formulário</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>
