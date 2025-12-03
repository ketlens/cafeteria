-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Tempo de geração: 27/10/2025 às 04:39
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

 /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cafeteria`
--

-- --------------------------------------------------------
-- Tabela `contato`
-- --------------------------------------------------------

CREATE TABLE `contato` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `mensagem` TEXT NOT NULL,
  `data_envio` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabela `detalhes_pedido`
-- --------------------------------------------------------

CREATE TABLE `detalhes_pedido` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pedido_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `quantidade` INT NOT NULL,
  `preco_unitario` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabela `itens_pedido`
-- --------------------------------------------------------

CREATE TABLE `itens_pedido` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pedido_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `quantidade` INT NOT NULL,
  `preco_unitario` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabela `pedidos`
-- --------------------------------------------------------

CREATE TABLE `pedidos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_id` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Pendente','Pago','Entregue','Cancelado') DEFAULT 'Pendente',
  `data_pedido` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabela `produtos`
-- --------------------------------------------------------

CREATE TABLE `produtos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` TEXT DEFAULT NULL,
  `preco` DECIMAL(10,2) NOT NULL,
  `imagem` VARCHAR(255) DEFAULT NULL,
  `categoria` VARCHAR(50) DEFAULT NULL,
  `data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Inserção de dados para a tabela `produtos`
INSERT INTO `produtos` (id, nome, descricao, preco, imagem, categoria, data_criacao) VALUES
(1, 'Café Expresso', 'Café expresso', 20.00, 'img/cafe-expresso.png', 'Unitário', '2025-10-27 02:07:41'),
(2, 'Capputino', 'Capputino', 13.00, 'img/cappuccino.jpg', 'Unitário', '2025-11-14 09:30:46'),
(3, 'Café preto', 'Aroma forte e sabor marcante, o combustível básico...', 5.00, 'img/Cafe-preto.jpg', 'Unitário', '2025-11-14 09:37:29'),
(4, 'Pão francês fresquinho', 'Casquinha crocante, miolo macio', 12.00, 'img/pao-frances.jpg', 'Unitário', '2025-11-14 09:39:38'),
(5, 'Ovos mexidos', 'Cremosos, proteicos e sempre satisfatórios.', 5.00, 'img/ovos-mechidos.jpg', 'Unitário', '2025-11-14 09:41:04'),
(6, 'Pão de queijo', 'Pão de queijo – Quentinho e macio, aquele sabor qu...', 3.00, 'img/Pao-queijo.jpg', 'Unitário', '2025-11-14 09:42:10'),
(7, 'Pão de forma', 'Pão de forma', 10.00, 'img/Pao-forma.jpg', 'Unitário', '2025-11-14 09:45:04'),
(8, 'Café com leite', 'Café com leite', 7.00, 'img/Cafe-com-leite.jpg', 'Unitário', '2025-11-14 09:45:16'),
(9, 'Suco natural de Laranja', 'Suco natural de Laranja', 7.00, 'img/suco-laranja.jpg', 'Unitário', '2025-11-14 09:46:00'),
(10, 'Bolo caseiro de fubá', 'Bolo caseiro de fubá', 13.00, 'img/bolo-de-fuba.jpg', 'Unitário', '2025-11-14 09:47:06'),
(11, 'Bolo caseiro de cenoura', 'Bolo caseiro de cenoura', 12.00, 'img/bolo-de-cenoura.jpg', 'Unitário', '2025-11-14 09:47:35'),
(12, 'Bolo caseiro de chocolate', 'Bolo caseiro de chocolate', 13.00, 'img/bolo-de-chocolate.jpg', 'Unitário', '2025-11-14 09:48:10'),
(13, 'Bolo caseiro de laranja', 'Bolo caseiro de laranja', 12.00, 'img/bolo-laranja.jpg', 'Unitário', '2025-11-14 09:48:35'),
(14, 'Misto quente', 'Misto quente', 2.00, 'img/misto-quente.jpg', 'Unitário', '2025-11-14 09:52:28'),
(15, 'Sanduíche natural', 'Sanduíche natural', 3.00, 'img/Sandu-natural.jpg', 'Unitário', '2025-11-14 09:52:48'),
(16, 'Coxinha', 'Coxinha', 7.00, 'img/Coxinha.jpg', 'Unitário', '2025-11-14 09:53:15'),
(17, 'Enroladinho de presunto e queijo', 'Enroladinho de presunto e queijo', 12.00, 'img/Enroladinho.jpg', 'Unitário', '2025-11-14 09:53:32'),
(18, 'Tradicional', 'Pão francês com manteiga • Café preto • Uma fruta ...', 10.00, 'img/combo-tradiciona.png', 'Combo', '2025-11-14 09:55:00'),
(19, 'Energia', 'Ovos mexidos • Pão de forma torrado • Suco de laranja', 3.00, 'img/combo-energia.png', 'Combo', '2025-11-14 09:55:23'),
(20, 'Natural', 'Iogurte com granola • Frutas picadas • Chá', 1.00, 'img/combo-natural.png', 'Combo', '2025-11-14 09:55:54');

