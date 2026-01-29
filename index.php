<?php
/**
 * PDV FARMÁCIA - VERSÃO COMPLETA COM GERENCIAMENTO DE ESTOQUE
 */

// 1. CONFIGURAÇÕES DE CONEXÃO
$host = 'localhost';
$db   = 'farmacia_db';
$user = 'postgres';
$pass = '1234'; // <--- Verifique se sua senha ainda é esta

try {
    // 2. CRIA A CONEXÃO (Fundamental estar no topo)
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. LÓGICA DE EXCLUSÃO (Agora o $pdo já existe!)
    if (isset($_GET['excluir'])) {
        $ean_para_remover = $_GET['excluir'];
        $sql_delete = "DELETE FROM produtos WHERE ean = :ean";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->execute(['ean' => $ean_para_remover]);
        
        // Redireciona para limpar a URL e atualizar a lista
        header("Location: index.php"); 
        exit;
    }

    // 4. LÓGICA DE CADASTRO
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_cadastrar'])) {
        $sql_ins = "INSERT INTO produtos (ean, nome, preco_tabela, categoria) VALUES (:ean, :nome, :preco, :cat)";
        $stmt_ins = $pdo->prepare($sql_ins);
        $stmt_ins->execute([
            'ean'   => $_POST['ean'],
            'nome'  => $_POST['nome'],
            'preco' => $_POST['preco'],
            'cat'   => $_POST['categoria']
        ]);
        header("Location: index.php");
        exit;
    }

    // 5. BUSCA DE PRODUTOS PARA AS LISTAS
    $stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
    $produtos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro crítico de conexão: " . $e->getMessage());
}

// 6. LÓGICA DE PROCESSAMENTO DE VENDA
$resultado = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_venda'])) {
    $ean_selecionado = $_POST['produto_ean'];
    $fidelidade = isset($_POST['fidelidade']);

    foreach ($produtos_db as $p) {
        if ($p['ean'] == $ean_selecionado) {
            $desconto = ($fidelidade) ? (($p['categoria'] == "Generico") ? 0.50 : 0.15) : 0;
            $precoFinal = $p['preco_tabela'] * (1 - $desconto);
            $resultado = [
                "nome" => $p['nome'],
                "original" => $p['preco_tabela'],
                "final" => $precoFinal,
                "economizou" => $p['preco_tabela'] - $precoFinal
            ];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema Farmácia 2.0</title>
    <style>
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        background: #fdfdfd; 
        color: #333;
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        padding: 20px; 
    }
    
    .container { 
        display: flex; 
        gap: 25px; 
        flex-wrap: wrap; 
        justify-content: center; 
        width: 100%; 
        max-width: 1100px; 
    }

    /* Cores da Drogaria São Paulo: Azul Marinho e Detalhes Vermelhos */
    .caixa { 
        background: white; 
        padding: 25px; 
        border-radius: 4px; /* Bordas mais quadradas para um ar sério */
        box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
        width: 420px; 
        border-top: 5px solid #003366; /* Azul Marinho */
    }

    .estoque { 
        width: 100%; 
        max-width: 865px; 
        border-top-color: #e30613; /* Vermelho destaque */
    }

    h2, h3 { 
        color: #003366; 
        text-transform: uppercase;
        font-size: 1.2rem;
        margin-bottom: 20px;
        letter-spacing: 1px;
    }

    label { font-weight: 600; font-size: 0.9rem; color: #555; }

    input, select { 
        width: 100%; 
        padding: 12px; 
        margin: 8px 0 18px 0; 
        border: 1px solid #ccc; 
        border-radius: 4px;
        background: #fafafa;
    }

    button { 
        cursor: pointer; 
        font-weight: bold; 
        border: none; 
        padding: 15px;
        border-radius: 4px;
        transition: 0.2s;
        text-transform: uppercase;
    }

    /* Botões seguindo a paleta */
    .btn-venda { background: #e30613; color: white; }
    .btn-venda:hover { background: #b3050f; }

    .btn-cadastrar { background: #003366; color: white; }
    .btn-cadastrar:hover { background: #002244; }

    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { 
        background: #003366; 
        color: white; 
        padding: 12px; 
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.95rem; }
    
    .excluir { 
        color: #e30613; 
        text-decoration: none; 
        font-size: 11px; 
        font-weight: bold; 
        border: 1px solid #e30613;
        padding: 4px 8px;
        border-radius: 3px;
    }
    .excluir:hover { background: #e30613; color: white; }

    .recibo {
        background: #f8f9fa;
        border-left: 5px solid #003366;
        padding: 15px;
        margin-top: 20px;
    }
</style>
</head>
<body>

<div class="container">
    <div class="caixa">
        <h2>🛒 PDV Vendas</h2>
        <form method="POST">
            <label>Produto:</label>
            <select name="produto_ean">
                <?php foreach ($produtos_db as $p): ?>
                    <option value="<?= $p['ean'] ?>"><?= $p['nome'] ?></option>
                <?php endforeach; ?>
            </select>
            <label><input type="checkbox" name="fidelidade"> Desconto Viva Saúde</label>
            <button type="submit" name="btn_venda" class="btn-venda">PROCESSAR VENDA</button>
        </form>

        <?php if ($resultado): ?>
            <div style="background: #fffde7; padding: 15px; border: 1px dashed #fbc02d; margin-top: 15px;">
                <strong>Recibo:</strong> <?= $resultado['nome'] ?><br>
                Total: <strong style="color: #2e7d32; font-size: 1.2em;">R$ <?= number_format($resultado['final'], 2, ',', '.') ?></strong>
            </div>
        <?php endif; ?>
    </div>

    <div class="caixa" style="border-top-color: #2e7d32;">
        <h2>➕ Novo Produto</h2>
        <form method="POST">
            <input type="text" name="ean" placeholder="EAN" required>
            <input type="text" name="nome" placeholder="Nome do Medicamento" required>
            <input type="number" step="0.01" name="preco" placeholder="Preço R$" required>
            <select name="categoria">
                <option value="Generico">Genérico</option>
                <option value="Referencia">Referência</option>
                <option value="Dermocosmetico">Dermocosmético</option>
            </select>
            <button type="submit" name="btn_cadastrar" class="btn-cadastrar">SALVAR NO BANCO</button>
        </form>
    </div>

    <div class="caixa estoque">
        <h3>📦 Gerenciar Estoque</h3>
        <table>
            <thead>
                <tr>
                    <th>EAN</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos_db as $p): ?>
                <tr>
                    <td><?= $p['ean'] ?></td>
                    <td><?= $p['nome'] ?></td>
                    <td>R$ <?= number_format($p['preco_tabela'], 2, ',', '.') ?></td>
                    <td>
                        <a href="?excluir=<?= $p['ean'] ?>" class="excluir" onclick="return confirm('Apagar este produto?')">EXCLUIR</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
// Passamos os dados do PHP para o JavaScript com segurança
const produtosJS = <?php echo json_encode($produtos_db); ?>;

document.getElementsByName('produto_ean')[0].addEventListener('change', function() {
    const eanSelecionado = this.value;
    const produto = produtosJS.find(p => p.ean == eanSelecionado);
    
    if (produto) {
        // Aqui você pode criar um elemento para mostrar o preço prévio
        console.log("Preço atual: R$ " + produto.preco_tabela);
        // Desafio: Tente fazer esse preço aparecer em um <span> ao lado do select!
    }
});
</script>

</body>
</html>