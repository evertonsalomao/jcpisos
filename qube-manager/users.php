<?php
require_once 'config.php';
requireLogin();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $email = trim($_POST['email']);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $data = [
                'username' => $username,
                'password' => $hashedPassword,
                'email' => $email ?: null
            ];

            if ($supabase->insert('cms_users', $data)) {
                $message = 'Usuário criado com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao criar usuário. Usuário pode já existir.';
                $messageType = 'danger';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'];

            if ($id === $_SESSION['user_id']) {
                $message = 'Você não pode excluir seu próprio usuário!';
                $messageType = 'danger';
            } else {
                if ($supabase->delete('cms_users', 'id=eq.' . urlencode($id))) {
                    $message = 'Usuário excluído com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao excluir usuário';
                    $messageType = 'danger';
                }
            }
        } elseif ($_POST['action'] === 'change_password') {
            $id = $_POST['id'];
            $newPassword = $_POST['new_password'];

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $data = [
                'password' => $hashedPassword
            ];

            if ($supabase->update('cms_users', 'id=eq.' . urlencode($id), $data)) {
                $message = 'Senha alterada com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao alterar senha';
                $messageType = 'danger';
            }
        }
    }
}

$users = $supabase->select('cms_users', 'order=created_at.desc');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Qube Manager</title>
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
        .current-user {
            background: #fff3cd;
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
                        <a class="nav-link" href="categories.php">Categorias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="galleries.php">Galerias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="users.php">Usuários</a>
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
                <h1>Usuários do Sistema</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    ➕ Novo Usuário
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
                        <th>Usuário</th>
                        <th>Email</th>
                        <th>Criado em</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="<?php echo $user['id'] === $_SESSION['user_id'] ? 'current-user' : ''; ?>">
                                <td>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                    <?php if ($user['id'] === $_SESSION['user_id']): ?>
                                        <span class="badge bg-warning">Você</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning"
                                            onclick="changePassword('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')">
                                        🔑
                                    </button>
                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                        <button class="btn btn-sm btn-danger"
                                                onclick="deleteUser('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')">
                                            🗑️
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">Nenhum usuário cadastrado</td>
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
                        <h5 class="modal-title">Novo Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nome de Usuário</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email (opcional)</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                            <small class="text-muted">Mínimo 6 caracteres</small>
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

    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="id" id="password_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Alterar Senha - <span id="password_username"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" name="new_password" required minlength="6">
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Alterar Senha</button>
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
        function changePassword(id, username) {
            document.getElementById('password_id').value = id;
            document.getElementById('password_username').textContent = username;
            new bootstrap.Modal(document.getElementById('passwordModal')).show();
        }

        function deleteUser(id, username) {
            if (confirm('Tem certeza que deseja excluir o usuário "' + username + '"?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>
