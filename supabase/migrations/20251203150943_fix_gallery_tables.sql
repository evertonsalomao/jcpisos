/*
  # Corrigir tabelas de galeria

  1. Alterações na tabela qube_galleries
    - Adicionar coluna `description` (texto, descrição da galeria)

  2. Alterações na tabela qube_gallery_images
    - Renomear `image_url` para `image_path` (padronizar nomenclatura)
    - Renomear `order_index` para `image_order` (padronizar com código)
    - Adicionar coluna `title` (título/descrição da imagem)
*/

-- Adicionar description na tabela qube_galleries
DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_name = 'qube_galleries' AND column_name = 'description'
  ) THEN
    ALTER TABLE qube_galleries ADD COLUMN description text;
  END IF;
END $$;

-- Renomear e adicionar colunas na tabela qube_gallery_images
DO $$
BEGIN
  -- Renomear image_url para image_path
  IF EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_name = 'qube_gallery_images' AND column_name = 'image_url'
  ) THEN
    ALTER TABLE qube_gallery_images RENAME COLUMN image_url TO image_path;
  END IF;

  -- Renomear order_index para image_order
  IF EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_name = 'qube_gallery_images' AND column_name = 'order_index'
  ) THEN
    ALTER TABLE qube_gallery_images RENAME COLUMN order_index TO image_order;
  END IF;

  -- Adicionar coluna title
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_name = 'qube_gallery_images' AND column_name = 'title'
  ) THEN
    ALTER TABLE qube_gallery_images ADD COLUMN title text;
  END IF;
END $$;
