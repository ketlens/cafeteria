<?php
session_start();
require 'conexao.php';

// VARIÁVEIS DE USUÁRIO
$is_admin = isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 1;
$is_logged_in = isset($_SESSION['usuario_id']);

// CATEGORIAS DO MENU
$categorias_do_menu = ['Unitário', 'Combo', 'Cesta', 'Doces'];
$placeholder_url = 'https://via.placeholder.com/400x200/cccccc/333333?text=IMAGEM+FALHOU';

// --------------------------
// CONSULTA DOS PRODUTOS + PROMOÇÕES ATIVAS
// --------------------------
$produtos_agrupados = [];
$promocoes = [];
$mais_vendidos = [];
$todos_produtos = [];

$query = "
    SELECT 
        p.*,
        pr.desconto_percentual,
        pr.data_inicio AS promo_inicio,
        pr.data_fim AS promo_fim
    FROM produtos p
    LEFT JOIN promocoes pr
        ON pr.produto_id = p.id
        AND pr.ativo = 1
        AND CURDATE() BETWEEN pr.data_inicio AND pr.data_fim
    ORDER BY p.nome ASC
";

$resultado = $conn->query($query);

while ($p = $resultado->fetch_assoc()) {

    $id = $p['id'];
    $todos_produtos[$id] = $p;

    // Agrupamento por categoria
    $categoria = $p['categoria'] ?? 'Unitário';
    if (!in_array($categoria, $categorias_do_menu)) {
        $categoria = 'Unitário';
    }
    $produtos_agrupados[$categoria][] = $p;

    // Detectar promoções REAIS
    if (!empty($p['desconto_percentual'])) {
        $promocoes[] = $p;
    }

    // Exemplo simples de Mais Vendidos
    if ($p['preco'] > 15) {
        $mais_vendidos[] = $p;
    }
}

$promocoes = array_slice($promocoes, 0, 5);
$mais_vendidos = array_slice($mais_vendidos, 0, 5);

// ------------------------------
// FUNÇÃO PARA RENDERIZAR UM CARD
// ------------------------------
function renderizar_card($p, $is_logged_in, $is_admin, $placeholder_url)
{
    $nome = htmlspecialchars($p['nome']);
    $descricao = htmlspecialchars($p['descricao']);
    $categoria = htmlspecialchars($p['categoria']);
    $imagem = htmlspecialchars($p['imagem']);

    $temPromo = !empty($p['desconto_percentual']);

    // PREÇOS
    if ($temPromo) {
        $preco_original = $p['preco'];
        $desconto = $p['desconto_percentual'];
        $preco_final = $preco_original - ($preco_original * ($desconto / 100));

        $preco_html = "
            <span class='text-xs font-bold text-white bg-red-600 px-2 py-1 rounded-md inline-block mb-1'>
                🔥 {$desconto}% OFF
            </span>
            <p class='line-through text-gray-500 text-lg'>R$ " . number_format($preco_original, 2, ',', '.') . "</p>
            <p class='font-extrabold text-2xl text-green-600'>R$ " . number_format($preco_final, 2, ',', '.') . "</p>
        ";
    } else {
        $preco_html = "
            <p class='font-extrabold text-2xl text-red-600'>R$ " . number_format($p['preco'], 2, ',', '.') . "</p>
        ";
    }

    return "
    <div class='bg-white rounded-xl shadow-xl overflow-hidden p-4 border border-gray-100 hover:shadow-2xl hover:scale-[1.02] transition duration-300 flex flex-col justify-between'>
        
        <div>
            <img src='{$imagem}' alt='{$nome}' 
                class='rounded-lg w-full h-36 object-cover mb-3 shadow-md'
                onerror=\"this.src='{$placeholder_url}'; this.style.objectFit='contain';\"
            >

            <h4 class='text-xl font-bold text-gray-900 mb-1'>{$nome}</h4>

            <p class='text-xs text-gray-500 h-8 overflow-hidden mb-3'>{$descricao}</p>

            <p class='font-bold text-sm text-gray-500 bg-gray-100 inline-block px-2 py-0.5 rounded-full'>
                {$categoria}
            </p>
        </div>

        <div class='mt-4 pt-2 border-t border-gray-100'>
            {$preco_html}

            <div class='mt-3'>
                " . (
                    $is_logged_in && !$is_admin ?
                    "
                    <form action='adicionar_carrinho.php' method='POST' class='flex gap-2 items-center'>
                        <input type='hidden' name='produto_id' value='{$p['id']}'>
                        <input type='number' name='quantidade' value='1' min='1' 
                            class='w-12 border border-gray-300 p-1.5 rounded-lg text-center text-sm font-medium'>
                        <button type='submit' class='w-full bg-red-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-red-700 transition shadow-md text-sm'>
                            Pedir
                        </button>
                    </form>
                    "
                    :
                    (!$is_logged_in ?
                        "<a href='login.php' class='block text-center text-sm bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition'>Login para pedir</a>"
                    : "")
                ) . "
            </div>

        </div>

    </div>
    ";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Menu Completo - Café & Aroma</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --cor-primaria: #4A2C2A;
        }
    </style>
