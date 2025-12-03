<?php
session_start();
include('conexao.php');

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Administrador ou gerente
if ($_SESSION['nivel_acesso'] != 1 && $_SESSION['nivel_acesso'] != 2) {
    header("Location: index.php?alerta=acesso_negado");
    exit;
}

// DATA SELECIONADA
$data = $_GET['data'] ?? date("Y-m-d");

/* =======================
   TOTAL DO DIA
========================= */
$sql_total = "
SELECT SUM(total) AS total
FROM venda
WHERE DATE(data_venda)=?
";

$stmt = $conn->prepare($sql_total);
$stmt->bind_param("s", $data);
$stmt->execute();
$total_dia = ($stmt->get_result()->fetch_assoc()['total']) ?? 0;


/* =======================
   QUANTIDADE TOTAL
========================= */
$sql_qtd = "
SELECT SUM(quantidade) AS qtd
FROM venda
WHERE DATE(data_venda)=?
";

$stmt = $conn->prepare($sql_qtd);
$stmt->bind_param("s", $data);
$stmt->execute();
$qtd_total = ($stmt->get_result()->fetch_assoc()['qtd']) ?? 0;


/* =======================
   LUCRO
========================= */
$margem = 0.30;
$lucro = $total_dia * $margem;


/* =======================
   GRÁFICO DE VENDAS POR HORA
========================= */
$sql_horas = "
SELECT HOUR(data_venda) AS hora, SUM(total) AS valor
FROM venda
WHERE DATE(data_venda)=?
GROUP BY HOUR(data_venda)
ORDER BY hora ASC
";

$stmt = $conn->prepare($sql_horas);
$stmt->bind_param("s", $data);
$stmt->execute();
$res_horas = $stmt->get_result();

$horarios = [];
$valores = [];

while ($row = $res_horas->fetch_assoc()) {
    $horarios[] = sprintf('%02d:00', $row['hora']);
    $valores[] = $row['valor'];
}


/* =======================
   PRODUTOS MAIS VENDIDOS
========================= */
$sql_produtos = "
SELECT p.nome, SUM(v.quantidade) AS qtd
FROM venda v
JOIN produtos p ON p.id = v.produto_id
WHERE DATE(v.data_venda)=?
GROUP BY p.nome
ORDER BY qtd DESC
";

$stmt = $conn->prepare($sql_produtos);
$stmt->bind_param("s", $data);
$stmt->execute();
$res_produtos = $stmt->get_result();

$produtos = [];
$qtd_produtos = [];

while ($row = $res_produtos->fetch_assoc()) {
    $produtos[] = $row['nome'];
    $qtd_produtos[] = $row['qtd'];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório do Dia - Cafeteria</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root { --cor-primaria:#4A2C2A; }
</style>
</head>

<body class="bg-gray-100">

<header class="bg-[var(--cor-primaria)] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Relatório do Dia</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded">Voltar</a>
    <?php if ($_SESSION['nivel_acesso'] == 2): ?>
    <a href="painel_gerente.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mt-6 inline-block">
        Voltar para a Página Inicial
    </a>
<?php endif; ?>
</header>

<main class="max-w-6xl mx-auto p-6">

<form method="GET" class="bg-white p-4 rounded shadow flex gap-4 items-center mb-6">
    <label class="font-semibold text-[var(--cor-primaria)]">Selecionar Data:</label>

    <input type="date" name="data" value="<?= $data ?>" class="border p-2 rounded">

    <button class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded hover:bg-[#6b423f]">
        Gerar Relatório
    </button>

    <button type="button" onclick="window.print()" 
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Imprimir Relatório
    </button>
</form>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded shadow text-center">
        <h3 class="text-gray-600 font-semibold">Total vendido</h3>
        <p class="text-3xl font-bold text-red-600">
            R$ <?= number_format($total_dia, 2, ',', '.') ?>
        </p>
    </div>

    <div class="bg-white p-6 rounded shadow text-center">
        <h3 class="text-gray-600 font-semibold">Quantidade vendida</h3>
        <p class="text-3xl font-bold text-blue-600">
            <?= $qtd_total ?> itens
        </p>
    </div>

    <div class="bg-white p-6 rounded shadow text-center">
        <h3 class="text-gray-600 font-semibold">Lucro estimado</h3>
        <p class="text-3xl font-bold text-green-600">
            R$ <?= number_format($lucro, 2, ',', '.') ?>
        </p>
    </div>
</div>

<div class="bg-white p-6 rounded shadow mb-8">
    <h2 class="text-xl font-bold mb-3 text-[var(--cor-primaria)]">
        Vendas por horário (<?= date("d/m/Y", strtotime($data)) ?>)
    </h2>
    <canvas id="graficoHoras"></canvas>
</div>

<div class="bg-white p-6 rounded shadow mb-8">
    <h2 class="text-xl font-bold mb-3 text-[var(--cor-primaria)]">
        Produtos mais vendidos
    </h2>
    <canvas id="graficoProdutos"></canvas>
</div>

<div class="bg-white p-6 rounded shadow mb-8">
    <h2 class="text-xl font-bold mb-3 text-[var(--cor-primaria)]">Lista de vendas</h2>

    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">Produto</th>
                <th class="border p-2">Qtd</th>
                <th class="border p-2">Total</th>
                <th class="border p-2">Hora</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $sql_detalhes = "
SELECT 
    p.nome,
    v.quantidade,
    (v.total / v.quantidade) AS preco_unitario,
    DATE_FORMAT(v.data_venda, '%H:%i') AS hora
FROM venda v
JOIN produtos p ON p.id = v.produto_id
WHERE DATE(v.data_venda)='$data'
ORDER BY v.data_venda ASC";

$detalhes = $conn->query($sql_detalhes);

while($d = $detalhes->fetch_assoc()):
?>
<tr>
    <td class="border p-2"><?= $d['nome'] ?></td>
    <td class="border p-2"><?= $d['quantidade'] ?></td>
    <td class="border p-2">R$ <?= number_format($d['preco_unitario'],2,',','.') ?></td>
    <td class="border p-2">R$ <?= number_format($d['preco_unitario'] * $d['quantidade'],2,',','.') ?></td>
    <td class="border p-2"><?= $d['hora'] ?></td>
</tr>
<?php endwhile; ?>

        </tbody>
    </table>

</div>

</main>

<script>
// GRÁFICO DE HORÁRIOS
new Chart(document.getElementById('graficoHoras'), {
    type: 'line',
    data: {
        labels: <?= json_encode($horarios) ?>,
        datasets: [{
            label: 'Vendas (R$)',
            data: <?= json_encode($valores) ?>,
            borderColor: '#4A2C2A',
            backgroundColor: '#4A2C2A55',
            tension: 0.3,
            borderWidth: 3
        }]
    }
});

// GRÁFICO DE PRODUTOS
new Chart(document.getElementById('graficoProdutos'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($produtos) ?>,
        datasets: [{
            label: 'Quantidade',
            data: <?= json_encode($qtd_produtos) ?>,
            backgroundColor: '#b63131aa'
        }]
    }
});
</script>

</body>
</html>
