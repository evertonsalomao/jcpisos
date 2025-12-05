-- ===================================================
-- SCRIPT DE ATUALIZAÇÃO CORRIGIDO DO BANCO DE DADOS
-- Para quem já tem uma instalação anterior
-- Compatível com MySQL 5.7+ e MySQL 8.0+
-- VERSÃO CORRIGIDA: Usa INT AUTO_INCREMENT
-- ===================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ===================================================
-- IMPORTANTE: BACKUP ANTES DE EXECUTAR!
-- ===================================================
-- Este script irá recriar as tabelas de produtos
-- Faça backup dos dados se já tiver informações cadastradas
-- ===================================================

-- Remove tabelas dependentes primeiro (ordem inversa das dependências)
DROP TABLE IF EXISTS `qube_product_colors`;
DROP TABLE IF EXISTS `qube_product_dimensions`;
DROP TABLE IF EXISTS `qube_product_advantages`;
DROP TABLE IF EXISTS `qube_product_applications`;
DROP TABLE IF EXISTS `qube_product_faqs`;
DROP TABLE IF EXISTS `qube_gallery_products`;

-- Remove tabelas principais
DROP TABLE IF EXISTS `qube_colors`;
DROP TABLE IF EXISTS `qube_products`;
DROP TABLE IF EXISTS `qube_cities`;

-- ===================================================
-- CRIAR TABELA: CORES DOS PRODUTOS
-- ===================================================
CREATE TABLE `qube_colors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `image_path` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- CRIAR TABELA: PRODUTOS (PISOS)
-- ===================================================
CREATE TABLE `qube_products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
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
-- CRIAR TABELA: RELAÇÃO PRODUTO-COR
-- ===================================================
CREATE TABLE `qube_product_colors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `color_id` INT(11) NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_color` (`product_id`, `color_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_color` (`color_id`),
  CONSTRAINT `fk_product_colors_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_product_colors_color` FOREIGN KEY (`color_id`) REFERENCES `qube_colors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- CRIAR TABELA: DIMENSÕES DO PRODUTO
-- ===================================================
CREATE TABLE `qube_product_dimensions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
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
-- CRIAR TABELA: VANTAGENS DO PRODUTO
-- ===================================================
CREATE TABLE `qube_product_advantages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `text` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_advantages_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- CRIAR TABELA: APLICAÇÕES DO PRODUTO
-- ===================================================
CREATE TABLE `qube_product_applications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `text` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_applications_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- CRIAR TABELA: RELAÇÃO GALERIA-PRODUTO
-- ===================================================
CREATE TABLE `qube_gallery_products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `gallery_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_gallery_product` (`gallery_id`, `product_id`),
  KEY `idx_gallery` (`gallery_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_gallery_products_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `qube_galleries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_gallery_products_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- CRIAR TABELA: FAQs DO PRODUTO
-- ===================================================
CREATE TABLE `qube_product_faqs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `fk_faqs_product` FOREIGN KEY (`product_id`) REFERENCES `qube_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- CRIAR TABELA: CIDADES
-- ===================================================
CREATE TABLE `qube_cities` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
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
-- INSERIR CORES PADRÃO
-- ===================================================
INSERT INTO `qube_colors` (`name`, `image_path`, `order_index`) VALUES
('Natural', 'img/cor-natural.png', 1),
('Amarelo', 'img/cor-amarelo.png', 2),
('Vermelho', 'img/cor-vermelho.png', 3),
('Terra Cota', 'img/cor-terra.png', 4),
('Grafite', 'img/cor-grafite.png', 5);

-- ===================================================
-- SCRIPT CONCLUÍDO COM SUCESSO!
-- ===================================================
--
-- Tabelas criadas:
-- ✓ qube_colors (com AUTO_INCREMENT)
-- ✓ qube_products (com AUTO_INCREMENT)
-- ✓ qube_product_colors
-- ✓ qube_product_dimensions
-- ✓ qube_product_advantages
-- ✓ qube_product_applications
-- ✓ qube_product_faqs
-- ✓ qube_gallery_products
-- ✓ qube_cities (com AUTO_INCREMENT)
--
-- Cores padrão inseridas automaticamente!
-- ===================================================
