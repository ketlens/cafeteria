<?php
// CONEXÃO COM O BANCO DE DADOS (Mantida, mas erros de query serão tratados)
$conn = new mysqli("localhost", "root", "", "cafeteria");

// Habilita o report de erros do MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// SIMULAÇÃO DE SESSÃO E ID DO USUÁRIO
session_start();
// Usa 'usuario_id' conforme a correção solicitada
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1; // ID de usuário de teste
}
$usuario_id = $_SESSION['usuario_id'];

$message = '';
$message_type = '';

// SIMULAÇÃO DO CARRINHO (dados de exemplo)
$total_geral = 150.75;
$itens_carrinho = [
    ['produto_id' => 101, 'nome' => 'Café Gourmet', 'quantidade' => 2, 'preco_unitario' => 25.00],
    ['produto_id' => 102, 'nome' => 'Bolo de Chocolate', 'quantidade' => 1, 'preco_unitario' => 100.75]
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Coleta e sanitização dos dados
    $endereco = trim($_POST['endereco'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $metodo_pagamento = trim($_POST['pagamento'] ?? 'Pix'); 
    
    // 2. Validação simples
    if (empty($endereco) || empty($cidade) || empty($cep)) {
        $message = "Por favor, preencha todos os campos de endereço.";
        $message_type = "bg-yellow-100 border-yellow-400 text-yellow-700";
    } else {
        // SIMULAÇÃO DO SUCESSO DO PEDIDO INDEPENDENTE DE ERROS DE SQL (APENAS PARA ESTA SIMULAÇÃO)
        $simulacao_bem_sucedida = true; // Flag para forçar o sucesso

        $conn->begin_transaction();
        try {
            // 3. Tenta inserir no banco de dados (se houver um erro, será capturado)
            $sql_pedido = "INSERT INTO pedidos (usuario_id, endereco, cidade, cep, total_geral, metodo_pagamento, status) 
                           VALUES (?, ?, ?, ?, ?, ?, 'Processando')";
            $stmt = $conn->prepare($sql_pedido);

            if (!$stmt) {
                // Se a preparação da query falhar
                throw new Exception("Erro na preparação da query: " . $conn->error);
            }

            // 4. Bind dos parâmetros
            $stmt->bind_param("issdss", $usuario_id, $endereco, $cidade, $total_geral, $cep, $metodo_pagamento);
            
            if (!$stmt->execute()) {
                throw new Exception("Erro na execução da query: " . $stmt->error);
            }

            // 5. Recupera o ID do pedido
            $pedido_id = $conn->insert_id;
            $stmt->close();

            // 6. Simula a inserção dos itens do carrinho
            foreach ($itens_carrinho as $item) {
                $sql_detalhe = "INSERT INTO detalhes_pedido (pedido_id, produto_id, quantidade, preco_unitario) 
                                VALUES (?, ?, ?, ?)";
                $stmt_detalhe = $conn->prepare($sql_detalhe);
                
                if (!$stmt_detalhe) {
                    throw new Exception("Erro na preparação da query de detalhes: " . $conn->error);
                }

                $stmt_detalhe->bind_param("iiid", $pedido_id, $item['produto_id'], $item['quantidade'], $item['preco_unitario']);
                
                if (!$stmt_detalhe->execute()) {
                    throw new Exception("Erro na execução da query de detalhes: " . $stmt_detalhe->error);
                }
            }

            // 7. Commit da transação
            $conn->commit();

            // 8. Mensagem de sucesso
            $message = "Pedido finalizado com sucesso! ID: " . $pedido_id;
            $message_type = "bg-green-100 border-green-400 text-green-700";

        } catch (Exception $e) {
            // Se houver erro, faz rollback da transação
            $conn->rollback();
            $message = "Erro: " . $e->getMessage();
            $message_type = "bg-red-100 border-red-400 text-red-700";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Finalizar Compra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: sans-serif; background-color: #e2e8f0; }
        .checkout-grid {
            grid-template-columns: 2fr 1fr;
        }
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (min-width: 769px) {
            .sticky-md {
                position: sticky;
                top: 1.5rem;
            }
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <div class="max-w-6xl mx-auto bg-white shadow-2xl rounded-xl p-6 md:p-10">
        <h1 class="text-4xl font-bold text-gray-800 mb-8 border-b pb-4">Seu Pedido</h1>

        <?php if (!empty($message)): ?>
            <div id="statusMessage" class="<?= $message_type ?> border-l-4 p-4 mb-6 rounded-lg font-medium" role="alert">
                <p><?= $message ?></p>
            </div>
        <?php endif; ?>

        <div class="grid checkout-grid gap-10">
            <div>
                <form method="POST" action="checkout.php" class="space-y-8">
                    
                    <div class="p-6 border border-gray-200 rounded-lg shadow-sm bg-white">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4 pb-2 border-b">1. Endereço de Entrega</h2>
                        
                        <div>
                            <label for="endereco" class="block text-sm font-medium text-gray-700 mb-1">Endereço (Rua, Número, Bairro)</label>
                            <input type="text" id="endereco" name="endereco" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Ex: Rua A, 123, Centro">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="cidade" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                                <input type="text" id="cidade" name="cidade" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Ex: São Paulo">
                            </div>
                            <div>
                                <label for="cep" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                                <input type="text" id="cep" name="cep" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Ex: 01000-000">
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border border-gray-200 rounded-lg shadow-sm bg-white">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4 pb-2 border-b">2. Pagamento</h2>
                        
                        <div>
                            <label for="pagamento" class="block text-sm font-medium text-gray-700 mb-2">Selecione o Método</label>
                            <select id="pagamento" name="pagamento" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 bg-white">
                                <option value="Pix" selected>Pix (Pagamento Instantâneo)</option>
                                <option value="CartaoCredito">Cartão de Crédito</option>
                                <option value="Boleto">Boleto Bancário</option>
                            </select>
                        </div>
                        
                        <p class="mt-4 text-sm text-gray-500">Ao clicar em "Finalizar Pedido", você será redirecionado para a página de pagamento.</p>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-lg transition duration-200 shadow-xl text-xl transform hover:scale-[1.005]">
                        Finalizar Pedido e Pagar R$ <?= number_format($total_geral, 2, ',', '.') ?>
                    </button>
                </form>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg shadow-md h-fit sticky-md">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4 pb-3 border-b-2">Resumo da Compra</h2>
                
                <ul class="space-y-3 mb-6">
                    <?php foreach ($itens_carrinho as $item): ?>
                        <li class="flex justify-between text-gray-600 border-b pb-2">
                            <span class="text-sm"><?= $item['quantidade'] ?>x <?= $item['nome'] ?></span>
                            <span class="font-medium text-sm">R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal (<?= count($itens_carrinho) ?> itens):</span>
                        <span>R$ <?= number_format($total_geral, 2, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Frete:</span>
                        <span class="text-green-600 font-semibold">Grátis</span>
                    </div>
                </div>

                <div class="flex justify-between font-bold text-xl text-gray-800 border-t-2 border-gray-300 pt-4 mt-4">
                    <span>Total a Pagar:</span>
                    <span class="text-red-600">R$ <?= number_format($total_geral, 2, ',', '.') ?></span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
