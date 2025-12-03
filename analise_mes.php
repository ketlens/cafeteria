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
   DEFINIR MÊS SELECIONADO E MÊS PASSADO
========================= */
$mes = $_GET['mes'] ?? date("Y-m");
list($ano, $num_mes) = explode("-", $mes);

$inicio_mes = date("Y-m-01", strtotime("$ano-$num_mes-01"));
$fim_mes = date("Y-m-t", strtotime($inicio_mes));

$inicio_mes_ant = date("Y-m-01", strtotime($inicio_mes . " -1 month"));
$fim_mes_ant = date("Y-m-t", strtotime($inicio_mes . " -1 month"));

/* =========================
   FUNÇÕES DE VALOR, QUANTIDADE E LUCRO
========================= */
function valor($conn, $inicio, $fim)
{
    $sql = "SELECT SUM(total) AS total FROM venda WHERE data_venda BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $inicio, $fim);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
}

function quantidade($conn, $inicio, $fim)
{
    $sql = "SELECT SUM(quantidade) AS qtd FROM venda WHERE data_venda BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $inicio, $fim);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['qtd'] ?? 0;
}

$total_mes = valor($conn, $inicio_mes, $fim_mes);
$qtd_mes = quantidade($conn, $inicio_mes, $fim_mes);
$lucro_mes = $total_mes * 0.30;

$total_mes_ant = valor($conn, $inicio_mes_ant, $fim_mes_ant);
$qtd_mes_ant = quantidade($conn, $inicio_mes_ant, $fim_mes_ant);

/* =========================
   Ranking dos Dias do Mês
========================= */
$sql_rank = "
SELECT DAY(data_venda) AS dia, SUM(total) AS total
FROM venda
WHERE data_venda BETWEEN ? AND ?
GROUP BY dia
ORDER BY total DESC";
$stmt = $conn->prepare($sql_rank);
$stmt->bind_param("ss", $inicio_mes, $fim_mes);
$stmt->execute();
$res_rank = $stmt->get_result();

$dias_rank = [];
$valores_rank = [];
while ($r = $res_rank->fetch_assoc()) {
    $dias_rank[] = $r['dia'];
    $valores_rank[] = $r['total'];
}

/* =========================
   Melhor Horário Médio de Vendas
========================= */
$sql_hora = "
SELECT HOUR(data_venda) AS hora, AVG(total) AS media
FROM venda
WHERE data_venda BETWEEN ? AND ?
GROUP BY hora
ORDER BY media DESC";
$stmt = $conn->prepare($sql_hora);
$stmt->bind_param("ss", $inicio_mes, $fim_mes);
$stmt->execute();
$res_horas = $stmt->get_result();

$horarios_med = [];
$valores_med = [];
while ($h = $res_horas->fetch_assoc()) {
    $horarios_med[] = $h['hora'] . "h";
    $valores_med[] = $h['media'];
}

/* =========================
   Produtos Mais Vendidos
========================= */
$sql_prod = "
SELECT p.nome, SUM(v.quantidade) AS qtd
FROM venda v
JOIN produtos p ON p.id = v.produto_id
WHERE v.data_venda BETWEEN ? AND ?
GROUP BY p.nome
ORDER BY qtd DESC";
$stmt = $conn->prepare($sql_prod);
$stmt->bind_param("ss", $inicio_mes, $fim_mes);
$stmt->execute();
$res_prod = $stmt->get_result();

$produtos = [];
$qtd_produtos = [];
while ($p = $res_prod->fetch_assoc()) {
    $produtos[] = $p['nome'];
    $qtd_produtos[] = $p['qtd'];
}

/* =========================
   Locais com Mais Vendas
========================= */
$sql_local = "
SELECT local, COUNT(*) AS total
FROM venda
WHERE data_venda BETWEEN ? AND ?
GROUP BY local
ORDER BY total DESC";
$stmt = $conn->prepare($sql_local);
$stmt->bind_param("ss", $inicio_mes, $fim_mes);
$stmt->execute();
$res_local = $stmt->get_result();

$locais = [];
$entregas = [];
while ($l = $res_local->fetch_assoc()) {
    $locais[] = $l['local'];
    $entregas[] = $l['total'];
}

/* =========================
   Comparação: Mês Atual x Mês Anterior
========================= */
$sql_comp = "
SELECT data_venda, SUM(total) AS total
FROM venda
WHERE data_venda BETWEEN ? AND ?
GROUP BY data_venda
ORDER BY data_venda";
$stmt = $conn->prepare($sql_comp);

// Mês atual
$stmt->bind_param("ss", $inicio_mes, $fim_mes);
$stmt->execute();
$res_atual = $stmt->get_result();
$mes_atual = [];
$mes_atual_val = [];
while ($c = $res_atual->fetch_assoc()) {
    $mes_atual[] = $c['data_venda'];
    $mes_atual_val[] = $c['total'];
}

