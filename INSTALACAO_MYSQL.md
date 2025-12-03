# Guia de Instalação - Qube Manager (MySQL)

## Pré-requisitos

- Servidor web com PHP 7.4 ou superior
- MySQL 5.7+ ou MariaDB 10.2+
- Extensão PDO_MYSQL habilitada no PHP
- Acesso ao phpMyAdmin ou terminal MySQL

## Instalação Passo a Passo

### Passo 1: Criar Banco de Dados MySQL

Acesse o phpMyAdmin ou terminal MySQL e execute:

```sql
CREATE DATABASE qube_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Passo 2: Executar o Instalador Automático

1. Acesse: `http://seusite.com/qube-manager/install.php`

2. Preencha o formulário:
   - **Host MySQL**: `localhost` (ou IP do servidor)
   - **Nome do Banco**: `qube_manager` (ou nome que você criou)
   - **Usuário MySQL**: seu usuário (geralmente `root`)
   - **Senha MySQL**: sua senha do MySQL

3. Clique em "Continuar"

O instalador irá automaticamente:
- ✓ Criar todas as tabelas (users, categories, galleries, images)
- ✓ Inserir o usuário administrador
- ✓ Criar categorias padrão
- ✓ Gerar arquivo de configuração

### Passo 3: Fazer Login

Após a instalação, acesse: `http://seusite.com/qube-manager/`

**Credenciais:**
- Usuário: `adm_qube`
- Senha: `jj401rbz.`

### Passo 4: Começar a Usar

1. Faça login no painel
2. Altere sua senha em "Usuários"
3. Envie fotos para `/uploads/` via FTP
4. Crie galerias em "Galerias"
5. Adicione fotos às galerias
6. Publique as galerias

## Estrutura do Banco de Dados

O sistema cria 4 tabelas:

- **qube_users** - Usuários do painel administrativo
- **qube_categories** - Categorias das obras
- **qube_galleries** - Galerias de fotos
- **qube_gallery_images** - Imagens de cada galeria

## Instalação Manual (Opcional)

Se preferir instalar manualmente sem usar o instalador:

1. Importe o arquivo `qube-manager/database.sql` no MySQL
2. Edite `qube-manager/config/database.php` com seus dados
3. Acesse `qube-manager/login.php`

## Solução de Problemas

### Erro: "Banco de dados não encontrado"
- Certifique-se que criou o banco antes de rodar o instalador
- Verifique se o nome está correto

### Erro: "Acesso negado"
- Verifique usuário e senha do MySQL
- Confirme as permissões do usuário MySQL

### Erro: "PDO_MYSQL not found"
- Habilite a extensão no php.ini: `extension=pdo_mysql`
- Reinicie o servidor web

### Página em branco
- Verifique os logs de erro do PHP
- Confirme permissões de escrita em `qube-manager/config/`

## Segurança

Após instalação:
1. Delete o arquivo `install.php` (opcional mas recomendado)
2. Altere a senha padrão
3. Mantenha backups regulares do banco
4. Use senhas fortes para MySQL

## Backup

Para fazer backup, exporte o banco via phpMyAdmin ou:

```bash
mysqldump -u usuario -p qube_manager > backup.sql
```

Para restaurar:

```bash
mysql -u usuario -p qube_manager < backup.sql
```

## Suporte

Documentação completa em:
- `qube-manager/README.md`
- `qube-manager/database.sql` (estrutura do banco)
