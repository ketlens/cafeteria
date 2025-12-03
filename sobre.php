<?php
session_start(); // Início da sessão
// É CRUCIAL que o arquivo conexao.php exista e esteja sem erros.
// Se a página estiver em branco, 99% da chance é que o erro está aqui:
require 'conexao.php'; 

// Variáveis de estado do usuário (para manter a barra de navegação funcional)
$is_admin = isset($_SESSION['nivel_access']) && $_SESSION['nivel_acesso'] == 1;
$is_logged_in = isset($_SESSION['usuario_id']);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sobre Nós - Café & Aroma ☕</title>
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
    
    <!-- PONTO DE VERIFICAÇÃO DE PHP -->
    <?php // Remova este bloco após confirmar que a página está carregando ?>
    <!-- <div style="background: #e0f7fa; color: #00796b; padding: 5px; text-align: center; font-size: 14px;">Ponto de Verificação PHP OK. O erro não está no require.</div> -->
    <?php // FIM DO PONTO DE VERIFICAÇÃO ?>

    <header class="bg-white shadow-lg sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-6">
            
            <!-- 1. ROW PRINCIPAL: LOGO & BOTÕES DE AÇÃO (Login/Carrinho) -->
            <div class="py-4 flex justify-between items-center">
                <h1 class="text-3xl font-extrabold text-[var(--cor-primaria)]">
                    ☕ Café & <span class="text-red-600">Aroma</span>
                </h1>
                
                <!-- BOTÕES DE USUÁRIO / ADMIN -->
                <div class="flex gap-3 items-center">
                    <?php if ($is_logged_in): ?>
                        
                        <?php if ($is_admin): ?>
                            <a href="dashboard.php" class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[var(--cor-hover)] transition shadow-md">Painel Admin</a>
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

            <!-- 2. ROW DE NAVEGAÇÃO PRINCIPAL -->
            <nav class="hidden md:flex space-x-8 text-lg font-medium text-gray-700 border-t border-gray-100">
                <a href="index.php" class="nav-link py-3 hover:text-red-600 transition">Início</a>
                <a href="index.php#produtos" class="nav-link py-3 hover:text-red-600 transition">Produtos</a>
                <!-- Link ATIVO para a página Sobre -->
                <a href="sobre.php" class="nav-link py-3 text-red-600 border-b-2 border-red-600">Sobre</a>
            </nav>
        </div>
    </header>

    <!-- BANNER DE TÍTULO -->
    <section class="bg-[var(--cor-primaria)] text-white py-16 text-center shadow-md">
        <h2 class="text-4xl font-extrabold mb-2 tracking-tight">Nossa História</h2>
        <p class="text-lg opacity-80">Mais do que um simples TCC, somos uma ideia nova para o mercado.</p>
    </section>

    <!-- CONTEÚDO PRINCIPAL: SOBRE NÓS -->
    <main class="max-w-5xl mx-auto px-6 py-16">
        
        <!-- Seção 1: Nossa Origem -->
        <div class="grid md:grid-cols-2 gap-10 items-center mb-16">
            <div>
                <h3 class="text-3xl font-bold text-[var(--cor-primaria)] mb-4">Trabalho de conclusão de curso</h3>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    O Café & Aroma nasceu de um sonho simples: levar a praticidade e o acessibilidade a compra de um café da manhã e da tarde de qualidade para todos os lares.
                </p>
                <p class="text-gray-700 leading-relaxed">
                Começamos como uma pequena ideia, focada em selecionar não apenas os melhores produtos mais momentos em família
                </p>
            </div>
            <!-- CORRIGIDO: Adicionado um texto alt descritivo -->
            <img src="img/logo-tcc.png" alt="Logo do café e aroma" class="rounded-xl shadow-2xl w-full object-cover">
        </div>

        <!-- Seção 2: O Comprometimento com a Qualidade -->
        <div class="bg-white p-10 rounded-xl shadow-xl border border-gray-100">
            <h3 class="text-3xl font-bold text-red-600 text-center mb-8">Um pouco sobre nosso TCC</h3>
            
            <div class="grid md:grid-cols-3 gap-8 text-center">
                <div>
                    <span class="text-4xl text-[var(--cor-primaria)] block mb-3">🌱</span>
                    <h4 class="font-semibold text-lg mb-2">Origem da ideia</h4>
                    <p class="text-sm text-gray-600">A ideia surgiu atravez de conversas sobre coisas que gostavamos e que dariam um bom tcc, veio deu um antigo projeto 
                        elaborado por um dos pais integrantes e descidimos tranformar em um tcc surgindo o "Café & Aroma".</p>
                </div>
                <div>
                    <span class="text-4xl text-[var(--cor-primaria)] block mb-3">🎯</span>
                    <h4 class="font-semibold text-lg mb-2">Nossa missão enquanto empresa</h4>
                    <p class="text-sm text-gray-600">Nossa missão em quanto a empresa KeySolucion é criar ideias inovadoras e simples para problemas e lacunas encontradas no dia a dia.</p>
                </div>
                <div>
                    <span class="text-4xl text-[var(--cor-primaria)] block mb-3">🎓</span>
                    <h4 class="font-semibold text-lg mb-2">Integrantes</h4>
                    <p class="text-sm text-gray-600">Arthur Beck Brasiliense - desenvolvedor do backende</p>
                    <p class="text-sm text-gray-600">Kaique Gabriel Andrade Pacifico -  desenvolvedor do aplicativo</p>
                    <p class="text-sm text-gray-600">Ketlen Vasconcelos Almeida - desenvolvedora do site</p>
                </div>
            </div>
        </div>

        <!-- Seção 3: Visite-nos -->
        <div class="text-center mt-16">
            <h3 class="text-3xl font-bold text-[var(--cor-primaria)] mb-4">Venha nos Conhecer!</h3>
            <p class="text-xl text-gray-700 mb-6">Estamos de portas abertas no wokeshop do dia 4 de Dezembro venha nos conhecer ETEC Professor Milton Gazzetti</p>
            <p class="font-bold text-lg">Rua Paulo Sérgio Righetti, 45 -  Bairro Cidade Jardim, Presidente Venceslau</p>
            <a href="index.php#produtos" class="mt-8 inline-block bg-red-600 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-red-700 transition">
                Ver o Menu Agora
            </a>
        </div>

    </main>

    <!-- FOOTER -->
    <!-- FOOTER <footer class="bg-gray-900 text-gray-300 text-center py-8 mt-16">
        <div class="max-w-7xl mx-auto px-6">
            <p>© 2025 Café e Aroma</p>
            <p class="text-sm mt-1">Desenvolvido com paixão pelo café.</p>
        </div>
    </footer> -->
</body>
</html>
