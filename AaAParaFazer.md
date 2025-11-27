na pag de gerenciarSolicitaces.php mudar a tabela pra card, pra ficar melhor pros cell!!

pag de visita de destudos???





<?php
include '../conexao.php';

// Dados do formulário
$nome = $_POST['nome'];
$quantidade = $_POST['quantidade'];
$categoria = $_POST['categoria'];

// Verifica e processa upload da imagem
$imagemNome = null;

if (!empty($_FILES['imagem']['name'])) {
    $pasta = "../uploads/";
    
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $imagemNome = uniqid("img_") . "." . $extensao;

    move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . $imagemNome);
}

// Salvar no banco (salva apenas o nome do arquivo)
$sql = $conn->prepare("INSERT INTO materiais (nome_material, quantidade, categoria, imagem) VALUES (?, ?, ?, ?)");
$sql->bind_param("siss", $nome, $quantidade, $categoria, $imagemNome);

if ($sql->execute()) {
    header("Location: materiais.php?status=ok");
} else {
    echo "Erro ao salvar: " . $conn->error;
}
?>


<form action="../adicionarMaterial.php" method="POST" enctype="multipart/form-data">


 <div class="mb-3">
                    <label for="imagemAdd" class="form-label">Imagem</label>
                    <input type="file" class="form-control" id="imagemAdd" name="imagem" accept="image/*" required>
                </div>