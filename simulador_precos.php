<?php
/**
 * PROJETO: Simulador de Regras de Varejo Farmacêutico
 * FOCO: Estudo de lógica de descontos (Drogasil e Drogaria São Paulo)
 */

// 1. Simulação do Banco de Dados de Produtos
$produtos = [
    "7891001" => ["nome" => "Dipirona Monoidratada 500mg", "preco_tabela" => 15.00, "categoria" => "Generico"],
    "7891002" => ["nome" => "Protetor Solar Vichy FPS60", "preco_tabela" => 95.00, "categoria" => "Dermocosmetico"],
    "7891003" => ["nome" => "Aspirina 100mg 30cp", "preco_tabela" => 22.50, "categoria" => "Referencia"],
    "7891004" => ["nome" => "Fralda Huggies G 32un", "preco_tabela" => 58.90, "categoria" => "Higiene"]
];

/**
 * Função que simula o "Coração" do sistema de caixa (PDV)
 */
function calcularPrecoFinal($ean, $isClienteFidelidade, $listaProdutos) {
    // Verifica se o produto existe
    if (!isset($listaProdutos[$ean])) {
        return "Erro: Produto não encontrado no sistema.";
    }

    $item = $listaProdutos[$ean];
    $precoBase = $item['preco_tabela'];
    $percentualDesconto = 0;
    $tipoDesconto = "Preço de Tabela";

    // Lógica de Programação: Regras de Negócio das Redes
    if ($isClienteFidelidade) {
        if ($item['categoria'] == "Generico") {
            $percentualDesconto = 0.50; // 50% de desconto (Padrão Drogasil/São Paulo para Genéricos)
            $tipoDesconto = "Desconto Especial Genérico (Fidelidade)";
        } elseif ($item['categoria'] == "Dermocosmetico") {
            $percentualDesconto = 0.15; // 15% de desconto (Padrão Viva Saúde/Stix)
            $tipoDesconto = "Oferta Dermocosmético";
        } else {
            $percentualDesconto = 0.10; // 10% padrão para outros itens
            $tipoDesconto = "Desconto Fidelidade Padrão";
        }
    }

    $valorFinal = $precoBase * (1 - $percentualDesconto);
    $economia = $precoBase - $valorFinal;

    return [
        "Produto" => $item['nome'],
        "Categoria" => $item['categoria'],
        "Preço Original" => "R$ " . number_format($precoBase, 2, ',', '.'),
        "Desconto Aplicado" => ($percentualDesconto * 100) . "%",
        "Tipo de Desconto" => $tipoDesconto,
        "Valor Final" => "R$ " . number_format($valorFinal, 2, ',', '.'),
        "Você Economizou" => "R$ " . number_format($economia, 2, ',', '.')
    ];
}

// 2. Execução do Teste (Simulando uma venda no terminal)
echo "\n--- SIMULADOR DE CAIXA FARMACÊUTICO ---\n";

// Teste 1: Cliente com CPF (Fidelidade) comprando um Genérico
$venda1 = calcularPrecoFinal("7891001", true, $produtos);
print_r($venda1);

// Teste 2: Cliente comprando Dermocosmético
$venda2 = calcularPrecoFinal("7891002", true, $produtos);
print_r($venda2);

echo "\n--- FIM DA OPERAÇÃO ---\n";