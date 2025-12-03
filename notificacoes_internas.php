<?php
session_start();
require 'conexao.php';

// Verifica se é admin
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] != 1) {
    header("Location: login.php");
    exit;
}

// Futuro: buscar notificações reais do banco
$notificacoes = [
    ['id' => 1, 'tipo' => 'Pedido', 'mensagem' => 'Novo pedido #1023 realizado.', 'data' => '2025-11-24 10:32', 'status' => 'Não lida'],
    ['id' => 2, 'tipo' => 'Pagamento', 'mensagem' => 'Pagamento confirmado para o pedido #1018.', 'data' => '2025-11-23 15:45', 'status' => 'Lida'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Notificações Internas - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --cor-primaria: #4A2C2A; --cor-hover: #5d3835; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<header class="bg-[var(--cor-primaria)] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">🔔 Notificações Internas</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
</header>

<main class="max-w-5xl mx-auto p-6">

    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold text-[var(--cor-primaria)] mb-4">Últimas Notificações</h2>

        <?php if(empty($notificacoes)): ?>
            <p class="text-gray-500">Nenhuma notificação até o momento.</p>
        <?php else: ?>
            <ul class="space-y-4">
                <?php foreach($notificacoes as $notif): ?>
                    <li class="border-l-4 <?= $notif['status'] === 'Não lida' ? 'border-red-600 bg-red-50' : 'border-green-600 bg-green-50' ?> p-4 rounded shadow-sm flex justify-between items-center">
                        <div>
                            <p class="font-semibold"><?= htmlspecialchars($notif['tipo']) ?></p>
                            <p class="text-gray-700"><?= htmlspecialchars($notif['mensagem']) ?></p>
                            <p class="text-xs text-gray-500"><?= $notif['data'] ?></p>
                        </div>
                        <div>
                            <?php if($notif['status'] === 'Não lida'): ?>
                                <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">Marcar como lida</button>
                            <?php else: ?>
                                <span class="text-green-700 font-semibold text-sm">Lida</span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="text-gray-500 text-sm italic mt-4">
            Futuro: aqui um esboço a onde irá receber alertas sobre novos pedidos, pagamentos e outras ações importantes da loja em tempo real.
        </div>
    </div>

</main>

</body>
</html>
