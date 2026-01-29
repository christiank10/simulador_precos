<?php
/**
 * SISTEMA DE VENDAS FARMACÊUTICAS - VERSÃO POSTGRESQL
 */

// 1. Configuração da Conexão com pgAdmin / PostgreSQL
$host = 'localhost';
$db   = 'farmacia_db';
$user = 'postgres';
$pass = '1234'; // <--- COLOQUE SUA SENHA DO POSTGRES AQUI

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Busca os produtos diretamente do banco de dados
    $sql = "SELECT * FROM produtos ORDER BY nome ASC";
    $stmt = $pdo->query($sql);
    $produtos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro crítico de conexão: " . $e->getMessage());
}

$resultado = null;

// 3. Lógica de Processamento da Venda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['produto_ean'])) {
    $ean_selecionado = $_POST['produto_ean'];
    $fidelidade = isset($_POST['fidelidade']);

    // Busca o produto específico no banco para garantir o preço atualizado
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE ean = :ean");
    $stmt->execute(['ean' => $ean_selecionado]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $desconto = 0;
        
        // Aplica regras de negócio baseadas na categoria vinda do Banco
        if ($fidelidade) {
            $desconto = ($item['categoria'] == "Generico") ? 0.50 : 0.15;
        }
        
        $precoFinal = $item['preco_tabela'] * (1 - $desconto);
        $resultado = [
            "nome" => $item['nome'],
            "original" => $item['preco_tabela'],
            "final" => $precoFinal,
            "economizou" => $item['preco_tabela'] - $precoFinal,
            "categoria" => $item['categoria']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV Farmácia - Conectado ao Postgres</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #e9ecef; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .caixa { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 450px; border-top: 8px solid #d32f2f; }
        h2 { color: #d32f2f; text-align: center; margin-top: 0; }
        .status-db { font-size: 10px; color: green; text-align: center; margin-bottom: 20px; text-transform: uppercase; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #444; }
        select, button { width: 100%; padding: 12px; margin: 15px 0; border-radius: 6px; border: 1px solid #ddd; font-size: 16px; }
        .checkbox-area { display: flex; align-items: center; gap: 10px; cursor: pointer; background: #f8f9fa; padding: 10px; border-radius: 6px; }
        button { background: #d32f2f; color: white; border: none; cursor: pointer; font-weight: bold; transition: background 0.3s; }
        button:hover { background: #b71c1c; }
        .cupom { background: #fffde7; padding: 20px; border: 2px dashed #fbc02d; margin-top: 25px; border-radius: 4px; }
        .total { font-size: 24px; color: #2e7d32; font-weight: bold; }
    </style>
</head>
<body>

<div class="caixa">
    <div class="status-db">● Banco de Dados: Conectado (PostgreSQL)</div>
    <h2>PDV Farmácia</h2>
    
    <form method="POST">
        <label>Selecione o Medicamento:</label>
        <select name="produto_ean">
            <?php foreach ($produtos_db as $p): ?>
                <option value="<?= $p['ean'] ?>"><?= $p['nome'] ?> (<?= $p['categoria'] ?>)</option>
            <?php endforeach; ?>
        </select>

        <label class="checkbox-area">
            <input type="checkbox" name="fidelidade"> Ativar Desconto CPF (Viva Saúde)
        </label>

        <button type="submit">PROCESSAR VENDA</button>
    </form>

    <?php if ($resultado): ?>
        <div class="cupom">
            <strong>RECIBO DE VENDA</strong><hr>
            Produto: <?= $resultado['nome'] ?><br>
            Categoria: <?= $resultado['categoria'] ?><br>
            Valor Base: R$ <?= number_format($resultado['original'], 2, ',', '.') ?><br>
            Economia: <span style="color: #2e7d32;">R$ <?= number_format($resultado['economizou'], 2, ',', '.') ?></span><hr>
            <div class="total">R$ <?= number_format($resultado['final'], 2, ',', '.') ?></div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>