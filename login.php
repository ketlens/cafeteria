<?php
session_start();
require 'conexao.php';

$erro = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, senha, nivel_acesso FROM usuarios WHERE email = ?");

    if (!$stmt) {
        $erro = "Erro na preparação do SQL: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario['senha'])) {

                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];

                // REDIRECIONAMENTO CONDICIONAL
                switch ($usuario['nivel_acesso']) {
                    case 1: // Administrador
                        header("Location: indexadim.php");
                        break;
                    case 2: // Gerente de Vendas
                        header("Location: painel_gerente.php");
                        break;
                    default: // Cliente
                        header("Location: index.php");
                        break;
                }
                exit;
            } else {
                $erro = "Email ou senha incorretos.";
            }
        } else {
            $erro = "Email ou senha incorretos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cafeteria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --cor-primaria: #4A2C2A;
            /* Marrom Café Escuro */
            --cor-hover: #5d3835;
        }

        .text-\\[var\\(--cor-primaria\\)\\] {
            color: var(--cor-primaria);
        }

        .bg-\\[var\\(--cor-primaria\\)\\] {
            background-color: var(--cor-primaria);
        }

        .hover\\:bg-\\[var\\(--cor-hover\\)\\]:hover {
            background-color: var(--cor-hover);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center font-sans">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl border border-gray-100">
        <div class="text-center mb-6">
            <span class="text-5xl" role="img" aria-label="Café">☕</span>
            <h1 class="text-3xl font-extrabold text-gray-800 mt-2">Café & Aroma</h1>
        </div>

        <form method="POST">
            <!-- Título do Formulário -->
            <h2 class="text-2xl font-bold text-[var(--cor-primaria)] text-center mb-6">Acesse sua Conta</h2>

            <?php if (!empty($erro)): ?>
                <p class="text-red-500 text-sm text-center mb-4 p-2 bg-red-50 rounded-lg border border-red-200"><?= $erro ?></p>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Email" required
                class="w-full mb-4 p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition">

            <input type="password" name="senha" placeholder="Senha" required
                class="w-full mb-6 p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition">

            <button type="submit"
                class="w-full bg-[var(--cor-primaria)] text-white p-3 rounded-lg font-semibold hover:bg-[var(--cor-hover)] transition shadow-md">
                Entrar
            </button>

            <a href="cadastro.php" class="text-red-600 text-sm text-center block mt-4 hover:underline transition">
                Não tem conta? Cadastre-se
            </a>
        </form>
    </div>

</body>

</html>