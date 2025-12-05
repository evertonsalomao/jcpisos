/*
  # Sistema de Gerenciamento de Produtos

  ## Descrição Geral
  Sistema completo para gerenciar produtos (pisos intertravados) com todas as suas características:
  cores, dimensões, vantagens, aplicações, galerias e FAQs.

  ## 1. Novas Tabelas

  ### qube_colors (Cores dos Produtos)
    - `id` (uuid, primary key)
    - `name` (text) - Nome da cor (ex: "Terra Cota")
    - `image_path` (text) - Caminho da imagem da cor
    - `order_index` (integer) - Ordem de exibição
    - `created_at` (timestamptz)
    - `updated_at` (timestamptz)

  ### qube_products (Produtos)
    - `id` (uuid, primary key)
    - `title` (text) - Título do produto
    - `slug` (text, unique) - URL amigável
    - `description` (text) - Descrição principal
    - `description_below_image` (text) - Descrição abaixo da imagem
    - `image_path` (text) - Caminho da imagem principal
    - `is_published` (boolean) - Status de publicação
    - `order_index` (integer) - Ordem de exibição
    - `created_at` (timestamptz)
    - `updated_at` (timestamptz)

  ### qube_product_colors (Relação N:N entre Produtos e Cores)
    - `id` (uuid, primary key)
    - `product_id` (uuid, foreign key)
    - `color_id` (uuid, foreign key)
    - `order_index` (integer) - Ordem de exibição das cores no produto

  ### qube_product_dimensions (Dimensões do Produto)
    - `id` (uuid, primary key)
    - `product_id` (uuid, foreign key)
    - `dimension` (text) - Dimensão (ex: "11 x 22cm")
    - `thickness` (text) - Espessura (ex: "6cm")
    - `resistance` (text) - Resistência MPA (ex: "35 a 50 MPA")
    - `usage_indication` (text) - Indicação de uso
    - `order_index` (integer) - Ordem de exibição
    - `created_at` (timestamptz)

  ### qube_product_advantages (Vantagens do Produto)
    - `id` (uuid, primary key)
    - `product_id` (uuid, foreign key)
    - `text` (text) - Texto da vantagem
    - `order_index` (integer) - Ordem de exibição
    - `created_at` (timestamptz)

  ### qube_product_applications (Aplicações do Produto)
    - `id` (uuid, primary key)
    - `product_id` (uuid, foreign key)
    - `text` (text) - Texto da aplicação
    - `order_index` (integer) - Ordem de exibição
    - `created_at` (timestamptz)

  ### qube_gallery_products (Relação N:N entre Galerias e Produtos)
    - `id` (uuid, primary key)
    - `gallery_id` (uuid, foreign key)
    - `product_id` (uuid, foreign key)

  ### qube_product_faqs (FAQs do Produto)
    - `id` (uuid, primary key)
    - `product_id` (uuid, foreign key)
    - `question` (text) - Pergunta
    - `answer` (text) - Resposta
    - `order_index` (integer) - Ordem de exibição
    - `created_at` (timestamptz)

  ## 2. Security
  
  - Habilitar RLS em todas as tabelas
  - Políticas restritivas para leitura pública apenas de produtos publicados
  - Políticas de escrita apenas para usuários autenticados

  ## 3. Importantes
  
  - Todos os relacionamentos têm CASCADE DELETE para manter integridade
  - Índices criados para otimizar consultas
  - Ordem de exibição controlada via order_index
*/

-- =====================================================
-- TABELA: CORES
-- =====================================================

CREATE TABLE IF NOT EXISTS qube_colors (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    name text NOT NULL,
    image_path text NOT NULL,
    order_index integer DEFAULT 0,
    created_at timestamptz DEFAULT now(),
    updated_at timestamptz DEFAULT now()
);

ALTER TABLE qube_colors ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public can view colors"
    ON qube_colors FOR SELECT
    TO public
    USING (true);

