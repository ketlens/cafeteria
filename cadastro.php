<?php
// Inclui o session_start para ser consistente com login.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conexao.php'; // Carrega a conexão

$mensagem = ''; // Variável para armazenar mensagens de sucesso ou erro

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['senha'])) {

        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); 
        // O valor do nivel_acesso para clientes será 0
        $nivel_acesso = 0; 

        if (!isset($conn) || $conn === null) {
            $mensagem = "Erro: conexão com o banco de dados não encontrada.";
        } else {

            // QUERY ATUALIZADA: Adicionamos 'nivel_acesso' à lista de colunas e um novo placeholder '?'
            $sql = "INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {

                // bind_param ATUALIZADO: Adicionamos 'i' (integer) para o novo campo nivel_acesso (0)
                $stmt->bind_param("sssi", $nome, $email, $senha, $nivel_acesso);

                if ($stmt->execute()) {
                    // Redireciona com parâmetro de sucesso para evitar alert()
                    header("Location: login.php?cadastro_sucesso=true");
                    $stmt->close();
                    $conn->close();
                    exit();
                } else {
                    // Erro comum aqui é e-mail duplicado
                    if ($conn->errno == 1062) {
                        $mensagem = "❌ O E-mail já está em uso.";
                    } else {
                        $mensagem = "❌ Erro ao cadastrar: " . $stmt->error;
                    }
                }

                $stmt->close();

            } else {
                $mensagem = "❌ Erro na preparação do cadastro: " . $conn->error;
            }
        }

    } else {
        $mensagem = "⚠️ Por favor, preencha todos os campos.";
    }
}

// Variáveis de estado do usuário (necessárias para o cabeçalho)
$is_admin = isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 1;
$is_logged_in = isset($_SESSION['usuario_id']);

// Conexão somente é fechada aqui se não foi fechada antes
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro</title>
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
<body class="bg-stone-50 text-gray-800 font-sans min-h-screen flex flex-col">
    
    <!-- CABEÇALHO PADRÃO -->
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
                            <a href="logout.php" class="border border-gray-400 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition">Sair</a>
                        <?php else: ?>
                            <a href="carrinho.php" class="border border-[var(--cor-primaria)] text-[var(--cor-primaria)] px-4 py-2 rounded-lg font-semibold hover:bg-[var(--cor-primaria)] hover:text-white transition shadow-sm">
                                🛒 Carrinho
                            </a>
                            <a href="logout.php" class="border border-gray-400 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition">Sair</a>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <!-- Na página de cadastro, mostramos o botão de login -->
                        <a href="login.php" class="bg-[var(--cor-primaria)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[var(--cor-hover)] transition shadow-lg">Entrar</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. ROW DE NAVEGAÇÃO PRINCIPAL -->
            <nav class="hidden md:flex space-x-8 text-lg font-medium text-gray-700 border-t border-gray-100">
                <a href="index.php" class="nav-link py-3 hover:text-red-600 transition">Início</a>
                <a href="index.php#produtos" class="nav-link py-3 hover:text-red-600 transition">Produtos</a>
                <a href="sobre.php" class="nav-link py-3 hover:text-red-600 transition">Sobre</a>
            </nav>
        </div>
    </header>
    <!-- FIM DO CABEÇALHO PADRÃO -->

    <!-- CONTEÚDO PRINCIPAL (Centraliza o Formulário) -->
    <main class="flex-grow flex justify-center items-center py-16">
        <form method="POST" class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-sm border border-gray-100">
            <!-- Título do Formulário -->
            <h2 class="text-2xl font-bold text-[var(--cor-primaria)] text-center mb-6">Cadastre-se</h2> 

            <?php if (!empty($mensagem)): ?>
                <p class="text-red-500 text-sm text-center mb-4 p-2 bg-red-50 rounded-lg border border-red-200"><?= $mensagem ?></p>
            <?php endif; ?>
            
            <input type="text" name="nome" placeholder="Nome completo" required 
                   class="w-full mb-4 p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition">

            <input type="email" name="email" placeholder="E-mail" required 
                   class="w-full mb-4 p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition">
            
            <input type="password" name="senha" placeholder="Senha" required 
                   class="w-full mb-6 p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition">
            
            <button type="submit" 
                    class="w-full bg-red-600 text-white p-3 rounded-lg font-semibold hover:bg-red-700 transition shadow-md">
                Cadastrar
            </button>
            
            <a href="login.php" class="text-[var(--cor-primaria)] text-sm text-center block mt-4 hover:underline transition">
                Já tenho conta
            </a> 
        </form>
    </main>
    <!-- FIM DO CONTEÚDO PRINCIPAL -->
    
    <!-- FOOTER 
    <footer class="bg-gray-900 text-gray-300 text-center py-6">
        © 2025 Cafeteria Expresso. Todos os direitos reservados.
    </footer>  ad-->

</body>
</html>
