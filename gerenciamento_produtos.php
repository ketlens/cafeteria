<?php
session_start();
include('conexao.php');

// Verificar o acesso do usuário
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Redirecionar para a página inicial se o nível de acesso não for 1 ou 2
if ($_SESSION['nivel_acesso'] != 1 && $_SESSION['nivel_acesso'] != 2) {
    header("Location: index.php?alerta=acesso_negado");
    exit;
}

// Função para buscar produtos com estoque baixo
function produtos_estoque_baixo($conn)
{
    $sql = "
    SELECT p.id, p.nome, p.estoque, p.estoque_minimo, (p.estoque_minimo - p.estoque) AS reposicao
    FROM produtos p
    WHERE p.estoque < p.estoque_minimo
    ORDER BY reposicao DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->get_result();
}

// Buscar os produtos com estoque baixo
$produtos_result = produtos_estoque_baixo($conn);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Produtos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --cor-primaria: #4A2C2A;
        }
    </style>
</head>

<body class="bg-gray-100">

<header class="bg-[var(--cor-primaria)] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Gerenciamento de Produtos</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
</header>

<main class="max-w-7xl mx-auto p-6">

    <!-- Tabela de Produtos com Estoque Baixo -->
    <div class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-xl font-bold text-[var(--cor-primaria)] mb-4">Produtos com Estoque Baixo</h2>

        <!-- Tabela -->
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nome do Produto</th>
                    <th class="px-4 py-2 text-left">Estoque Atual</th>
                    <th class="px-4 py-2 text-left">Estoque Mínimo</th>
                    <th class="px-4 py-2 text-left">Faltam</th>
                    <th class="px-4 py-2 text-left">Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Verificar se existem produtos com estoque baixo
                if ($produtos_result->num_rows > 0) {
                    while ($row = $produtos_result->fetch_assoc()) {
                        // Estilo para destacar produtos com estoque muito baixo
                        $falta = $row['reposicao'];
                        $class = ($falta > 5) ? 'bg-red-200' : 'bg-yellow-200'; // Vermelho para falta crítica, amarelo para baixa

                        echo "<tr class='{$class}'>
                            <td class='px-4 py-2'>{$row['nome']}</td>
                            <td class='px-4 py-2'>{$row['estoque']}</td>
                            <td class='px-4 py-2'>{$row['estoque_minimo']}</td>
                            <td class='px-4 py-2'>{$falta}</td>
                            <td class='px-4 py-2'>
                                <button class='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600'>Repor Estoque</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='px-4 py-2 text-center text-gray-500'>Nenhum produto com estoque baixo.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</main>

</body>
</html>