CREATE POLICY "Authenticated users can insert colors"
    ON qube_colors FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated users can update colors"
    ON qube_colors FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated users can delete colors"
    ON qube_colors FOR DELETE
    TO authenticated
    USING (true);

CREATE INDEX IF NOT EXISTS idx_colors_order ON qube_colors(order_index);

-- =====================================================
-- TABELA: PRODUTOS
-- =====================================================

CREATE TABLE IF NOT EXISTS qube_products (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    title text NOT NULL,
    slug text UNIQUE NOT NULL,
    description text NOT NULL,
    description_below_image text DEFAULT '',
    image_path text NOT NULL,
    is_published boolean DEFAULT false,
    order_index integer DEFAULT 0,
    created_at timestamptz DEFAULT now(),
    updated_at timestamptz DEFAULT now()
);

ALTER TABLE qube_products ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public can view published products"
    ON qube_products FOR SELECT
    TO public
    USING (is_published = true);

CREATE POLICY "Authenticated users can view all products"
    ON qube_products FOR SELECT
    TO authenticated
    USING (true);

CREATE POLICY "Authenticated users can insert products"
    ON qube_products FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated users can update products"
    ON qube_products FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated users can delete products"
    ON qube_products FOR DELETE
    TO authenticated
    USING (true);

CREATE INDEX IF NOT EXISTS idx_products_slug ON qube_products(slug);
CREATE INDEX IF NOT EXISTS idx_products_published ON qube_products(is_published);
CREATE INDEX IF NOT EXISTS idx_products_order ON qube_products(order_index);

-- =====================================================
-- TABELA: RELAÇÃO PRODUTO-COR (N:N)
-- =====================================================

CREATE TABLE IF NOT EXISTS qube_product_colors (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    product_id uuid NOT NULL REFERENCES qube_products(id) ON DELETE CASCADE,
    color_id uuid NOT NULL REFERENCES qube_colors(id) ON DELETE CASCADE,
    order_index integer DEFAULT 0,
    UNIQUE(product_id, color_id)
);

ALTER TABLE qube_product_colors ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public can view product colors"
    ON qube_product_colors FOR SELECT
    TO public
    USING (true);

CREATE POLICY "Authenticated users can insert product colors"
    ON qube_product_colors FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated users can update product colors"
    ON qube_product_colors FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated users can delete product colors"
    ON qube_product_colors FOR DELETE
    TO authenticated
    USING (true);

CREATE INDEX IF NOT EXISTS idx_product_colors_product ON qube_product_colors(product_id);
CREATE INDEX IF NOT EXISTS idx_product_colors_color ON qube_product_colors(color_id);

-- =====================================================
-- TABELA: DIMENSÕES DO PRODUTO
-- =====================================================

CREATE TABLE IF NOT EXISTS qube_product_dimensions (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    product_id uuid NOT NULL REFERENCES qube_products(id) ON DELETE CASCADE,
    dimension text NOT NULL,
    thickness text NOT NULL,
    resistance text NOT NULL,
    usage_indication text NOT NULL,
    order_index integer DEFAULT 0,
    created_at timestamptz DEFAULT now()
);

ALTER TABLE qube_product_dimensions ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public can view product dimensions"
    ON qube_product_dimensions FOR SELECT
    TO public
    USING (true);

CREATE POLICY "Authenticated users can insert product dimensions"
    ON qube_product_dimensions FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated users can update product dimensions"
    ON qube_product_dimensions FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated users can delete product dimensions"
    ON qube_product_dimensions FOR DELETE
    TO authenticated
    USING (true);

CREATE INDEX IF NOT EXISTS idx_product_dimensions_product ON qube_product_dimensions(product_id);

-- =====================================================
-- TABELA: VANTAGENS DO PRODUTO
-- =====================================================