-- --------------------------------------------------------
-- Tabela `usuarios`
-- --------------------------------------------------------

CREATE TABLE `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `nivel_acesso` TINYINT NOT NULL DEFAULT 0,
  `senha` VARCHAR(255) NOT NULL,
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Inserção de dados para a tabela `usuarios`
INSERT INTO `usuarios` (`id`, `nome`, `email`, `nivel_acesso`, `senha`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@cafeteria.com', 1, '$2y$10$h8o4xjAq6E7gk9o1pTj52O5Bg7vAZwZ3dZIZmFQp0cxM7yQuf5dV2', '2025-10-27 00:32:28'),
(2, 'kimberly', 'kimberly@gmail.com', 0, '$2y$10$GtQ8FYDLNOhyiFlTOeXJ6.rQ2UID581LtGmkqAeQQ.4GNTyRagnlK', '2025-10-27 01:35:43'),
(3, 'vick', 'vick@gmail.com', 0, '$2y$10$8jPRHgRMad9V0wAXy8z2IeP3R3AtZ/IkkU9Fgmn0xiQVL6dc0xo/e', '2025-10-27 02:02:33'),
(4, 'kimberly', 'kimberlyalmeida@gmail.com', 0, '$2y$10$yQwUITSThDDVT463yH1.he7ySQYq15RrhcBS8rtCfDenIIZjdiQlG', '2025-10-27 02:31:06'),
(5, 'admi', 'admin@com', 1, '$2y$10$1ZY2MiWlFHGBSbbeiz2c6ugs2EeClCtEOYrqRmBNRrrcOCCrNKyli', '2025-11-13 08:16:17');

-- --------------------------------------------------------
-- Tabela `venda`
-- --------------------------------------------------------

CREATE TABLE `venda` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario_id` INT NOT NULL,
  `produto_id` INT NOT NULL,
  `quantidade` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `data_venda` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `local` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Inserção de dados para a tabela `venda`
INSERT INTO `venda` (id, usuario_id, produto_id, quantidade, total, data_venda, local) VALUES
(1, 1, 2, 10, 10.50, '2025-11-19 11:35:18', 'jardim'),
(2, 2, 1, 15, 15.00, '2025-11-19 11:35:18', 'real'),
(3, 3, 3, 8, 8.00, '2025-11-19 11:35:18', 'jardim'),
(4, 7, 6, 0, 0.00, '2025-11-19 12:16:06', 'jardim'),
(5, 7, 6, 0, 0.00, '2025-11-19 12:16:06', 'jardim');

-- --------------------------------------------------------
-- Chaves estrangeiras
-- --------------------------------------------------------

ALTER TABLE `detalhes_pedido`
  ADD CONSTRAINT `detalhes_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `detalhes_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `venda`
  ADD CONSTRAINT `venda_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venda_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

COMMIT;

 /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
