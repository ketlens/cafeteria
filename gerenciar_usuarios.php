<?php
session_start();
include('conexao.php');

// Busca todos os usuários
$sql = "SELECT id, nome, email, nivel_acesso, data_cadastro FROM usuarios ORDER BY nome ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        :root {
            --cor-primaria: #4A2C2A;
        }
    </style>
</head>
<body class="bg-gray-100">

<header style="background-color: #4A2C2A;" class="bg-[#4A2C2A] text-white p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Gerenciar Usuários</h1>
    <a href="indexadim.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Voltar</a>
</header>

<main class="max-w-7xl mx-auto p-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold text-[#4A2C2A] mb-4">Lista de Usuários</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Nome</th>
                    <th class="p-2 border">E-mail</th>
                    <th class="p-2 border">Tipo</th>
                    <th class="p-2 border">Data Cadastro</th>
                    <th class="p-2 border">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="p-2 border"><?= $row['id'] ?></td>
                            <td class="p-2 border"><?= $row['nome'] ?></td>
                            <td class="p-2 border"><?= $row['email'] ?></td>
                            <td class="p-2 border"><?= ucfirst($row['nivel_acesso']) ?></td>
                            <td class="p-2 border"><?= date('d/m/Y', strtotime($row['data_cadastro'])) ?></td>
                            <td class="p-2 border flex gap-2">
                                <a href="editar_usuario.php?id=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Editar</a>
                                <a href="alterar_nivel.php?id=<?= $row['id'] ?>" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Alterar Nível</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="p-2 border text-center">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