CREATE TABLE IF NOT EXISTS qube_product_advantages (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    product_id uuid NOT NULL REFERENCES qube_products(id) ON DELETE CASCADE,
    text text NOT NULL,
    order_index integer DEFAULT 0,
    created_at timestamptz DEFAULT now()
);

ALTER TABLE qube_product_advantages ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public can view product advantages"
    ON qube_product_advantages FOR SELECT
    TO public
    USING (true);

CREATE POLICY "Authenticated users can insert product advantages"
    ON qube_product_advantages FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated users can update product advantages"
    ON qube_product_advantages FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated users can delete product advantages"
    ON qube_product_advantages FOR DELETE
    TO authenticated
    USING (true);

CREATE INDEX IF NOT EXISTS idx_product_advantages_product ON qube_product_advantages(product_id);

-- =====================================================
-- TABELA: APLICAÇÕES DO PRODUTO
-- =====================================================

CREATE TABLE IF NOT EXISTS qube_product_applications (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    product_id uuid NOT NULL REFERENCES qube_products(id) ON DELETE CASCADE,
    text text NOT NULL,
    order_index integer DEFAULT 0,
    created_at timestamptz DEFAULT now()
);

ALTER TABLE qube_product_applications ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public can view product applications"
    ON qube_product_applications FOR SELECT
    TO public
    USING (true);

CREATE POLICY "Authenticated users can insert product applications"
    ON qube_product_applications FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated users can update product applications"
    ON qube_product_applications FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated users can delete product applications"
    ON qube_product_applications FOR DELETE
    TO authenticated
    USING (true);

CREATE INDEX IF NOT EXISTS idx_product_applications_product ON qube_product_applications(product_id);

-- =====================================================
-- TABELA: RELAÇÃO GALERIA-PRODUTO (N:N)
-- =====================================================

CREATE TABLE IF NOT EXISTS qube_gallery_products (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    gallery_id uuid NOT NULL REFERENCES qube_galleries(id) ON DELETE CASCADE,
    product_id uuid NOT NULL REFERENCES qube_products(id) ON DELETE CASCADE,
    UNIQUE(gallery_id, product_id)
);

ALTER TABLE qube_gallery_products ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public can view gallery products"
    ON qube_gallery_products FOR SELECT
    TO public
    USING (true);

CREATE POLICY "Authenticated users can insert gallery products"
    ON qube_gallery_products FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated users can update gallery products"
    ON qube_gallery_products FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated users can delete gallery products"
    ON qube_gallery_products FOR DELETE
    TO authenticated
    USING (true);

CREATE INDEX IF NOT EXISTS idx_gallery_products_gallery ON qube_gallery_products(gallery_id);
CREATE INDEX IF NOT EXISTS idx_gallery_products_product ON qube_gallery_products(product_id);

-- =====================================================
-- TABELA: FAQs DO PRODUTO
-- =====================================================

CREATE TABLE IF NOT EXISTS qube_product_faqs (
    id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    product_id uuid NOT NULL REFERENCES qube_products(id) ON DELETE CASCADE,
    question text NOT NULL,
    answer text NOT NULL,
    order_index integer DEFAULT 0,
    created_at timestamptz DEFAULT now()
);

ALTER TABLE qube_product_faqs ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public can view product faqs"
    ON qube_product_faqs FOR SELECT
    TO public
    USING (true);

CREATE POLICY "Authenticated users can insert product faqs"
    ON qube_product_faqs FOR INSERT
    TO authenticated
    WITH CHECK (true);

CREATE POLICY "Authenticated users can update product faqs"
    ON qube_product_faqs FOR UPDATE
    TO authenticated
    USING (true)
    WITH CHECK (true);

CREATE POLICY "Authenticated users can delete product faqs"
    ON qube_product_faqs FOR DELETE
    TO authenticated
    USING (true);

CREATE INDEX IF NOT EXISTS idx_product_faqs_product ON qube_product_faqs(product_id);
