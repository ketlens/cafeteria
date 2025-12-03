<?php
session_start();
require 'conexao.php';

// Verifica se é admin
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] != 1) {
    header("Location: login.php");
    exit;
}

// Aqui podemos processar futuras alterações do admin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Exemplo de placeholders
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    $taxa_entrega = $_POST['taxa_entrega'] ?? '';

    // Por enquanto, apenas mensagem de sucesso
    $msg_sucesso = "Configurações salvas com sucesso! (Futuro: salvar no banco)";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Configurações da Loja - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --cor-primaria: #4A2C2A; --cor-hover: #5d3835; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<header class="bg-[var(--cor-primaria)] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold"> Configurações da Loja</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
</header>

<main class="max-w-3xl mx-auto p-6">

    <?php if (!empty($msg_sucesso)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <?= $msg_sucesso ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded shadow space-y-6">

        <h2 class="text-xl font-bold text-[var(--cor-primaria)]">Horário de Funcionamento</h2>
        <div class="flex gap-4">
            <div>
                <label class="block font-semibold mb-1" for="horario_inicio">Início:</label>
                <input type="time" name="horario_inicio" id="horario_inicio" class="border p-2 rounded w-full">
            </div>
            <div>
                <label class="block font-semibold mb-1" for="horario_fim">Fim:</label>
                <input type="time" name="horario_fim" id="horario_fim" class="border p-2 rounded w-full">
            </div>
        </div>

        <h2 class="text-xl font-bold text-[var(--cor-primaria)]">Taxas e Informações de Entrega</h2>
        <div>
            <label class="block font-semibold mb-1" for="taxa_entrega">Taxa de Entrega (R$):</label>
            <input type="number" step="0.01" name="taxa_entrega" id="taxa_entrega" class="border p-2 rounded w-full" placeholder="Ex: 5.00">
        </div>

        <div class="text-gray-500 text-sm italic">
            Futuro: aqui dará para configurar horários de funcionamento, taxas de entrega, impostos e outras informações da loja.
        </div>

        <button type="submit" class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded hover:bg-[var(--cor-hover)] transition">Salvar Configurações</button>
    </form>

</main>

</body>
</html>
