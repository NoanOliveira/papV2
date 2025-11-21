<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("Usuário não autenticado.");
}

include_once __DIR__ . '/../includes/config.php';
$user_id = $_SESSION['usuario_id'];
$userNivel = $_SESSION['nivel'] ?? 'Usuário';

// Consulta materiais
$sql = "SELECT * FROM materiais";
$resultado = $conn->query($sql);

// Consulta nível do usuário
$stmt = $conn->prepare("SELECT nivel FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nivel);
$stmt->fetch();
$stmt->close();

$userNivel = strtolower($nivel ?: 'desconhecido');

// 🔔 Buscar notificações do usuário logado
$notifQuery = "SELECT * FROM notificacoes WHERE id_usuario = ? AND lida = 0 ORDER BY data_criacao DESC LIMIT 5";
$notifStmt = $conn->prepare($notifQuery);
$notifStmt->bind_param("i", $user_id);
$notifStmt->execute();
$resultNotif = $notifStmt->get_result();
$notifCount = $resultNotif->num_rows;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Lista de Materiais</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
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
                    <h6 class="mb-0"><?php echo htmlspecialchars($_SESSION['nome']); ?></h6>
                    <span><?php echo htmlspecialchars($userNivel); ?></span>
                </div>
            </div>
            <div class="navbar-nav w-100">
                <a href="home.php" class="nav-item nav-link"><i
                                class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                                class="fa fa-laptop me-2"></i>Pages</a>
                            <div class="dropdown-menu bg-transparent border-0">
                                <a href="login.php" class="dropdown-item">login</a>
                                <a href="register.php" class="dropdown-item">Register</a>
                                <a href="../404.html" class="dropdown-item">404 Error</a>
                                <a href="visitaEstudo.php" class="dropdown-item">papel visitaEstudo</a>
                                <a href="gerenciarSolicitacoes.php" class="dropdown-item">Solicitações</a>
                                
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
                            <?php while ($n = $resultNotif->fetch_assoc()): ?>
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
                        <span class="d-none d-lg-inline-flex"><?= htmlspecialchars($_SESSION['nome']); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                        <a href="#" class="dropdown-item">Meu Perfil</a>
                        <a href="#" class="dropdown-item">Configurações</a>
                        <a href="../logout.php" class="dropdown-item">Sair</a>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->

        <!-- Lista de Materiais -->
        <div class="container-fluid pt-4 px-4">
            <div class="bg-light rounded-top p-4">
                <h2 class="mb-4 text-center">📦 Lista de Materiais Registados</h2>

                <!-- Botões -->
                <div class="text-center mb-4">
                    <?php if ($userNivel === 'professor'): ?>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSolicitar">
                            <i class="fa fa-paper-plane me-2"></i> Solicitar Material
                        </button>
                    <?php elseif ($userNivel === 'funcionario'): ?>
                        <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalAdicionar">
                            <i class="fa fa-plus me-1"></i> Adicionar
                        </button>
                        <button class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#modalEditar">
                            <i class="fa fa-edit me-1"></i> Editar
                        </button>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalRemover">
                            <i class="fa fa-trash me-1"></i> Remover
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Tabela -->
                <?php if ($resultado->num_rows > 0): ?>
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="table-primary">
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

        <!-- Footer -->
        <div class="container-fluid pt-4 px-4">
            <div class="bg-light rounded-top p-4 text-center">
                &copy; Sistema de Materiais - Todos os direitos reservados.
            </div>
        </div>
    </div>
</div>

<!-- ===============================
MODAIS
=============================== -->

<!-- Modal: Solicitar Material -->
<div class="modal fade" id="modalSolicitar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Solicitar Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="solicitarMaterial.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="idMaterial" class="form-label">Selecione o Material</label>
                        <select class="form-select" id="idMaterial" name="idMaterial" required>
                            <option value="" selected disabled>-- Escolha um material --</option>
                            <?php
                            $query = "SELECT id, nome FROM materiais ORDER BY nome ASC";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['id']}' data-nome='" . htmlspecialchars($row['nome'], ENT_QUOTES) . "'>{$row['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nome_material" class="form-label">Nome do Material</label>
                        <input type="text" class="form-control" id="nome_material" name="nome_material" readonly required>
                    </div>

                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade do Material</label>
                        <input type="number" class="form-control" id="quantidade" name="quantidade" required>
                    </div>

                    <div class="mb-3">
                        <label for="finalidade" class="form-label">Para qual sera a finalidade</label>
                        <textarea class="form-control" id="finalidade" name="finalidade" rows="3" placeholder="Descreva a finalidade..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Enviar Solicitação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Adicionar -->
