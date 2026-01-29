# 💊 Simulador de Regras de Negócio Farmacêutico

Este projeto simula a lógica de precificação e descontos de grandes redes de drogarias (como Drogasil e Drogaria São Paulo). O objetivo é estudar como as regras de negócio de **Trade Marketing** e **CRM** são aplicadas no backend de sistemas de varejo.

## 🚀 Tecnologias Utilizadas
* **PHP 8.2**: Linguagem principal para a lógica de descontos.
* **CLI**: Interface de linha de comando para execução dos testes.
* **Git**: Controle de versão e organização de código.

## ⚙️ Regras de Negócio Implementadas
O sistema aplica descontos baseados na categoria do produto e no perfil do cliente:
- **Genéricos**: 50% de desconto para clientes fidelidade (Estratégia de atração).
- **Dermocosméticos**: 15% de desconto (Estratégia de ticket médio alto).
- **Padrão**: 10% de desconto para as demais categorias.
