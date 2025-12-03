<?php
session_start();
require 'conexao.php';

// --- Segurança ---
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel_acesso'] != 1) {
    header("Location: index.php?alerta=acesso_negado");
    exit;
}

$mensagem = '';
$tipoMensagem = '';

// Buscar produtos
$produtos = [];
$res = $conn->query("SELECT id, nome FROM produtos ORDER BY nome ASC");
while ($p = $res->fetch_assoc()) {
    $produtos[] = $p;
}

// Ações: criar / editar / deletar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $acao = $_POST['acao'];

    $codigo = $_POST['codigo'];
    $descricao = $_POST['descricao'];
    $desconto = $_POST['desconto'];
    $tipo = $_POST['tipo'];
    $produto = $_POST['produto_id'] !== "" ? $_POST['produto_id'] : NULL;
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];
    $limite = $_POST['limite_por_usuario'];

    // Criar
    if ($acao === 'criar') {
        $stmt = $conn->prepare("
            INSERT INTO cupom (codigo, descricao, desconto, tipo, produto_id, data_inicio, data_fim, limite_por_usuario, ativo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, TRUE)
        ");

        $stmt->bind_param("ssdsiisi", $codigo, $descricao, $desconto, $tipo, $produto, $data_inicio, $data_fim, $limite);

        if ($stmt->execute()) {
            $mensagem = "Cupom criado com sucesso!";
            $tipoMensagem = "sucesso";
        }
    }

    // Editar
    if ($acao === 'editar') {
        $id = $_POST['cupom_id'];

        $stmt = $conn->prepare("
            UPDATE cupom SET codigo=?, descricao=?, desconto=?, tipo=?, produto_id=?, data_inicio=?, data_fim=?, limite_por_usuario=?
            WHERE id=?
        ");

        $stmt->bind_param("ssdsiisii", $codigo, $descricao, $desconto, $tipo, $produto, $data_inicio, $data_fim, $limite, $id);

        if ($stmt->execute()) {
            $mensagem = "Cupom atualizado!";
            $tipoMensagem = "sucesso";
        }
    }
}

// Deletar
if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    $conn->query("DELETE FROM cupom WHERE id = $id");
    $mensagem = "Cupom removido!";
    $tipoMensagem = 'sucesso';
}

// Buscar cupons
$cupons = $conn->query("SELECT cupom.*, produtos.nome AS produto_nome 
                        FROM cupom 
                        LEFT JOIN produtos ON produtos.id = cupom.produto_id
                        ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Cupons</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<header class="bg-blue-600 text-white p-4 flex justify-between">
    <h1 class="text-2xl font-bold">Gestão de Cupons</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded">Voltar</a>
</header>

<main class="max-w-6xl mx-auto p-6">

<?php if ($mensagem): ?>
    <div class="p-4 mb-4 rounded <?= $tipoMensagem === 'sucesso' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' ?>">
        <?= $mensagem ?>
    </div>
<?php endif; ?>

<!-- Criar cupom -->
<section class="bg-white p-6 rounded shadow mb-8">
    <h2 class="text-xl font-bold mb-4">Criar Cupom</h2>

    <form method="POST" class="space-y-3">
        <input type="hidden" name="acao" value="criar">

        <div>
            <label>Código:</label>
            <input name="codigo" required class="border p-2 w-full rounded">
        </div>

        <div>
            <label>Descrição:</label>
            <textarea name="descricao" class="border p-2 w-full rounded"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Desconto:</label>
                <input type="number" name="desconto" required class="border p-2 w-full rounded" step="0.01">
            </div>
            <div>
                <label>Tipo:</label>
                <select name="tipo" class="border p-2 w-full rounded" required>
                    <option value="percentual">Percentual (%)</option>
                    <option value="valor">Valor (R$)</option>
                </select>
            </div>
        </div>

        <div>
            <label>Produto (opcional):</label>
            <select name="produto_id" class="border p-2 w-full rounded">
                <option value="">Aplicar em qualquer produto</option>
                <?php foreach ($produtos as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
                <?php endforeach; ?>
            </select>
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

        <div>
            <label>Limite por usuário:</label>
            <input type="number" name="limite_por_usuario" min="1" value="1" class="border p-2 w-full rounded">
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Criar Cupom</button>
    </form>
</section>


<!-- Lista de cupons -->
<section class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Cupons Cadastrados</h2>

    <table class="w-full table-auto">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">ID</th>
                <th class="p-2">Código</th>
                <th class="p-2">Produto</th>
                <th class="p-2">Desconto</th>
                <th class="p-2">Período</th>
                <th class="p-2">Limite</th>
                <th class="p-2 text-center">Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($c = $cupons->fetch_assoc()): ?>
                <tr class="border-b">
                    <td class="p-2"><?= $c['id'] ?></td>
                    <td class="p-2"><?= $c['codigo'] ?></td>
                    <td class="p-2"><?= $c['produto_nome'] ?: 'Todos' ?></td>
                    <td class="p-2">
                        <?= $c['tipo'] === 'percentual' ? $c['desconto'] . '%' : 'R$ ' . number_format($c['desconto'],2,',','.') ?>
                    </td>
                    <td class="p-2">
                        <?= date('d/m/Y', strtotime($c['data_inicio'])) ?>
                        —
                        <?= date('d/m/Y', strtotime($c['data_fim'])) ?>
                    </td>
                    <td class="p-2 text-center"><?= $c['limite_por_usuario'] ?></td>

                    <td class="p-2 text-center">
                        <a href="editar_cupom.php?id=<?= $c['id'] ?>" class="px-3 py-1 bg-yellow-500 text-white rounded">Editar</a>
                        <a href="gestao_cupons.php?deletar=<?= $c['id'] ?>" class="px-3 py-1 bg-red-600 text-white rounded" onclick="return confirm('Excluir cupom?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

</main>

</body>
</html>
