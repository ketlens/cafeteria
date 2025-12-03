<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['nivel_acesso'] != 1 && $_SESSION['nivel_acesso'] != 2) {
    header("Location: index.php?alerta=acesso_negado");
    exit;
}

/* =========================
   SEMANA SELECIONADA
========================= */
$semana = $_GET['semana'] ?? date("Y-\WW");
list($ano, $num_semana) = explode("-W", $semana);

$inicio_semana = date("Y-m-d", strtotime($ano . "W" . $num_semana));
$fim_semana = date("Y-m-d", strtotime($inicio_semana . " +6 days"));

/* =========================
   FUNÇÕES
========================= */
function totalSemana($conn, $inicio, $fim)
{
    $sql = "SELECT SUM(total) AS total FROM venda WHERE data_venda BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $inicio, $fim);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

function qtdSemana($conn, $inicio, $fim)
{
    $sql = "SELECT SUM(quantidade) AS qtd FROM venda WHERE data_venda BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $inicio, $fim);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['qtd'] ?? 0;
}

/* =========================
   DADOS DA SEMANA
========================= */
$total_semana = totalSemana($conn, $inicio_semana, $fim_semana);
$qtd_semana = qtdSemana($conn, $inicio_semana, $fim_semana);
$lucro_semana = $total_semana * 0.30;

/* =========================
   PRODUTOS MAIS VENDIDOS
========================= */
$sql_prod = "
SELECT p.nome, SUM(v.quantidade) AS qtd
FROM venda v
JOIN produtos p ON v.produto_id = p.id
WHERE v.data_venda BETWEEN ? AND ?
GROUP BY p.nome
ORDER BY qtd DESC";
$stmt = $conn->prepare($sql_prod);
$stmt->bind_param("ss", $inicio_semana, $fim_semana);
$stmt->execute();
$res_prod = $stmt->get_result();

$produtos = [];
$qtd_produtos = [];
while ($p = $res_prod->fetch_assoc()) {
    $produtos[] = $p['nome'];
    $qtd_produtos[] = $p['qtd'];
}

/* =========================
   LOCAIS COM MAIS ENTREGAS
========================= */
$sql_local = "
SELECT local, COUNT(*) AS total
FROM venda
WHERE data_venda BETWEEN ? AND ?
GROUP BY local
ORDER BY total DESC";
$stmt = $conn->prepare($sql_local);
$stmt->bind_param("ss", $inicio_semana, $fim_semana);
$stmt->execute();
$res_local = $stmt->get_result();

$locais = [];
$entregas = [];
while ($l = $res_local->fetch_assoc()) {
    $locais[] = $l['local'];
    $entregas[] = $l['total'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Análise Semanal - Cafeteria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --cor-primaria: #4A2C2A;
        }
    </style>
</head>

<body class="bg-gray-100">

    <header class="bg-[var(--cor-primaria)] text-white p-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Análise Semanal</h1>
        <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
        <?php if ($_SESSION['nivel_acesso'] == 2): ?>
    <a href="painel_gerente.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mt-6 inline-block">
        Voltar para a Página Inicial
    </a>
<?php endif; ?>
    </header>

    <main class="max-w-6xl mx-auto p-6">

        <!-- Formulário semana -->
        <form method="GET" class="bg-white p-4 rounded shadow flex gap-4 items-center mb-6">
            <label class="font-semibold text-[var(--cor-primaria)]">Selecione a Semana:</label>
            <input type="week" name="semana" value="<?= $semana ?>" class="border p-2 rounded">
            <button class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded">Atualizar</button>
            <button type="button" onclick="window.print()"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Imprimir Relatório
            </button>
        </form>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded shadow text-center">
                <h3 class="text-gray-600 font-semibold">Total Semana</h3>
                <p class="text-3xl font-bold text-red-600">R$ <?= number_format($total_semana, 2, ',', '.') ?></p>
            </div>
            <div class="bg-white p-6 rounded shadow text-center">
                <h3 class="text-gray-600 font-semibold">Itens Vendidos</h3>
                <p class="text-3xl font-bold text-blue-600"><?= $qtd_semana ?></p>
            </div>
            <div class="bg-white p-6 rounded shadow text-center">
                <h3 class="text-gray-600 font-semibold">Lucro Estimado (30%)</h3>
                <p class="text-3xl font-bold text-green-600">R$ <?= number_format($lucro_semana, 2, ',', '.') ?></p>
            </div>
        </div>

        <!-- Produtos mais vendidos -->
        <div class="bg-white p-6 rounded shadow mb-8">
            <h2 class="text-xl font-bold mb-3 text-[var(--cor-primaria)]">Produtos Mais Vendidos</h2>
            <canvas id="graficoProdutos"></canvas>
        </div>

        <!-- Locais com mais entregas -->
        <div class="bg-white p-6 rounded shadow mb-8">
            <h2 class="text-xl font-bold mb-3 text-[var(--cor-primaria)]">Locais com Mais Vendas</h2>
            <?php if (count($locais) > 0): ?>
                <canvas id="graficoLocais"></canvas>
            <?php else: ?>
                <p class="text-gray-600">Nenhuma venda registrada nesta semana.</p>
            <?php endif; ?>
        </div>

    </main>

    <script>
        // Produtos
        new Chart(document.getElementById('graficoProdutos'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($produtos) ?>,
                datasets: [{
                    label: 'Quantidade Vendida',
                    data: <?= json_encode($qtd_produtos) ?>,
                    backgroundColor: '#b63131aa'
                }]
            }
        });

        // Locais
        new Chart(document.getElementById('graficoLocais'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($locais) ?>,
                datasets: [{
                    label: 'Total de Vendas',
                    data: <?= json_encode($entregas) ?>,
                    backgroundColor: '#3b82f6aa'
                }]
            }
        });
    </script>

</body>

</html>