<div class="modal fade" id="modalAdicionar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Adicionar Novo Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../adicionarMaterial.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomeAdd" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nomeAdd" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantidadeAdd" class="form-label">Quantidade</label>
                        <input type="number" class="form-control" id="quantidadeAdd" name="quantidade" required>
                    </div>
                    <div class="mb-3">
                    <label for="categoriaAdd" class="form-label">Categoria</label>
                    <input list="listaCategorias" class="form-control" id="categoriaAdd" name="categoria" required>
                    <datalist id="listaCategorias">
                        <?php
                        $categorias = $conn->query("SELECT nome FROM categorias ORDER BY nome ASC");
                        while ($cat = $categorias->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($cat['nome'], ENT_QUOTES) . "'>";
                        }
                        ?>
                    </datalist>
                    <small class="form-text text-muted">Digite uma nova categoria se não existir.</small>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Editar Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarMaterial" action="../editarMaterial.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="idMaterialEdit" class="form-label">Selecione o Material</label>
                        <select class="form-select" id="idMaterialEdit" name="id" required>
                            <option value="" selected disabled>-- Escolha um material --</option>
                            <?php
                            $query = "SELECT id, nome FROM materiais ORDER BY nome ASC";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="novoNome" class="form-label">Novo Nome</label>
                        <input type="text" class="form-control" id="novoNome" name="novo_nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="novaQuantidade" class="form-label">Nova Quantidade</label>
                        <input type="number" class="form-control" id="novaQuantidade" name="nova_quantidade" required>
                    </div>
                    <div class="mb-3">
                        <label for="novaCategoria" class="form-label">Nova Categoria</label>
                        <input type="text" class="form-control" id="novaCategoria" name="nova_categoria" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Remover -->
<div class="modal fade" id="modalRemover" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Remover Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../removerMaterial.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="idRemove" class="form-label">Selecione o Material</label>
                        <select class="form-select" id="idRemove" name="id" required>
                            <option value="" selected disabled>-- Escolha um material --</option>
                            <?php
                            $query = "SELECT id, nome FROM materiais ORDER BY nome ASC";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <p class="text-danger">Tem certeza que deseja remover este material? Essa ação é irreversível.</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Remover</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    console.log('Script carregado');

    // Preenche nome automaticamente no modal de solicitação
    const selectMaterial = document.getElementById('idMaterial');
    const nomeMaterial = document.getElementById('nome_material');
    if (selectMaterial && nomeMaterial) {
        selectMaterial.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            nomeMaterial.value = selectedOption.getAttribute('data-nome') || '';
        });
    }

    // Preenche automaticamente os campos ao editar material
    const selectEdit = document.getElementById('idMaterialEdit');
    if (selectEdit) {
        selectEdit.addEventListener('change', function () {
            const id = this.value;
            if (!id) return;

            fetch('../buscar_materiais.php?id=' + encodeURIComponent(id))
                .then(res => res.json())
                .then(data => {
                    if (data && !data.erro) {
                        document.getElementById('novoNome').value = data.nome || '';
                        document.getElementById('novaQuantidade').value = data.quantidade || '';
                        document.getElementById('novaCategoria').value = data.categoria || '';
                    } else {
                        alert('Material não encontrado.');
                    }
                })
                .catch(err => {
                    alert('Erro ao buscar dados do material.');
                });
        });
    }

    // Adiciona nova categoria caso não exista
    const inputCategoria = document.getElementById('categoriaAdd');
    const formAdd = document.querySelector('#modalAdicionar form');

    if (formAdd && inputCategoria) {
        formAdd.addEventListener('submit', function (e) {
            const categoriaDigitada = inputCategoria.value.trim();
            if (!categoriaDigitada) return;

            const existeNaLista = Array.from(document.querySelectorAll('#listaCategorias option'))
                .some(opt => opt.value.toLowerCase() === categoriaDigitada.toLowerCase());

            if (!existeNaLista) {
                e.preventDefault();
                fetch('../adicionar_categoria.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'nome=' + encodeURIComponent(categoriaDigitada)
                })
                .then(() => {
                    formAdd.submit();
                })
                .catch(err => {
                    alert('Erro ao adicionar categoria: ' + err);
                });
            }
        });
    }

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

});
</script>


</body>