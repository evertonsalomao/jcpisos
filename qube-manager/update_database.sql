-- ===================================================
-- ATUALIZAÇÃO DO BANCO DE DADOS - QUBE MANAGER
-- Execute este script no seu MySQL para adicionar as novas colunas
-- ===================================================

-- Adicionar coluna 'title' na tabela qube_gallery_images
ALTER TABLE qube_gallery_images
ADD COLUMN IF NOT EXISTS title VARCHAR(255) DEFAULT '' AFTER image_url;

-- Renomear coluna 'image_url' para 'image_path'
ALTER TABLE qube_gallery_images
CHANGE COLUMN image_url image_path TEXT NOT NULL;
