-- ===================================================
-- SCRIPT DE ATUALIZAÇÃO DO BANCO DE DADOS MYSQL
-- Para quem já tem uma instalação anterior
-- Compatível com MySQL 5.7+ e MySQL 8.0+
-- ===================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: CORES DOS PRODUTOS
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_colors` (
  `id` VARCHAR(36) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `image_path` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: PRODUTOS (PISOS)
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_products` (
  `id` VARCHAR(36) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `description_below_image` TEXT DEFAULT NULL,
  `image_path` TEXT NOT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_published` (`is_published`),
  KEY `idx_order` (`order_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: RELAÇÃO PRODUTO-COR
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_product_colors` (
  `id` VARCHAR(36) NOT NULL,
  `product_id` VARCHAR(36) NOT NULL,
  `color_id` VARCHAR(36) NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_color` (`product_id`, `color_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_color` (`color_id`),
  CONSTRAINT `fk_product_colors_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_colors_color` FOREIGN KEY (`color_id`) REFERENCES `qube_colors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: DIMENSÕES DO PRODUTO
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_product_dimensions` (
  `id` VARCHAR(36) NOT NULL,
  `product_id` VARCHAR(36) NOT NULL,
  `dimension` VARCHAR(100) NOT NULL,
  `thickness` VARCHAR(50) NOT NULL,
  `resistance` VARCHAR(50) NOT NULL,
  `usage_indication` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_dimensions_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: VANTAGENS DO PRODUTO
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_product_advantages` (
  `id` VARCHAR(36) NOT NULL,
  `product_id` VARCHAR(36) NOT NULL,
  `text` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_advantages_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: APLICAÇÕES DO PRODUTO
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_product_applications` (
  `id` VARCHAR(36) NOT NULL,
  `product_id` VARCHAR(36) NOT NULL,
  `text` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_applications_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: RELAÇÃO GALERIA-PRODUTO
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_gallery_products` (
  `id` VARCHAR(36) NOT NULL,
  `gallery_id` VARCHAR(36) NOT NULL,
  `product_id` VARCHAR(36) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_gallery_product` (`gallery_id`, `product_id`),
  KEY `idx_gallery` (`gallery_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_gallery_products_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `qube_galleries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gallery_products_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: FAQs DO PRODUTO
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_product_faqs` (
  `id` VARCHAR(36) NOT NULL,
  `product_id` VARCHAR(36) NOT NULL,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_faqs_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- ATUALIZAR/CRIAR TABELA: CIDADES
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_cities` (
  `id` VARCHAR(36) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `state` VARCHAR(2) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_state` (`state`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- INSERIR CORES PADRÃO (se não existirem)
-- ===================================================
INSERT INTO `qube_colors` (`id`, `name`, `image_path`, `order_index`) VALUES
(UUID(), 'Natural', 'img/cor-natural.png', 1),
(UUID(), 'Amarelo', 'img/cor-amarelo.png', 2),
(UUID(), 'Vermelho', 'img/cor-vermelho.png', 3),
(UUID(), 'Terra Cota', 'img/cor-terra.png', 4),
(UUID(), 'Grafite', 'img/cor-grafite.png', 5)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- ===================================================
-- SCRIPT CONCLUÍDO
-- ===================================================
--
-- Este script adiciona as novas tabelas de:
-- - Produtos e Cores
-- - Cidades
-- - Relacionamentos N:N
--
-- Sem afetar dados existentes de:
-- - Usuários
-- - Categorias
-- - Galerias
--
-- ===================================================
