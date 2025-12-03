<?php
session_start();
// O arquivo 'conexao.php' será incluído aqui, mas não é necessário para esta página, apenas para o header.
// Deixaremos o include para garantir consistência em ambientes PHP
require 'conexao.php'; 

// --- 1. VERIFICAÇÃO DE SEGURANÇA: APENAS ADMIN PODE ACESSAR ---
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 1) {
    // Se não for admin, redireciona para a página principal
    header("Location: index.php?alerta=acesso_negado");
    exit;
}

$nome_admin = $_SESSION['usuario_nome'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel Admin - Café & Aroma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --cor-primaria: #4A2C2A; 
            --cor-hover: #5d3835;
        }
        .card-link {
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .card-link:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 20px 25px -5px rgba(220, 38, 38, 0.2), 0 8px 10px -6px rgba(220, 38, 38, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans min-h-screen">
    
    <!-- CABEÇALHO ADMIN -->
    <header class="bg-[var(--cor-primaria)] shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-white">
                Painel do Administrador ⚙️
            </h1>
            <nav class="flex space-x-4 items-center">
                <span class="text-gray-300 text-sm hidden sm:block">Olá, <?= htmlspecialchars($nome_admin) ?>!</span>
                <a href="index.php" class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600 transition shadow-md">Ver Loja</a>
                <a href="logout.php" class="border border-gray-300 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Sair</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <h2 class="text-4xl font-bold mb-10 text-gray-800">Bem-vindo(a) ao Centro de Controle</h2>

        <!-- SEÇÃO 1: GESTÃO DO CARDÁPIO E PROMOÇÕES -->
        <section class="mb-12">
            <h3 class="text-2xl font-semibold mb-6 text-[var(--cor-primaria)] border-b pb-2">Gestão de Cardápio e Marketing</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <a href="dashboard.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">📋</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Cadastrar e Editar Produtos</h4>
                    <p class="text-gray-500 text-sm">Adicione novos itens, altere preços e descrições do menu principal.</p>
                </a>
                
                <a href="promocoes.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">🔥</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Gerenciar Promoções do Dia</h4>
                    <p class="text-gray-500 text-sm">Defina quais produtos aparecerão na seção de destaque de promoções.</p>
                </a>
                
                <a href="mais_vendidos.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">⭐</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Ver Mais Vendidos</h4>
                    <p class="text-gray-500 text-sm">Visualizar os produtos mais populares por período.</p>
                </a>
                <a href="promocoes_futuras.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">⭐</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Produtos e promoções Futuras</h4>
                    <p class="text-gray-500 text-sm">Visualizar os produtos mais populares por período.</p>
                </a>
                <a href="promocoes.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">⭐</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Produtos em Promoções</h4>
                    <p class="text-gray-500 text-sm">Visualizar os produtos mais populares por período (Futuro).</p>
                </a>
            </div>
        </section>

        <!-- SEÇÃO 2: ANÁLISE DE VENDAS -->
        <section class="mb-12">
            <h3 class="text-2xl font-semibold mb-6 text-[var(--cor-primaria)] border-b pb-2">Análise de Vendas e Desempenho</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <a href="analise_dia.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">🗓️</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Vendas do Dia</h4>
                    <p class="text-gray-500 text-sm">Relatório e total de pedidos e faturamento de hoje.</p>
                </a>

                <a href="analise_semana.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">📊</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Vendas da Semana</h4>
                    <p class="text-gray-500 text-sm">Acompanhamento e comparação de desempenho semanal.</p>
                </a>
                
                <a href="analise_mes.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">📈</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Vendas do Mês</h4>
                    <p class="text-gray-500 text-sm">Visão geral do faturamento e tendências mensais.</p>
                </a>

                <a href="faturamento_total.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">📊</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Relatórios e Estatísticas</h4>
                    <p class="text-gray-500 text-sm">Acesse relatórios detalhados de faturamento e estoque.</p>
                </a>
            </div>
        </section>

        <!-- SEÇÃO 3: OUTRAS FUNÇÕES ADMINISTRATIVAS -->
        <section>
            <h3 class="text-2xl font-semibold mb-6 text-[var(--cor-primaria)] border-b pb-2">Outras Ferramentas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <a href="pedidos_andamento.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">📦</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Pedidos em Andamento</h4>
                    <p class="text-gray-500 text-sm">Gerencie, atualize status e visualize os pedidos ativos.</p>
                </a>
                
                <a href="gerenciar_usuarios.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">👥</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Gerenciar Usuários</h4>
                    <p class="text-gray-500 text-sm">Lista de clientes e administradores (Futuro).</p>
                </a>

                <a href="gestao_cupons.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">🎯</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Gerenciar Cupons de Desconto</h4>
                    <p class="text-gray-500 text-sm">Crie, edite e valide cupons para campanhas promocionais(Futuro).</p>
                </a>

                <a href="configuracoes_loja.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">⚙️</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Configurações da Loja</h4>
                    <p class="text-gray-500 text-sm">Ajuste informações como horários de funcionamento e taxas(Futuro).</p>
                </a>
            </div>
        </section>

        <!-- SEÇÃO 4: SUORTE / COMUNICAÇÃO -->
        <section class="mb-12">
            <h3 class="text-2xl font-semibold mb-6 text-[var(--cor-primaria)] border-b pb-2">Suporte e Comunicação</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <a href="mensagens_clientes.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">✉️</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Mensagens de Clientes</h4>
                    <p class="text-gray-500 text-sm">Gerencie o contato e suporte para clientes diretamente pelo painel(Futuro).</p>
                </a>
                
                <a href="notificacoes_internas.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                    <div class="text-4xl text-red-600 mb-3">🛎️</div>
                    <h4 class="text-xl font-bold text-gray-800 mb-1">Notificações Internas</h4>
                    <p class="text-gray-500 text-sm">Receba alertas e notificações sobre novos pedidos e pagamentos(Futuro).</p>
                </a>
            </div>
        </section>

    </main>

    <!-- Rodapé -->
    <footer class="bg-gray-800 text-white text-center py-6">
        <p>&copy; 2025 Café & Aroma. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
