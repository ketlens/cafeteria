<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mes = $_GET['mes'] ?? date("Y-m");
list($ano, $num_mes) = explode("-", $mes);

$inicio_mes = date("Y-m-01", strtotime("$ano-$num_mes-01"));
$fim_mes    = date("Y-m-t", strtotime($inicio_mes));

$sql = "
SELECT p.nome AS produto, 
       SUM(v.quantidade) AS total_vendido, 
       SUM(v.total) AS total_valor
FROM venda v
JOIN produtos p ON p.id = v.produto_id
WHERE v.data_venda BETWEEN ? AND ?
GROUP BY p.nome
ORDER BY total_vendido DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $inicio_mes, $fim_mes);
$stmt->execute();
$res = $stmt->get_result();

$produtos = [];
while ($row = $res->fetch_assoc()) {
    $produtos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Produtos Mais Vendidos</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<header class="bg-[#4A2C2A] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Produtos Mais Vendidos</h1>
    <a href="indexadim.php?mes=<?= $mes ?>" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
</header>

<main class="max-w-5xl mx-auto p-6 bg-white rounded shadow mt-6">
    <table class="min-w-full border border-gray-300">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-4 py-2">Produto</th>
                <th class="border px-4 py-2">Quantidade Vendida</th>
                <th class="border px-4 py-2">Valor Total (R$)</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($produtos as $p): ?>
            <tr>
                <td class="border px-4 py-2"><?= $p['produto'] ?></td>
                <td class="border px-4 py-2"><?= $p['total_vendido'] ?></td>
                <td class="border px-4 py-2"><?= number_format($p['total_valor'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>
