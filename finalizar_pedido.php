<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['carrinho'])) {
    header("Location: index.php?erro=carrinho_vazio");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$carrinho = $_SESSION['carrinho'];
$valor_total = 0;

foreach ($carrinho as $item) {
    // Certifica-se de que o preço e a quantidade são tratados como números
    $valor_total += (float)$item['preco'] * (int)$item['quantidade'];
}

// Inicia a transação para garantir que ambas as inserções sejam bem-sucedidas
$conn->begin_transaction();

try {
    // 3. INSERIR O CABEÇALHO DO PEDIDO
    // CORREÇÃO: Alterado 'usuario_id' para 'user_id' na query SQL, conforme o erro do MySQL.
    $stmt_pedido = $conn->prepare("INSERT INTO pedidos (usuario_id, valor_total, status) VALUES (?, ?, 'Processando')"); 
    
    // **NOVA VERIFICAÇÃO CRÍTICA**
    if (!$stmt_pedido) {
        throw new Exception("Falha no SQL do cabeçalho do pedido: " . $conn->error);
    }
    
    // A ligação de parâmetros permanece a mesma, usando a variável $usuario_id
    $stmt_pedido->bind_param("id", $usuario_id, $valor_total); 
    
    if (!$stmt_pedido->execute()) {
        throw new Exception("Falha ao executar o cabeçalho do pedido.");
    }
    $pedido_id = $conn->insert_id;
    $stmt_pedido->close();

    // 4. INSERIR OS DETALHES DO PEDIDO
    $stmt_detalhe = $conn->prepare("INSERT INTO detalhes_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
    
    if (!$stmt_detalhe) {
        throw new Exception("Falha no SQL dos detalhes do pedido: " . $conn->error);
    }
    
    foreach ($carrinho as $item) {
        $produto_id = $item['id'];
        $quantidade = $item['quantidade'];
        $preco_unitario = $item['preco'];
        
        // iiid -> integer, integer, integer, double/decimal
        $stmt_detalhe->bind_param("iiid", $pedido_id, $produto_id, $quantidade, $preco_unitario);
        
        if (!$stmt_detalhe->execute()) {
            throw new Exception("Falha ao salvar detalhes do produto " . $item['nome'] . ".");
        }
    }
    $stmt_detalhe->close();

    // 5. COMMIT
    $conn->commit();
    
    // 6. LIMPAR O CARRINHO
    unset($_SESSION['carrinho']);

    // Redireciona para a página de confirmação atualizada
    header("Location: pedido_confirmado.php?id=" . $pedido_id);
    exit;

} catch (Exception $e) {
    // 7. ROLLBACK e REDIRECIONAMENTO COM ERRO VISÍVEL
    $conn->rollback();
    // Você verá esta mensagem de erro na página do carrinho.
    header("Location: carrinho.php?erro=" . urlencode("Erro! " . $e->getMessage()));
    exit;
}
?>
