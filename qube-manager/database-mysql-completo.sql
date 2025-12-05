-- ===================================================
-- QUBE MANAGER - ESTRUTURA COMPLETA DO BANCO DE DADOS MYSQL
-- Compatível com MySQL 5.7+ e MySQL 8.0+
-- ===================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ===================================================
-- TABELA: USUÁRIOS DO PAINEL
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_users` (
  `id` VARCHAR(36) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- TABELA: CATEGORIAS
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_categories` (
  `id` VARCHAR(36) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_order` (`order_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- TABELA: GALERIAS
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_galleries` (
  `id` VARCHAR(36) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `category_id` VARCHAR(36) DEFAULT NULL,
  `featured_image` TEXT DEFAULT NULL,
  `published` TINYINT(1) NOT NULL DEFAULT 1,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_published` (`published`),
  KEY `idx_order` (`order_index`),
  CONSTRAINT `fk_galleries_category` FOREIGN KEY (`category_id`) REFERENCES `qube_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- TABELA: IMAGENS DAS GALERIAS
-- ===================================================
CREATE TABLE IF NOT EXISTS `qube_gallery_images` (
  `id` VARCHAR(36) NOT NULL,
  `gallery_id` VARCHAR(36) NOT NULL,
  `image_path` TEXT NOT NULL,
  `title` VARCHAR(255) DEFAULT '',
  `order_index` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gallery` (`gallery_id`),
  KEY `idx_order` (`order_index`),
  CONSTRAINT `fk_gallery_images_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `qube_galleries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================================================
-- TABELA: CORES DOS PRODUTOS
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
-- TABELA: PRODUTOS (PISOS INTERTRAVADOS)
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
-- TABELA: RELAÇÃO PRODUTO-COR (N:N)
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
-- TABELA: DIMENSÕES DO PRODUTO
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
-- TABELA: VANTAGENS DO PRODUTO
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
-- TABELA: APLICAÇÕES DO PRODUTO
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
-- TABELA: RELAÇÃO GALERIA-PRODUTO (N:N)
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
-- TABELA: FAQs DO PRODUTO
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
-- TABELA: CIDADES
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
-- DADOS INICIAIS
-- ===================================================

-- Inserir usuário admin padrão (senha: admin123)
-- IMPORTANTE: Altere a senha após o primeiro login!
INSERT INTO `qube_users` (`id`, `username`, `password`, `name`, `email`) VALUES
('550e8400-e29b-41d4-a716-446655440000', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin@example.com')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Inserir cores padrão
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
-- PRÓXIMOS PASSOS:
-- 1. Execute este script no seu banco de dados MySQL
-- 2. Acesse o painel em: /qube-manager/
-- 3. Login padrão:
--    Usuário: admin
--    Senha: admin123
-- 4. IMPORTANTE: Altere a senha após o primeiro login!
--
-- ===================================================
