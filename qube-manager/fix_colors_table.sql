-- ===================================================
-- SCRIPT DE CORREÇÃO: TABELA QUBE_COLORS
-- Corrige o problema do campo ID sem AUTO_INCREMENT
-- ===================================================

-- Remove a tabela se existir (cuidado: apaga dados!)
DROP TABLE IF EXISTS `qube_product_colors`;
DROP TABLE IF EXISTS `qube_colors`;

-- Recria a tabela com ID AUTO_INCREMENT
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

-- Recria a tabela de relacionamento produto-cor
CREATE TABLE `qube_product_colors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `color_id` INT(11) NOT NULL,
  `order_index` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_color` (`product_id`, `color_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_color` (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insere cores padrão
INSERT INTO `qube_colors` (`name`, `image_path`, `order_index`) VALUES
('Natural', 'img/cor-natural.png', 1),
('Amarelo', 'img/cor-amarelo.png', 2),
('Vermelho', 'img/cor-vermelho.png', 3),
('Terra Cota', 'img/cor-terra.png', 4),
('Grafite', 'img/cor-grafite.png', 5);

-- ===================================================
-- SCRIPT CONCLUÍDO
-- ===================================================