// Mês anterior
$stmt->bind_param("ss", $inicio_mes_ant, $fim_mes_ant);
$stmt->execute();
$res_old = $stmt->get_result();
$mes_ant_val = [];
while ($c = $res_old->fetch_assoc()) {
    $mes_ant_val[] = $c['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Análise Mensal</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
<header class="bg-[#4A2C2A] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Análise Mensal</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
    <?php if ($_SESSION['nivel_acesso'] == 2): ?>
    <a href="painel_gerente.php" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mt-6 inline-block">
        Voltar para a Página Inicial
    </a>
<?php endif; ?>
</header>
    <main class="max-w-7xl mx-auto p-6">
    <!-- Formulário de Seleção de Mês e Botão de Imprimir -->
    <div class="flex justify-between items-center mb-8">
        <!-- Formulário de Seleção de Mês -->
        <form action="" method="get" class="flex space-x-4 items-center">
            <label for="mes" class="text-black">Selecione o Mês:</label>
            <input type="month" name="mes" id="mes" value="<?= $mes ?>" class="px-4 py-2 rounded border-none bg-white text-gray-700">
            <button type="submit" class=" text-white bg-green-600 px-4 py-2 rounded hover:bg-green-700">Atualizar</button>
        </form>
        
        <!-- Botão para Imprimir Relatório -->
        <button onclick="window.print()" class="text-white bg-blue-600 px-4 py-2 rounded hover:bg-blue-700">
            Imprimir Relatório
        </button>
    </div>

<!-- Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
<div class="bg-white p-6 rounded shadow text-center">
<h3 class="text-gray-600 font-semibold">Total do Mês</h3>
<p class="text-3xl font-bold text-red-600">R$ <?= number_format($total_mes,2,',','.') ?></p>
</div>
<div class="bg-white p-6 rounded shadow text-center">
<h3 class="text-gray-600 font-semibold">Itens Vendidos</h3>
<p class="text-3xl font-bold text-blue-600"><?= $qtd_mes ?></p>
</div>
<div class="bg-white p-6 rounded shadow text-center">
<h3 class="text-gray-600 font-semibold">Lucro Estimado (30%)</h3>
<p class="text-3xl font-bold text-green-600">R$ <?= number_format($lucro_mes,2,',','.') ?></p>
</div>
<div class="bg-white p-6 rounded shadow text-center">
<h3 class="text-gray-600 font-semibold">Comparado ao Mês Anterior</h3>
<p class="text-xl font-bold <?= ($total_mes >= $total_mes_ant) ? 'text-green-600' : 'text-red-600' ?>">
<?= ($total_mes >= $total_mes_ant ? "+" : "-") ?> <?= number_format(abs($total_mes - $total_mes_ant),2,',','.') ?></p>
</div>
</div>

<!-- Gráficos -->
<div class="bg-white p-6 rounded shadow mb-8">
<h2 class="text-xl font-bold text-[#4A2C2A] mb-2">Ranking dos Dias do Mês</h2>
<canvas id="graficoRankingDias" height="110"></canvas>
</div>

<div class="bg-white p-6 rounded shadow mb-8">
<h2 class="text-xl font-bold text-[#4A2C2A] mb-2">Melhor Horário Médio de Vendas</h2>
<canvas id="graficoHorarioMedio" height="110"></canvas>
</div>

<div class="bg-white p-6 rounded shadow mb-8">
<h2 class="text-xl font-bold text-[#4A2C2A] mb-2">Produtos Mais Vendidos</h2>
<canvas id="graficoProdutos" height="120"></canvas>
</div>

<div class="bg-white p-6 rounded shadow mb-8">
<h2 class="text-xl font-bold text-[#4A2C2A] mb-2">Locais com Mais Vendas</h2>
<canvas id="graficoLocais" height="100"></canvas>
</div>

<div class="bg-white p-6 rounded shadow mb-8">
<h2 class="text-xl font-bold text-[#4A2C2A] mb-2">Comparação: Mês Atual x Mês Anterior</h2>
<canvas id="graficoComparativo" height="120"></canvas>
</div>

</main>

<script>
new Chart(document.getElementById('graficoRankingDias'), {
type: 'bar',
data: {
labels: <?= json_encode($dias_rank) ?>,
datasets:[{
label:'Vendas (R$)',
data: <?= json_encode($valores_rank) ?>,
backgroundColor:'#6b423faa'
}]
}
});

new Chart(document.getElementById('graficoHorarioMedio'), {
type: 'bar',
data: {
labels: <?= json_encode($horarios_med) ?>,
datasets:[{
label:'Média Vendas (R$)',
data: <?= json_encode($valores_med) ?>,
backgroundColor:'#f87171aa'
}]
}
});

new Chart(document.getElementById('graficoProdutos'), {
type:'bar',
data:{
labels: <?= json_encode($produtos) ?>,
datasets:[{
label:'Quantidade',
data: <?= json_encode($qtd_produtos) ?>,
backgroundColor:'#60a5faaa'
}]
}
});

new Chart(document.getElementById('graficoLocais'), {
type:'bar',
data:{
labels: <?= json_encode($locais) ?>,
datasets:[{
label:'Vendas',
data: <?= json_encode($entregas) ?>,
backgroundColor:'#34d399aa'
}]
}
});

new Chart(document.getElementById('graficoComparativo'), {
type:'line',
data:{
labels: <?= json_encode($mes_atual) ?>,
datasets:[
{
label:'Mês Atual',
data: <?= json_encode($mes_atual_val) ?>,
borderColor:'#4A2C2A',
backgroundColor:'#4A2C2A33',
tension:0.2
},
{
label:'Mês Anterior',
data: <?= json_encode($mes_ant_val) ?>,
borderColor:'#f87171',
backgroundColor:'#f8717133',
tension:0.2
}
]
}
});
</script>
</body>
</html>
