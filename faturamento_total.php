<?php
session_start();
include('conexao.php');

// Verificar o acesso do usuário
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Redirecionar se o nível de acesso não for 1 ou 2
if ($_SESSION['nivel_acesso'] != 1 && $_SESSION['nivel_acesso'] != 2) {
    header("Location: index.php?alerta=acesso_negado");
    exit;
}

// Verificar o ano selecionado
$ano = $_GET['ano'] ?? date("Y");

/* ================================
   Função CORRIGIDA — tabela VENDA
================================ */
function faturamento_por_mes($conn, $ano)
{
    $sql = "
    SELECT 
        MONTH(data_venda) AS mes, 
        SUM(total) AS faturamento
    FROM venda
    WHERE YEAR(data_venda) = ?
    GROUP BY MONTH(data_venda)
    ORDER BY mes ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ano);
    $stmt->execute();
    return $stmt->get_result();
}

$faturamento_result = faturamento_por_mes($conn, $ano);

/* ================================
   Faturamento ANUAL — corrigido
================================ */
$sql_ano = "
SELECT SUM(total) AS faturamento_total
FROM venda
WHERE YEAR(data_venda) = ?
";
$stmt_ano = $conn->prepare($sql_ano);
$stmt_ano->bind_param("s", $ano);
$stmt_ano->execute();
$faturamento_anual = $stmt_ano->get_result()->fetch_assoc()['faturamento_total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Faturamento Total - <?= $ano ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --cor-primaria: #4A2C2A;
        }
    </style>
</head>

<body class="bg-gray-100">

<header class="bg-[var(--cor-primaria)] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Faturamento Total - Ano <?= $ano ?></h1>

    <div class="flex gap-4">
        <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>

        <?php if ($_SESSION['nivel_acesso'] == 2): ?>
            <a href="painel_gerente.php" class="bg-yellow-500 px-4 py-2 rounded hover:bg-yellow-600">
                Painel do Gerente
            </a>
        <?php endif; ?>
    </div>
</header>

<main class="max-w-7xl mx-auto p-6">

    <!-- Formulário para selecionar o ano -->
    <form method="GET" class="bg-white p-4 rounded shadow flex gap-4 items-center mb-6">
        <label class="text-[var(--cor-primaria)] font-semibold">Selecione o Ano:</label>
        <input type="number" name="ano" value="<?= $ano ?>" class="p-2 border rounded w-32" min="2000" max="<?= date('Y') ?>">
        <button class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded hover:bg-[#6b423f]">
            Atualizar
        </button>
        <button onclick="window.print()" class="text-white bg-blue-600 px-4 py-2 rounded hover:bg-blue-700">
            Imprimir Relatório
        </button>
    </form>

    <!-- Tabela de Faturamento Mensal -->
    <div class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-xl font-bold text-[var(--cor-primaria)] mb-4">
            Faturamento Mensal de <?= $ano ?>
        </h2>

        <table class="min-w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Mês</th>
                    <th class="px-4 py-2 text-left">Faturamento (R$)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $meses = [
                    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
                ];
                
                // Preenche meses vazios com 0
                $mes_faturamento = array_fill(1, 12, 0);

                while ($row = $faturamento_result->fetch_assoc()) {
                    $mes_faturamento[$row['mes']] = $row['faturamento'];
                }

                for ($i = 1; $i <= 12; $i++) {
                    echo "
                    <tr>
                        <td class='px-4 py-2'>{$meses[$i - 1]}</td>
                        <td class='px-4 py-2'>R$ " . number_format($mes_faturamento[$i], 2, ',', '.') . "</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Faturamento Anual -->
    <div class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-xl font-bold text-[var(--cor-primaria)] mb-4">Faturamento Total Anual</h2>
        <p class="text-3xl font-bold text-green-600">
            R$ <?= number_format($faturamento_anual, 2, ',', '.') ?>
        </p>
    </div>

</main>

</body>
</html>
