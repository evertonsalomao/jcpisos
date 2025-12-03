# Qube Manager - Painel de Gestão de Conteúdo

Sistema de gerenciamento de conteúdo para o site JC Pisos Intertravados.

## Instalação

1. Acesse: `http://seusite.com/qube-manager/install.php`
2. Este script criará o usuário inicial e as categorias padrão
3. Após a instalação, faça login em: `http://seusite.com/qube-manager/`

## Credenciais Iniciais

- **Usuário:** adm_qube
- **Senha:** jj401rbz.

**IMPORTANTE:** Altere a senha após o primeiro acesso!

## Funcionalidades

### 1. Categorias
Gerencie as categorias das obras:
- Criar novas categorias
- Editar categorias existentes
- Excluir categorias

### 2. Galerias
Gerencie as galerias de fotos das obras:
- Criar nova galeria com título e categoria
- Fazer upload de múltiplas imagens
- Definir uma imagem de destaque
- Adicionar mais imagens a galerias existentes
- Excluir imagens individuais
- Excluir galerias completas

### 3. Usuários
Gerencie os usuários do sistema:
- Criar novos usuários
- Alterar senhas
- Excluir usuários (exceto o próprio usuário logado)

## Como Usar

### Criar uma Galeria

1. Acesse "Galerias" no menu
2. Clique em "➕ Nova Galeria"
3. Preencha:
   - Título da galeria
   - Selecione a categoria
   - Escolha uma imagem de destaque
   - Selecione múltiplas imagens da galeria (Ctrl+Clique)
4. Clique em "Criar Galeria"

### Visualizar Imagens de uma Galeria

1. Na lista de galerias, clique em "👁️ Ver Imagens"
2. Você pode:
   - Visualizar todas as imagens
   - Adicionar novas imagens
   - Excluir imagens individuais

### Página Obras

As obras são exibidas automaticamente na página `obras.php` do site.
- As categorias aparecem como filtros no topo
- Cada galeria é exibida com sua imagem de destaque e título
- Ao clicar na imagem, abre um lightbox com todas as fotos da galeria

## Requisitos Técnicos

- PHP 8+
- Supabase (banco de dados)
- Permissões de escrita na pasta `qube-manager/uploads/galleries/`

## Estrutura de Arquivos

```
qube-manager/
├── config.php              # Configuração e conexão com Supabase
├── index.php               # Página de login
├── dashboard.php           # Dashboard principal
├── categories.php          # Gerenciamento de categorias
├── galleries.php           # Gerenciamento de galerias
├── users.php               # Gerenciamento de usuários
├── api_get_images.php      # API para buscar imagens
├── logout.php              # Logout
├── install.php             # Script de instalação
└── uploads/
    └── galleries/          # Pasta de upload de imagens
```

## Suporte

Em caso de dúvidas ou problemas, entre em contato com o desenvolvedor.
