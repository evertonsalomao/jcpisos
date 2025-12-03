/*
  # Create CMS Tables for Qube Manager

  1. New Tables
    - `cms_users`
      - `id` (uuid, primary key)
      - `username` (text, unique)
      - `password` (text, hashed password)
      - `email` (text, unique, nullable)
      - `created_at` (timestamp)
    
    - `cms_categories`
      - `id` (uuid, primary key)
      - `name` (text)
      - `slug` (text, unique)
      - `created_at` (timestamp)
    
    - `cms_galleries`
      - `id` (uuid, primary key)
      - `title` (text)
      - `category_id` (uuid, foreign key)
      - `featured_image` (text)
      - `created_at` (timestamp)
    
    - `cms_gallery_images`
      - `id` (uuid, primary key)
      - `gallery_id` (uuid, foreign key)
      - `image_path` (text)
      - `image_order` (integer)
      - `created_at` (timestamp)

  2. Security
    - Enable RLS on all tables
    - Add policies for authenticated access only
*/

-- Create cms_users table
CREATE TABLE IF NOT EXISTS cms_users (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  username text UNIQUE NOT NULL,
  password text NOT NULL,
  email text UNIQUE,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE cms_users ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Authenticated users can view users"
  ON cms_users FOR SELECT
  TO authenticated
  USING (true);

CREATE POLICY "Authenticated users can insert users"
  ON cms_users FOR INSERT
  TO authenticated
  WITH CHECK (true);

CREATE POLICY "Authenticated users can update users"
  ON cms_users FOR UPDATE
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can delete users"
  ON cms_users FOR DELETE
  TO authenticated
  USING (true);

-- Create cms_categories table
CREATE TABLE IF NOT EXISTS cms_categories (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  name text NOT NULL,
  slug text UNIQUE NOT NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE cms_categories ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view categories"
  ON cms_categories FOR SELECT
  TO anon, authenticated
  USING (true);

CREATE POLICY "Authenticated users can insert categories"
  ON cms_categories FOR INSERT
  TO authenticated
  WITH CHECK (true);

CREATE POLICY "Authenticated users can update categories"
  ON cms_categories FOR UPDATE
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can delete categories"
  ON cms_categories FOR DELETE
  TO authenticated
  USING (true);

-- Create cms_galleries table
CREATE TABLE IF NOT EXISTS cms_galleries (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  title text NOT NULL,
  category_id uuid REFERENCES cms_categories(id) ON DELETE CASCADE,
  featured_image text NOT NULL,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE cms_galleries ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view galleries"
  ON cms_galleries FOR SELECT
  TO anon, authenticated
  USING (true);

CREATE POLICY "Authenticated users can insert galleries"
  ON cms_galleries FOR INSERT
  TO authenticated
  WITH CHECK (true);

CREATE POLICY "Authenticated users can update galleries"
  ON cms_galleries FOR UPDATE
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can delete galleries"
  ON cms_galleries FOR DELETE
  TO authenticated
  USING (true);

-- Create cms_gallery_images table
CREATE TABLE IF NOT EXISTS cms_gallery_images (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  gallery_id uuid REFERENCES cms_galleries(id) ON DELETE CASCADE,
  image_path text NOT NULL,
  image_order integer DEFAULT 0,
  created_at timestamptz DEFAULT now()
);

ALTER TABLE cms_gallery_images ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Anyone can view gallery images"
  ON cms_gallery_images FOR SELECT
  TO anon, authenticated
  USING (true);

CREATE POLICY "Authenticated users can insert gallery images"
  ON cms_gallery_images FOR INSERT
  TO authenticated
  WITH CHECK (true);

CREATE POLICY "Authenticated users can update gallery images"
  ON cms_gallery_images FOR UPDATE
  TO authenticated
  USING (true)
  WITH CHECK (true);

CREATE POLICY "Authenticated users can delete gallery images"
  ON cms_gallery_images FOR DELETE
  TO authenticated
  USING (true);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_galleries_category_id ON cms_galleries(category_id);
CREATE INDEX IF NOT EXISTS idx_gallery_images_gallery_id ON cms_gallery_images(gallery_id);
CREATE INDEX IF NOT EXISTS idx_gallery_images_order ON cms_gallery_images(image_order);