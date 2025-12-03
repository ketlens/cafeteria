<?php
session_start();
require 'conexao.php';

// Verifica se é admin
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] != 1) {
    header("Location: login.php");
    exit;
}

// Futuro: buscar mensagens do banco
$mensagens = [
    ['id' => 1, 'nome' => 'João Silva', 'email' => 'joao@email.com', 'assunto' => 'Dúvida sobre pedido', 'mensagem' => 'Olá, meu pedido não chegou.', 'status' => 'Pendente', 'data' => '2025-11-24'],
    ['id' => 2, 'nome' => 'Maria Souza', 'email' => 'maria@email.com', 'assunto' => 'Sugestão', 'mensagem' => 'Seria ótimo ter opção vegana.', 'status' => 'Pendente', 'data' => '2025-11-23'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mensagens de Clientes - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --cor-primaria: #4A2C2A; --cor-hover: #5d3835; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<header class="bg-[var(--cor-primaria)] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">📬 Mensagens de Clientes</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
</header>

<main class="max-w-5xl mx-auto p-6">

    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold text-[var(--cor-primaria)] mb-4">Mensagens Recentes</h2>

        <?php if(empty($mensagens)): ?>
            <p class="text-gray-500">Nenhuma mensagem recebida até o momento.</p>
        <?php else: ?>
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Nome</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Assunto</th>
                        <th class="px-4 py-2 text-left">Mensagem</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Data</th>
                        <th class="px-4 py-2 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mensagens as $msg): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?= $msg['id'] ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($msg['nome']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($msg['email']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($msg['assunto']) ?></td>
                            <td class="px-4 py-2 max-w-xs overflow-hidden text-ellipsis"><?= htmlspecialchars($msg['mensagem']) ?></td>
                            <td class="px-4 py-2"><?= $msg['status'] ?></td>
                            <td class="px-4 py-2"><?= $msg['data'] ?></td>
                            <td class="px-4 py-2 text-center">
                                <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">Responder</button>
                                <button class="bg-gray-400 text-white px-3 py-1 rounded hover:bg-gray-500 text-sm">Arquivar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="text-gray-500 text-sm italic mt-4">
            Futuro: aqui será a área de gerenciar o contato com clientes, responder e organizar mensagens diretamente pelo painel.
        </div>
    </div>

</main>

</body>
</html>
