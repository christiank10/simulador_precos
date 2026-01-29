<?php
require_once 'index.php'; // Vamos apenas aproveitar a conexão que você já fez lá

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ean = $_POST['ean'];
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];

    try {
        // SQL para inserir o novo produto
        $sql = "INSERT INTO produtos (ean, nome, preco_tabela, categoria) VALUES (:ean, :nome, :preco, :categoria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'ean' => $ean,
            'nome' => $nome,
            'preco' => $preco,
            'categoria' => $categoria
        ]);
        $mensagem = "✅ Produto cadastrado com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "❌ Erro ao cadastrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produtos - Farmácia</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; display: flex; flex-direction: column; align-items: center; padding: 50px; }
        .caixa { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        input, select, button { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { background: #1976d2; color: white; border: none; cursor: pointer; font-weight: bold; }
        .voltar { margin-top: 20px; text-decoration: none; color: #666; }
    </style>
</head>
<body>

<div class="caixa">
    <h2>Novo Produto</h2>
    
    <?php if($mensagem) echo "<p>$mensagem</p>"; ?>

    <form method="POST">
        <input type="text" name="ean" placeholder="Código EAN (Ex: 789...)" required>
        <input type="text" name="nome" placeholder="Nome do Medicamento" required>
        <input type="number" step="0.01" name="preco" placeholder="Preço de Tabela" required>
        <select name="categoria">
            <option value="Generico">Genérico</option>
            <option value="Dermocosmetico">Dermocosmético</option>
            <option value="Referencia">Referência</option>
        </select>
        <button type="submit">SALVAR PRODUTO</button>
    </form>
    
    <a href="index.php" class="voltar">← Voltar para Vendas</a>
</div>

</body>
</html>