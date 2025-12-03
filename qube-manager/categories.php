<?php
require_once 'config.php';
requireLogin();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $name = trim($_POST['name']);
            $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $name)));

            $data = [
                'name' => $name,
                'slug' => $slug
            ];

            if ($supabase->insert('cms_categories', $data)) {
                $message = 'Categoria criada com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao criar categoria';
                $messageType = 'danger';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'];
            if ($supabase->delete('cms_categories', 'id=eq.' . urlencode($id))) {
                $message = 'Categoria excluída com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao excluir categoria';
                $messageType = 'danger';
            }
        } elseif ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $name)));

            $data = [
                'name' => $name,
                'slug' => $slug
            ];

            if ($supabase->update('cms_categories', 'id=eq.' . urlencode($id), $data)) {
                $message = 'Categoria atualizada com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao atualizar categoria';
                $messageType = 'danger';
            }
        }
    }
}

$categories = $supabase->select('cms_categories', 'order=created_at.desc');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Qube Manager</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: #1a1a1a;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            color: #ff6b00 !important;
            font-weight: 600;
            font-size: 24px;
        }
        .nav-link {
            color: #fff !important;
            transition: color 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            color: #ff6b00 !important;
        }
        .main-content {
            padding: 30px 0;
        }
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .page-header h1 {
            margin: 0;
            color: #1a1a1a;
            font-size: 28px;
        }
        .content-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .btn-primary {
            background: #ff6b00;
            border: none;
        }
        .btn-primary:hover {
            background: #e55f00;
        }
        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .badge-category {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Qube Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="categories.php">Categorias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="galleries.php">Galerias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Usuários</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Categorias de Obras</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    ➕ Nova Categoria
                </button>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="content-card">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories && count($categories) > 0): ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td><span class="badge-category"><?php echo htmlspecialchars($category['slug']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                            onclick="editCategory('<?php echo $category['id']; ?>', '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>')">
                                        ✏️
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="deleteCategory('<?php echo $category['id']; ?>', '<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>')">
                                        🗑️
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4">Nenhuma categoria cadastrada</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-header">
                        <h5 class="modal-title">Nova Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome da Categoria</label>
                            <input type="text" class="form-control" name="name" required>
                            <small class="text-muted">Ex: Residencial/Condomínios</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome da Categoria</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editCategory(id, name) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function deleteCategory(id, name) {
            if (confirm('Tem certeza que deseja excluir a categoria "' + name + '"?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
