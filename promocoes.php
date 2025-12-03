<?php
session_start();
include('conexao.php');

// Permissão de admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 1) {
    header("Location: login.php");
    exit;
}

$mensagem = "";
$tipoMensagem = "";

// CADASTRAR PROMOÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $produto_id = $_POST['produto_id'];
    $desconto = $_POST['desconto_percentual'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    if ($data_inicio > $data_fim) {
        $mensagem = "A data de início não pode ser maior que a data de fim.";
        $tipoMensagem = "erro";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO promocoes (produto_id, desconto_percentual, data_inicio, data_fim, ativo)
            VALUES (?, ?, ?, ?, TRUE)
        ");
        $stmt->bind_param("idss", $produto_id, $desconto, $data_inicio, $data_fim);

        if ($stmt->execute()) {
            $mensagem = "Promoção cadastrada com sucesso!";
            $tipoMensagem = "sucesso";
        } else {
            $mensagem = "Erro ao cadastrar promoção.";
            $tipoMensagem = "erro";
        }
    }
}

// DELETAR PROMOÇÃO
if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    $conn->query("DELETE FROM promocoes WHERE id = $id");

    $mensagem = "Promoção excluída!";
    $tipoMensagem = "sucesso";
}

// BUSCAR PRODUTOS
$produtos = $conn->query("SELECT * FROM produtos ORDER BY nome ASC");

// BUSCAR PROMOÇÕES
$promocoes = $conn->query("
    SELECT promocoes.*, produtos.nome AS produto_nome 
    FROM promocoes
    INNER JOIN produtos ON produtos.id = promocoes.produto_id
    ORDER BY promocoes.id DESC
");

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gestão de Promoções</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<header class="bg-[#4A2C2A] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Promoções</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
</header>

<main class="max-w-6xl mx-auto p-6">

    <?php if ($mensagem): ?>
    <div class="p-4 mb-4 rounded <?= $tipoMensagem == 'sucesso' ? 'bg-green-200' : 'bg-red-200' ?>">
        <?= $mensagem ?>
    </div>
    <?php endif; ?>

    <!-- CADASTRAR PROMOÇÃO -->
    <section class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-xl font-bold mb-4">Cadastrar Promoção</h2>

        <form method="POST" class="space-y-3">

            <div>
                <label>Produto:</label>
                <select name="produto_id" class="border p-2 w-full rounded" required>
                    <?php while ($p = $produtos->fetch_assoc()): ?>
                        <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label>Desconto (%):</label>
                <input type="number" step="0.01" name="desconto_percentual" class="border p-2 w-full rounded" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Data início:</label>
                    <input type="date" name="data_inicio" required class="border p-2 w-full rounded">
                </div>
                <div>
                    <label>Data fim:</label>
                    <input type="date" name="data_fim" required class="border p-2 w-full rounded">
                </div>
            </div>

            <button class="bg-[#4A2C2A] text-white px-4 py-2 rounded">Cadastrar Promoção</button>
        </form>
    </section>

    <!-- LISTA DE PROMOÇÕES -->
    <section class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Promoções Cadastradas</h2>

        <table class="w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Produto</th>
                    <th class="p-2">Desconto</th>
                    <th class="p-2">Período</th>
                    <th class="p-2">Status</th>
                    <th class="p-2 text-center">Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($promo = $promocoes->fetch_assoc()): 
                        $hoje = date("Y-m-d");
                        $ativo = ($hoje >= $promo['data_inicio'] && $hoje <= $promo['data_fim']);
                ?>
                <tr class="border-b">
                    <td class="p-2"><?= $promo['id'] ?></td>
                    <td class="p-2"><?= $promo['produto_nome'] ?></td>
                    <td class="p-2"><?= $promo['desconto_percentual'] ?>%</td>
                    <td class="p-2"><?= date('d/m/Y', strtotime($promo['data_inicio'])) ?> → <?= date('d/m/Y', strtotime($promo['data_fim'])) ?></td>
                    <td class="p-2 <?= $ativo ? 'text-green-600' : 'text-red-600' ?>">
                        <?= $ativo ? 'Ativa' : 'Expirou' ?>
                    </td>
                    <td class="p-2 text-center">
                        <a href="gestao_promocoes.php?deletar=<?= $promo['id'] ?>" 
                           class="bg-red-600 text-white px-3 py-1 rounded"
                           onclick="return confirm('Excluir promoção?')">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </section>

</main>

</body>
</html>
