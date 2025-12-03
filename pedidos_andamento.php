<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Consulta os pedidos ativos do usuário
$sql = "SELECT * FROM pedidos WHERE usuario_id = ? AND status IN ('Processando', 'Em Andamento') ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedidos em Andamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Header -->
<header class="bg-[#4A2C2A] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Pedidos em Andamento</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
    <?php if ($_SESSION['nivel_acesso'] == 2): ?>
    <a href="painel_gerente.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mt-6 inline-block">
        Voltar para a Página Inicial
    </a>
<?php endif; ?>
</header>

<div class="p-6">
    <?php if ($result->num_rows > 0): ?>
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-[#8B5E3C] text-white">
                <tr>
                    <th class="py-3 px-6 text-left">ID Pedido</th>
                    <th class="py-3 px-6 text-left">Data</th>
                    <th class="py-3 px-6 text-left">Valor Total</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($pedido = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-100">
                        <td class="py-3 px-6"><?= $pedido['id'] ?></td>
                        <td class="py-3 px-6"><?= date('d/m/Y H:i', strtotime($pedido['data'])) ?></td>
                        <td class="py-3 px-6">R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></td>
                        <td class="py-3 px-6"><?= $pedido['status'] ?></td>
                        <td class="py-3 px-6">
                            <a href="pedido_detalhes.php?id=<?= $pedido['id'] ?>" class="bg-[#8B5E3C] text-white px-3 py-1 rounded hover:bg-[#7a4e31]">Ver Detalhes</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-700">Você não possui pedidos ativos no momento.</p>
    <?php endif; ?>
</div>

</body>
</html>
