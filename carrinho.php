<?php
session_start();
include('conexao.php');

// Opcional: Proteger a página se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Lógica para calcular o total
$carrinho = $_SESSION['carrinho'] ?? [];
$total_geral = 0;
foreach ($carrinho as $item) {
    $total_geral += $item['preco'] * $item['quantidade'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho - Cafeteria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --cor-primaria: #4A2C2A;
            --cor-hover: #5d3835;
        }
        .text-\[var\(--cor-primaria\)\] { color: var(--cor-primaria); }
    </style>
</head>
<body class="bg-gray-50 font-sans min-h-screen">
    <header class="bg-white shadow-md sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-extrabold text-[var(--cor-primaria)]">🛒 Seu Carrinho</h1>
            <a href="index.php" class="text-[var(--cor-primaria)] hover:text-[var(--cor-hover)]">Voltar à Loja</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto p-6">
        <h2 class="text-3xl font-bold mb-6 text-[var(--cor-primaria)]">Itens no Carrinho</h2>
        
        <?php if (empty($carrinho)): ?>
            <div class="bg-white p-6 rounded-lg shadow-xl text-center">
                <p class="text-xl text-gray-600 mb-4">Seu carrinho está vazio. Adicione alguns itens deliciosos!</p>
                <a href="index.php" class="inline-block bg-[var(--cor-primaria)] text-white px-6 py-3 rounded-lg hover:bg-[var(--cor-hover)] font-semibold transition">
                    Ver Produtos
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-xl">
                <ul class="divide-y divide-gray-200">
                    <?php 
                    foreach ($carrinho as $item):
                        $subtotal = $item['preco'] * $item['quantidade'];
                    ?>
                    <li class="px-4 py-4 sm:px-6 flex justify-between items-center">
                        <div class="flex flex-col">
                            <span class="text-lg font-semibold"><?= htmlspecialchars($item['nome']) ?></span>
                            <span class="text-sm text-gray-500">
                                R$ <?= number_format($item['preco'], 2, ',', '.') ?> x **<?= $item['quantidade'] ?>**
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-bold text-red-600">
                                R$ <?= number_format($subtotal, 2, ',', '.') ?>
                            </span>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="mt-6 p-4 bg-gray-200 rounded-lg flex justify-between items-center">
                <span class="text-xl font-bold text-[var(--cor-primaria)]">TOTAL GERAL:</span>
                <span class="text-3xl font-extrabold text-red-700">R$ <?= number_format($total_geral, 2, ',', '.') ?></span>
            </div>
            
            <!-- AQUI ESTÁ A MUDANÇA: Link para checkout.php e cor do botão vermelha -->
            <a href="checkout.php" class="mt-4 block w-full text-center bg-red-600 text-white px-6 py-3 rounded-lg text-lg font-bold hover:bg-red-700 transition duration-150">
                Finalizar Pedido
            </a>

            <!-- Botão para continuar comprando -->
            <div class="mt-3 text-center">
                <a href="index.php" class="text-sm text-[var(--cor-primaria)] hover:underline">
                    &larr; Continuar comprando
                </a>
            </div>

        <?php endif; ?>
    </main>
</body>
</html>
