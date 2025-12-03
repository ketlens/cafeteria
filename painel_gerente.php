<?php
session_start();
require 'conexao.php'; 

// --- VERIFICAÇÃO DE ACESSO ---
// Apenas Gerente (nivel_acesso = 2) pode acessar
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 2) {
    header("Location: index.php?alerta=acesso_negado");
    exit;
}

$nome_usuario = $_SESSION['usuario_nome'] ?? 'Gerente';
$nivel_acesso = $_SESSION['nivel_acesso']; // 2 = Gerente
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Gerente de Vendas</title>
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

<!-- CABEÇALHO -->
<header class="bg-[var(--cor-primaria)] shadow-md sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-3xl font-extrabold text-white">
            Painel do Gerente de Vendas ⚙️
        </h1>
        <nav class="flex space-x-4 items-center">
            <span class="text-gray-300 text-sm hidden sm:block">Olá, <?= htmlspecialchars($nome_usuario) ?>!</span>
            <a href="index.php" class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-600 transition shadow-md">Ver Loja</a>
            <a href="logout.php" class="border border-gray-300 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Sair</a>
        </nav>
    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-12">
    <h2 class="text-4xl font-bold mb-10 text-gray-800">Bem-vindo(a) ao Painel do Gerente</h2>

    <!-- SEÇÃO 1: ANÁLISE DE VENDAS -->
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
        </div>
    </section>

    <!-- SEÇÃO 2: PEDIDOS EM ANDAMENTO -->
    <section>
        <h3 class="text-2xl font-semibold mb-6 text-[var(--cor-primaria)] border-b pb-2">Pedidos em Andamento</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <a href="pedidos_andamento.php" class="card-link block bg-white p-6 rounded-xl border border-gray-100 hover:border-red-500">
                <div class="text-4xl text-red-600 mb-3">📦</div>
                <h4 class="text-xl font-bold text-gray-800 mb-1">Pedidos em Andamento</h4>
                <p class="text-gray-500 text-sm">Gerencie, atualize status e visualize os pedidos ativos.</p>
            </a>
        </div>
    </section>

</main>

</body>
</html>
