<?php
session_start();
include('conexao.php'); 

// Redireciona se não for um POST ou se o produto_id não for fornecido
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['produto_id'])) {
    header("Location: index.php?erro=requisicao_invalida");
    exit;
}

// Verifica se o usuário está logado (opcional, mas recomendado)
if (!isset($_SESSION['usuario_id'])) {
    // Pode redirecionar para o login ou permitir adicionar ao carrinho sem login
    header("Location: login.php");
    exit;
}

$produto_id = (int)$_POST['produto_id'];
$quantidade = max(1, (int)$_POST['quantidade']); // Garante que a quantidade seja no mínimo 1

// 1. Buscar detalhes do produto no DB
$stmt = $conn->prepare("SELECT id, nome, preco, imagem FROM produtos WHERE id = ?");
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: index.php?erro=produto_nao_encontrado");
    exit;
}

$produto = $resultado->fetch_assoc();
$stmt->close();

// 2. Inicializar o carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// 3. Adicionar ou atualizar o item no carrinho
if (isset($_SESSION['carrinho'][$produto_id])) {
    // Item já existe: apenas incrementa a quantidade
    $_SESSION['carrinho'][$produto_id]['quantidade'] += $quantidade;
} else {
    // Item novo: adiciona todos os detalhes (incluindo o preço atual do DB)
    $_SESSION['carrinho'][$produto_id] = [
        'id' => $produto['id'],
        'nome' => $produto['nome'],
        // É CRÍTICO salvar o preço, pois o preço pode mudar no DB
        'preco' => (float)$produto['preco'], 
        'quantidade' => $quantidade,
        'imagem' => $produto['imagem'] // Para visualização no carrinho
    ];
}

// 4. Redireciona para a página do carrinho
header("Location: carrinho.php"); 
exit;
?>
