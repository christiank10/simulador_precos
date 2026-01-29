<?php
// Reutilizando nossa base de dados (No futuro, isso viria de um SQL)
$produtos = [
    "7891001" => ["nome" => "Dipirona Monoidratada 500mg", "preco" => 15.00, "tipo" => "Generico"],
    "7891002" => ["nome" => "Protetor Solar Vichy FPS60", "preco" => 95.00, "tipo" => "Dermocosmetico"],
    "7891003" => ["nome" => "Aspirina 100mg 30cp", "preco" => 22.50, "tipo" => "Referencia"]
];

$resultado = null;

// Lógica para processar quando o botão for clicado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ean = $_POST['produto'];
    $fidelidade = isset($_POST['fidelidade']);
    
    if (isset($produtos[$ean])) {
        $item = $produtos[$ean];
        $desconto = 0;
        
        if ($fidelidade) {
            $desconto = ($item['tipo'] == "Generico") ? 0.50 : 0.15;
        }
        
        $precoFinal = $item['preco'] * (1 - $desconto);
        $resultado = [
            "nome" => $item['nome'],
            "original" => $item['preco'],
            "final" => $precoFinal,
            "economizou" => $item['preco'] - $precoFinal
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>PDV Simulado - Drogasil/São Paulo</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; padding: 50px; }
        .caixa { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        h2 { color: #d32f2f; border-bottom: 2px solid #d32f2f; padding-bottom: 10px; }
        select, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; }
        button { background: #2e7d32; color: white; border: none; cursor: pointer; font-weight: bold; }
        .cupom { background: #fffde7; padding: 15px; border: 1px dashed #fbc02d; margin-top: 20px; }
    </style>
</head>
<body>

<div class="caixa">
    <h2>Sistema de Vendas</h2>
    <form method="POST">
        <label>Selecione o Produto:</label>
        <select name="produto">
            <?php foreach ($produtos as $ean => $info): ?>
                <option value="<?= $ean ?>"><?= $info['nome'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>
            <input type="checkbox" name="fidelidade"> Cliente tem CPF (Viva Saúde/Stix)
        </label>

        <button type="submit">Calcular Preço</button>
    </form>

    <?php if ($resultado): ?>
        <div class="cupom">
            <strong>CUPOM FISCAL:</strong><br>
            Item: <?= $resultado['nome'] ?><br>
            De: R$ <?= number_format($resultado['original'], 2, ',', '.') ?><br>
            <strong>Por: R$ <?= number_format($resultado['final'], 2, ',', '.') ?></strong><br>
            <small style="color: green;">Você economizou: R$ <?= number_format($resultado['economizou'], 2, ',', '.') ?></small>
        </div>
    <?php endif; ?>
</div>

</body>
</html>