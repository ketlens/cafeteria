<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafeteria";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
//✅ Tabela promocao_do_dia
//✅ Ver Mais Vendidos
//✅ Vendas do Dia
//✅ Vendas da Semana
//✅ Vendas do Mês
//✅ Pedidos em Andamento
//✅ Gerenciar Usuários
?>
