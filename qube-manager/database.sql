-- ===================================================
-- QUBE MANAGER - ESTRUTURA DO BANCO DE DADOS MYSQL
-- ===================================================

-- Criar banco de dados (opcional - pode usar banco existente)
-- CREATE DATABASE IF NOT EXISTS qube_manager DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE qube_manager;

-- Tabela de usuários do painel
CREATE TABLE IF NOT EXISTS qube_users (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de categorias
CREATE TABLE IF NOT EXISTS qube_categories (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(50) UNIQUE NOT NULL,
  order_index INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug (slug),
  INDEX idx_order (order_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de galerias
CREATE TABLE IF NOT EXISTS qube_galleries (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  title VARCHAR(200) NOT NULL,
  category_id CHAR(36),
  featured_image TEXT,
  published TINYINT(1) DEFAULT 1,
  order_index INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES qube_categories(id) ON DELETE CASCADE,
  INDEX idx_category (category_id),
  INDEX idx_published (published),
  INDEX idx_order (order_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de imagens das galerias
CREATE TABLE IF NOT EXISTS qube_gallery_images (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  gallery_id CHAR(36),
  image_url TEXT NOT NULL,
  order_index INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (gallery_id) REFERENCES qube_galleries(id) ON DELETE CASCADE,
  INDEX idx_gallery (gallery_id),
  INDEX idx_order (order_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir usuário administrador inicial
-- Senha: jj401rbz.
INSERT INTO qube_users (id, username, password, name, email)
VALUES (
  UUID(),
  'adm_qube',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'Administrador',
  'admin@qube.com'
) ON DUPLICATE KEY UPDATE username=username;

-- Inserir categorias padrão
INSERT INTO qube_categories (id, name, slug, order_index) VALUES
  (UUID(), 'Residencial/Condomínios', 'first', 1),
  (UUID(), 'Comercial', 'second', 2),
  (UUID(), 'Industrial', 'third', 3)
ON DUPLICATE KEY UPDATE slug=slug;
