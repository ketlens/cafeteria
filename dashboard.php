<?php
session_start();
include('conexao.php');

// 1. VERIFICAÇÃO DE SEGURANÇA: DEVE ESTAR LOGADO
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// 2. VERIFICAÇÃO DE PERMISSÃO: DEVE SER ADMIN (nível 1)
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] != 1) {
    header("Location: index.php?alerta=acesso_negado"); // Redireciona para a loja se não for Admin
    exit;
}

// LÓGICA CRUD COMPLETA (Adicionar, Remover, Editar)
if (isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    
    // As novas categorias permitidas
    $categorias_validas = ['Unitário', 'Combo', 'Cesta']; 
    
    // Simplificando o binding de dados para evitar repetição
    $nome = $_POST['nome'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $preco = $_POST['preco'] ?? 0.00;
    // OTIMIZAÇÃO: Filtra para remover espaços desnecessários da URL
    $imagem = trim($_POST['imagem'] ?? ''); 
    $categoria = $_POST['categoria'] ?? 'Unitário';

    if (!in_array($categoria, $categorias_validas)) {
         $categoria = 'Unitário';
    }
    
    if ($acao === 'adicionar') {
        
        $stmt = $conn->prepare("INSERT INTO produtos (nome, descricao, preco, imagem, categoria) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdds", $nome, $descricao, $preco, $imagem, $categoria);
        $stmt->execute();
        
    } elseif ($acao === 'remover') {
        $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        
    } elseif ($acao === 'editar') { 
        $stmt = $conn->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, imagem = ?, categoria = ? WHERE id = ?");
        $stmt->bind_param("ssddsi", $nome, $descricao, $preco, $imagem, $categoria, $_POST['id']);
        $stmt->execute();
    }
    
    header("Location: dashboard.php");
    exit;
}

$produtos = $conn->query("SELECT * FROM produtos ORDER BY id DESC");

// Variável com o URL do placeholder (AGORA USA UM ENDEREÇO LOCAL MAIS SEGURO)
$placeholder_url = 'https://via.placeholder.com/400x200/cccccc/333333?text=IMAGEM+FALHOU'; 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel - Cafeteria Expresso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --cor-primaria: #4A2C2A; 
            --cor-hover: #5d3835;
        }
        .edit-form {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <header class="bg-[var(--cor-primaria)] text-white p-4 flex justify-between items-center shadow-lg">
        <h1 class="text-2xl font-bold">Painel de Administração (<?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin') ?>)</h1>
        <div class="flex gap-4">
            <a href="indexadim.php" class="text-white bg-[var(--cor-hover)] px-4 py-2 rounded-lg hover:bg-[#744a47] transition shadow">Voltar Painel principal</a>
            <a href="index.php" class="text-white bg-[var(--cor-hover)] px-4 py-2 rounded-lg hover:bg-[#744a47] transition shadow">Voltar à Loja</a>
            <a href="logout.php" class="text-white bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700 transition shadow">Sair</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6">
        <h2 class="text-3xl font-extrabold mb-6 text-[var(--cor-primaria)]">Gestão de Produtos</h2>
        
        <div class="bg-white shadow-xl rounded-lg p-6 mb-8 border-t-4 border-red-600">
            <h3 class="text-xl font-semibold mb-4 text-gray-700">Adicionar Novo Produto</h3>
            <form method="POST" class="grid grid-cols-6 gap-4 items-end">
                <input type="hidden" name="acao" value="adicionar">
                
                <input type="text" name="nome" placeholder="Nome do Produto" required class="border p-3 rounded-lg col-span-2">
                
                <input type="text" name="descricao" placeholder="Descrição curta" required class="border p-3 rounded-lg col-span-4">
                
                <input type="number" step="0.01" name="preco" placeholder="Preço (ex: 12.50)" required class="border p-3 rounded-lg col-span-1">
                
                <select name="categoria" required class="border p-3 rounded-lg col-span-1 bg-white">
                    <option value="Unitário">Unitário</option>
                    <option value="Combo">Combo</option>
                    <option value="Cesta">Cesta</option>
                </select>

                <input type="text" name="imagem" placeholder="URL da imagem (ex: img/cafe.jpg ou URL completa)" required class="border p-3 rounded-lg col-span-3">
                
                <button class="bg-red-600 text-white p-3 rounded-lg hover:bg-red-700 col-span-1 font-semibold transition shadow-md">Adicionar</button>
            </form>
        </div>

        <h3 class="text-2xl font-semibold mb-4 text-[var(--cor-primaria)]">Produtos Existentes (<?= $produtos->num_rows ?>)</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php while ($p = $produtos->fetch_assoc()): ?>
                <div class="bg-white shadow-xl rounded-lg p-4 flex flex-col justify-between" id="produto-<?= $p['id'] ?>">
                    <div>
                        <img 
                            src="<?= htmlspecialchars($p['imagem']) ?>" 
                            alt="<?= htmlspecialchars($p['nome']) ?>" 
                            class="h-40 w-full object-cover rounded-lg mb-3 border border-gray-200"
                            onerror="this.onerror=null; this.src='<?= $placeholder_url ?>'; this.style.objectFit='contain';"
                        >
                        <h3 class="font-bold text-xl text-[var(--cor-primaria)]"><?= htmlspecialchars($p['nome']) ?></h3>
                        <p class="text-sm text-gray-500 font-medium bg-gray-200 inline-block px-2 py-0.5 rounded-full mb-1">
                            Categoria: <?= htmlspecialchars($p['categoria'] ?? 'Não Definido') ?>
                        </p>
                        <p class="text-sm text-gray-600 mt-1 mb-3"><?= htmlspecialchars($p['descricao']) ?></p>
                        <p class="text-2xl text-red-600 font-extrabold">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button onclick="toggleEditForm(<?= $p['id'] ?>)" class="flex-1 bg-[var(--cor-primaria)] text-white px-3 py-2 rounded-lg hover:bg-[var(--cor-hover)] text-sm font-semibold transition">
                            Editar
                        </button>
                        
                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja remover o produto: <?= htmlspecialchars($p['nome']) ?>?')" class="flex-1">
                            <input type="hidden" name="acao" value="remover">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="w-full bg-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-400 text-sm font-semibold transition">
                                Remover
                            </button>
                        </form>
                    </div>

                    <div id="edit-form-<?= $p['id'] ?>" class="edit-form mt-4 p-4 border border-blue-200 bg-blue-50 rounded-lg">
                        <h4 class="font-bold text-lg mb-3 text-blue-700">Editar <?= htmlspecialchars($p['nome']) ?></h4>
                        <form method="POST" class="space-y-3">
                            <input type="hidden" name="acao" value="editar">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            
                            <input type="text" name="nome" value="<?= htmlspecialchars($p['nome']) ?>" placeholder="Nome" required 
                                class="w-full p-2 border rounded-lg">
                            
                            <textarea name="descricao" placeholder="Descrição" required 
                                class="w-full p-2 border rounded-lg h-16 resize-none"><?= htmlspecialchars($p['descricao']) ?></textarea>
                            
                            <input type="number" step="0.01" name="preco" value="<?= htmlspecialchars($p['preco']) ?>" placeholder="Preço" required 
                                class="w-full p-2 border rounded-lg">
                            
                            <select name="categoria" required class="w-full p-2 border rounded-lg bg-white">
                                <option value="Unitário" <?= ($p['categoria'] === 'Unitário') ? 'selected' : '' ?>>Unitário</option>
                                <option value="Combo" <?= ($p['categoria'] === 'Combo') ? 'selected' : '' ?>>Combo</option>
                                <option value="Cesta" <?= ($p['categoria'] === 'Cesta') ? 'selected' : '' ?>>Cesta</option>
                            </select>

                            <input type="text" name="imagem" value="<?= htmlspecialchars($p['imagem']) ?>" placeholder="URL da Imagem" required 
                                class="w-full p-2 border rounded-lg">
                                
                            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 font-semibold transition">
                                Salvar Alterações
                            </button>
                            <button type="button" onclick="toggleEditForm(<?= $p['id'] ?>)" class="w-full bg-gray-400 text-white p-2 rounded-lg hover:bg-gray-500 font-semibold transition mt-1">
                                Cancelar
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script>
        // Função para mostrar/esconder o formulário de edição
        function toggleEditForm(produtoId) {
            const form = document.getElementById('edit-form-' + produtoId);
            if (form.classList.contains('edit-form')) {
                form.classList.remove('edit-form');
            } else {
                form.classList.add('edit-form');
            }
        }
    </script>
</body>
</html>