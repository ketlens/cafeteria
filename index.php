<?php
session_start(); // Início da sessão
require 'conexao.php'; // Inclui o arquivo de conexão

// Variáveis de estado do usuário
$is_admin = isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 1;
$is_logged_in = isset($_SESSION['usuario_id']);

// --- LÓGICA PHP PARA AGRUPAR PRODUTOS POR CATEGORIA ---

// Categorias que queremos exibir na ordem
$categorias_do_menu = ['Unitário', 'Combo', 'Cesta']; 
$produtos_agrupados = [];

// URL de fallback (usada se a imagem principal falhar - mantida externa para garantir que funcione)
$placeholder_url = 'https://via.placeholder.com/400x200/cccccc/333333?text=IMAGEM+FALHOU'; 

if (isset($conn)) {
    // Busca todos os produtos ordenados pela categoria e ID
    $resultado = $conn->query("SELECT * FROM produtos ORDER BY categoria, id DESC");
    
    while ($p = $resultado->fetch_assoc()) {
        $categoria = $p['categoria'] ?? 'Unitário'; // Garante uma categoria padrão
        
        // Se a categoria existir na lista definida, armazena
        if (in_array($categoria, $categorias_do_menu)) {
            $produtos_agrupados[$categoria][] = $p;
        } else {
            // Se for uma categoria não mapeada, coloca em Unitário por segurança
            $produtos_agrupados['Unitário'][] = $p;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Café & Aroma ☕</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            /* Marrom Café Escuro */
            --cor-primaria: #4A2C2A; 
            /* Marrom um pouco mais claro para hover */
            --cor-hover: #5d3835;
        }
        .nav-link:hover {
            color: #dc2626; /* Cor vermelha para hover */
        }
    </style>
</head>
<body class="bg-stone-50 text-gray-800 font-sans">
    
    <header class="bg-white shadow-lg sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-6">
            
            <div class="py-4 flex justify-between items-center">
                <h1 class="text-3xl font-extrabold text-[var(--cor-primaria)]">
                    ☕ Café & <span class="text-red-600">Aroma</span>
                </h1>
                
                <div class="flex gap-3 items-center">
                    <?php if ($is_logged_in): ?>
                        
                        <?php if ($is_admin): ?>
                            <a href="indexadim.php" class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[var(--cor-hover)] transition shadow-md">Painel Admin</a>
                        <?php else: ?>
                            <a href="carrinho.php" class="border border-[var(--cor-primaria)] text-[var(--cor-primaria)] px-4 py-2 rounded-lg font-semibold hover:bg-[var(--cor-primaria)] hover:text-white transition shadow-sm">
                                🛒 Carrinho
                            </a>
                        <?php endif; ?>
                        
                        <a href="logout.php" class="border border-gray-400 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition">Sair</a>
                        
                    <?php else: ?>
                        <a href="login.php" class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[var(--cor-hover)] transition shadow-lg">Entrar / Cadastrar</a>
                    <?php endif; ?>
                </div>
            </div>

            <nav class="hidden md:flex space-x-8 text-lg font-medium text-gray-700 border-t border-gray-100">
                <a href="index.php" class="nav-link py-3 text-red-600 border-b-2 border-red-600">Início</a>
                <a href="menu.php" class="nav-link py-3 hover:text-red-600 transition">Menu</a>
                <a href="sobre.php" class="nav-link py-3 hover:text-red-600 transition">Sobre</a>
            </nav>
            
        </div>
    </header>

    <section class="bg-[var(--cor-primaria)] text-white py-20 text-center shadow-xl">
        <h2 class="text-5xl font-extrabold mb-4 tracking-tight">O sabor do café que desperta momentos </h2>
        <p class="text-xl opacity-90 mb-8">Conheça nossa seleção especial de cafés, doces e quitutes fresquinhos, com entrega rápida.</p>
        <a href="menu.php" class="bg-red-600 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-red-700 transition transform hover:scale-105">
            Explorar Menu Completo
        </a>
    </section>

    <main id="produtos" class="max-w-7xl mx-auto px-6 py-16">
        <h2 class="text-4xl font-bold text-center mb-12 text-[var(--cor-primaria)]">Nossas Delícias</h2>
        
        <?php if (!isset($conn)): ?>
            <p class='text-center text-red-500'>Erro: A conexão com o banco de dados não está disponível.</p>
        <?php elseif (empty($produtos_agrupados)): ?>
             <p class='text-center text-gray-600 text-xl'>Nenhum produto cadastrado no momento.</p>
        <?php endif; ?>

        <?php foreach ($categorias_do_menu as $categoria_nome): ?>
            
            <?php 
            // Verifica se há produtos nessa categoria
            if (empty($produtos_agrupados[$categoria_nome])) continue; 
            
            $slug_categoria = strtolower(str_replace(' ', '-', $categoria_nome));
            $limite_produtos = 5; // Limita a 5 produtos por categoria na tela principal
            ?>
            
            <section class="mb-16 pt-4" id="<?= $slug_categoria ?>">
                
                <h3 class="text-3xl font-bold mb-6 text-gray-800 border-b-4 border-red-600 pb-2 flex justify-between items-center">
                    <?= htmlspecialchars($categoria_nome) ?>
                    <a href="produtos.php?categoria=<?= urlencode($categoria_nome) ?>" class="text-base text-red-600 font-semibold hover:text-red-700 transition">
                        Ver Mais &rarr;
                    </a>
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                    <?php 
                    $contador = 0;
                    // Itera sobre os produtos da categoria
                    foreach ($produtos_agrupados[$categoria_nome] as $p): 
                        if ($contador >= $limite_produtos) break; // Para o loop após 5 itens
                        $contador++;
                    ?>
                        <div class="bg-white rounded-xl shadow-xl overflow-hidden p-4 border border-gray-100 hover:shadow-2xl hover:scale-[1.02] transition duration-300 flex flex-col justify-between">
                            <div>
                                <img 
                                    src="<?= htmlspecialchars($p['imagem']) ?>" 
                                    alt="<?= htmlspecialchars($p['nome']) ?>" 
                                    class="rounded-lg w-full h-36 object-cover mb-3 shadow-md"
                                    onerror="this.onerror=null; this.src='<?= $placeholder_url ?>'; this.style.objectFit='contain';"
                                >
                                <h4 class="text-xl font-bold text-gray-900 mb-1 leading-tight"><?= htmlspecialchars($p['nome']) ?></h4>
                                <p class="text-xs text-gray-500 h-8 overflow-hidden mb-3"><?= htmlspecialchars($p['descricao']) ?></p>
                                
                                <p class="font-bold text-sm text-gray-500 bg-gray-100 inline-block px-2 py-0.5 rounded-full mt-1">
                                    <?= htmlspecialchars($p['categoria'] ?? 'Unitário') ?>
                                </p>
                            </div>

                            <div class="mt-4 pt-2 border-t border-gray-100">
                                <p class="font-extrabold text-2xl text-red-600">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                                
                                <div class="mt-3">
                                    <?php if ($is_logged_in && !$is_admin): ?>
                                        <form action="adicionar_carrinho.php" method="POST" class="flex gap-2 items-center">
                                            <input type="hidden" name="produto_id" value="<?= $p['id'] ?>">
                                            <input type="number" name="quantidade" value="1" min="1" class="w-12 border border-gray-300 p-1.5 rounded-lg text-center text-sm font-medium">
                                            <button type="submit" class="w-full bg-red-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-red-700 transition shadow-md text-sm">
                                                Pedir
                                            </button>
                                        </form>
                                    <?php elseif (!$is_logged_in): ?>
                                        <a href="login.php" class="block text-center text-sm bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                                            Login para pedir
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($produtos_agrupados[$categoria_nome]) > $limite_produtos): ?>
                    <div class="text-center mt-8">
                        <a href="menu.php?categoria=<?= urlencode($categoria_nome) ?>" class="bg-red-600 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-red-700 transition transform hover:scale-105">
                            Ver todos os itens de <?= htmlspecialchars($categoria_nome) ?>
                        </a>
                    </div>
                <?php endif; ?>

            </section>
        <?php endforeach; ?>
        
    </main>

  <!--   <footer class="bg-white-900 text-white-300 text-center py-8 mt-16">
        <div class="max-w-7xl mx-auto px-6">
            <p>© 2025 Café & aroma.</p>
            <p class="text-sm mt-1">Desenvolvido com paixão pelo café.</p> ad-->
        </div>
    </footer>
</body>
</html>