</head>

<body class="bg-stone-50">

<header class="bg-white shadow-lg sticky top-0 z-20">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <h1 class="text-3xl font-extrabold text-[var(--cor-primaria)]">☕ Café & <span class="text-red-600">Aroma</span></h1>

        <div class="flex gap-3 items-center">
            <?php if ($is_logged_in): ?>
                <?php if ($is_admin): ?>
                    <a href="indexadim.php" class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[#5d3835]">Painel Admin</a>
                <?php else: ?>
                    <a href="carrinho.php" class="border border-[var(--cor-primaria)] text-[var(--cor-primaria)] px-4 py-2 rounded-lg hover:bg-[var(--cor-primaria)] hover:text-white">🛒 Carrinho</a>
                <?php endif; ?>
                <a href="logout.php" class="border border-gray-400 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100">Sair</a>
            <?php else: ?>
                <a href="login.php" class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded-lg hover:bg-[#5d3835] shadow">Entrar / Cadastrar</a>
            <?php endif; ?>
        </div>

    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-16">

    <h2 class="text-5xl font-extrabold text-center mb-12 text-[var(--cor-primaria)]">Nosso Menu Completo </h2>

    <!-- PROMOÇÕES REAIS -->
    <?php if (!empty($promocoes)): ?>
        <section class="mb-16">
            <h3 class="text-3xl font-bold mb-6 text-gray-800 border-b-4 border-red-600 pb-2"> Promoções Especiais</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                <?php foreach ($promocoes as $p): echo renderizar_card($p, $is_logged_in, $is_admin, $placeholder_url); endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- MAIS VENDIDOS -->
    <?php if (!empty($mais_vendidos)): ?>
        <section class="mb-16">
            <h3 class="text-3xl font-bold mb-6 text-gray-800 border-b-4 border-red-600 pb-2"> Mais Vendidos</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                <?php foreach ($mais_vendidos as $p): echo renderizar_card($p, $is_logged_in, $is_admin, $placeholder_url); endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- CATEGORIAS -->
    <h3 class="text-3xl font-bold mb-8 pt-4 border-t border-gray-300 text-gray-900">Todas as Categorias</h3>

    <?php foreach ($categorias_do_menu as $categoria): ?>
        <?php if (empty($produtos_agrupados[$categoria])) continue; ?>

        <section class="mb-16">
            <h4 class="text-2xl font-bold mb-6 text-gray-800 border-b-2 border-red-500 pb-2">
                <?= htmlspecialchars($categoria) ?>
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                <?php foreach ($produtos_agrupados[$categoria] as $p): ?>
                    <?= renderizar_card($p, $is_logged_in, $is_admin, $placeholder_url) ?>
                <?php endforeach; ?>
            </div>
        </section>

    <?php endforeach; ?>

</main>

</body>
</html>
