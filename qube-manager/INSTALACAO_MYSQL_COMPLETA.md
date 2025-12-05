# 🗄️ Instalação Completa do Banco de Dados MySQL

## 📋 Requisitos

- MySQL 5.7+ ou MySQL 8.0+
- PHP 7.4+ com extensão PDO_MYSQL
- Acesso ao phpMyAdmin ou linha de comando MySQL

---

## 🚀 Instalação Rápida

### Opção 1: Via phpMyAdmin

1. **Acesse o phpMyAdmin**
   - Abra seu navegador e acesse o phpMyAdmin

2. **Crie o Banco de Dados**
   ```sql
   CREATE DATABASE qube_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Selecione o Banco**
   - Clique no banco `qube_db` no menu lateral

4. **Importe o Script**
   - Clique na aba "SQL"
   - Cole o conteúdo do arquivo `database-mysql-completo.sql`
   - Clique em "Executar"

### Opção 2: Via Linha de Comando

```bash
# 1. Criar o banco de dados
mysql -u root -p -e "CREATE DATABASE qube_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Importar o script
mysql -u root -p qube_db < qube-manager/database-mysql-completo.sql
```

---

## ⚙️ Configuração

### 1. Configure o arquivo `config/database.php`

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'qube_db');
define('DB_USER', 'root');
define('DB_PASS', 'sua_senha_aqui');
define('DB_CHARSET', 'utf8mb4');
```

### 2. Teste a Conexão

Acesse: `http://seudominio.com/qube-manager/`

---

## 🔐 Login Inicial

**Credenciais Padrão:**
- **Usuário:** `admin`
- **Senha:** `admin123`

⚠️ **IMPORTANTE:** Altere a senha imediatamente após o primeiro login!

---

## 📊 Estrutura do Banco de Dados

O script cria as seguintes tabelas:

### 👤 Usuários
- `qube_users` - Usuários do painel administrativo

### 📁 Galerias
- `qube_categories` - Categorias das galerias
- `qube_galleries` - Galerias de imagens
- `qube_gallery_images` - Imagens das galerias

### 🧱 Produtos (Pisos)
- `qube_products` - Produtos principais
- `qube_colors` - Cores disponíveis
- `qube_product_colors` - Relação produto-cor (N:N)
- `qube_product_dimensions` - Dimensões e especificações
- `qube_product_advantages` - Vantagens do produto
- `qube_product_applications` - Aplicações do produto
- `qube_product_faqs` - Perguntas frequentes
- `qube_gallery_products` - Relação galeria-produto (N:N)

### 🗺️ Cidades
- `qube_cities` - Cidades para SEO

---

## ✅ Dados Iniciais

O script já insere automaticamente:

1. **Usuário Admin** (admin/admin123)
2. **5 Cores Padrão:**
   - Natural
   - Amarelo
   - Vermelho
   - Terra Cota
   - Grafite

---

## 🔧 Solução de Problemas

### Erro: "Access denied for user"
- Verifique o usuário e senha no arquivo `config/database.php`
- Certifique-se de que o usuário MySQL tem permissões

### Erro: "Unknown database"
- O banco de dados não foi criado
- Execute: `CREATE DATABASE qube_db;`

### Erro: "Foreign key constraint"
- Certifique-se de que o InnoDB está habilitado
- Execute o script completo de uma vez só

### Erro: "Charset mismatch"
- Verifique se o banco foi criado com utf8mb4
- Execute:
  ```sql
  ALTER DATABASE qube_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```

---

## 📝 Compatibilidade

✅ **Testado em:**
- MySQL 5.7
- MySQL 8.0
- MariaDB 10.3+

✅ **Recursos:**
- Suporte completo a caracteres UTF-8 (emojis, acentos)
- Foreign Keys com CASCADE DELETE
- Índices otimizados para performance
- Timestamps automáticos

---

## 🔄 Atualização de Versão Anterior

Se você já tem uma instalação antiga, execute o script de atualização:

```bash
mysql -u root -p qube_db < qube-manager/update_database.sql
```

---

## 📞 Suporte

Em caso de dúvidas ou problemas:
1. Verifique os logs de erro do MySQL
2. Confira as permissões do usuário do banco
3. Certifique-se de que todas as tabelas foram criadas corretamente

---

**Documentação atualizada em:** Dezembro 2024
