<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// CONSULTA SEM USAR A TABELA "promocoes"
$sql = "
    SELECT id, nome, preco, categoria, imagem 
    FROM produtos
    ORDER BY data_criacao DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Promoções Futuras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<header class="bg-[#4A2C2A] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Promoções Futuras</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
</header>

<div class="max-w-4xl mx-auto mt-6 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Produtos Disponíveis</h2>

    <table class="w-full border border-gray-300">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-4 py-2">Produto</th>
                <th class="border px-4 py-2">Categoria</th>
                <th class="border px-4 py-2">Preço</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($p = $result->fetch_assoc()): ?>
            <tr>
                <td class="border px-4 py-2"><?= $p['nome'] ?></td>
                <td class="border px-4 py-2"><?= $p['categoria'] ?></td>
                <td class="border px-4 py-2">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
