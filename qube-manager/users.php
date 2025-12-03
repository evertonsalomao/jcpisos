<?php
require_once 'config/config.php';
checkLogin();

$pageTitle = 'Usuários';

$database = new Database();
$db = $database->getConnection();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'create') {
        $username = $_POST['username'] ?? '';
        $name = $_POST['name'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($username) && !empty($password)) {
            $id = generateUUID();
            $hashedPassword = hashPassword($password);

            $query = "INSERT INTO qube_users (id, username, name, password) VALUES (:id, :username, :name, :password)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                $message = 'Usuário criado com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao criar usuário';
                $messageType = 'danger';
            }
        }
    } elseif ($action == 'update') {
        $id = $_POST['id'] ?? '';
        $username = $_POST['username'] ?? '';
        $name = $_POST['name'] ?? '';

        if (!empty($id) && !empty($username)) {
            $query = "UPDATE qube_users SET username = :username, name = :name WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':name', $name);

            if ($stmt->execute()) {
                $message = 'Usuário atualizado com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao atualizar usuário';
                $messageType = 'danger';
            }
        }
    } elseif ($action == 'change_password') {
        $id = $_POST['id'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($id) && !empty($password)) {
            $hashedPassword = hashPassword($password);

            $query = "UPDATE qube_users SET password = :password WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                $message = 'Senha alterada com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao alterar senha';
                $messageType = 'danger';
            }
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'] ?? '';

        if (!empty($id) && $id != $_SESSION['qube_user_id']) {
            $query = "DELETE FROM qube_users WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $message = 'Usuário excluído com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao excluir usuário';
                $messageType = 'danger';
            }
        } else {
            $message = 'Você não pode excluir seu próprio usuário!';
            $messageType = 'danger';
        }
    }
}

$query = "SELECT * FROM qube_users ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Gerenciar Usuários</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus"></i> Novo Usuário
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Nome</th>
                    <th>Data de Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="<?php echo $user['id'] == $_SESSION['qube_user_id'] ? 'table-warning' : ''; ?>">
                        <td>
                            <?php echo htmlspecialchars($user['username']); ?>
                            <?php if ($user['id'] == $_SESSION['qube_user_id']): ?>
                                <span class="badge bg-warning text-dark">Você</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo formatDate($user['created_at']); ?></td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-primary" onclick="editUser('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="changePassword('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')">
                                <i class="fas fa-key"></i>
                            </button>
                            <?php if ($user['id'] != $_SESSION['qube_user_id']): ?>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Usuário</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" name="name" required>
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

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Usuário</label>
                        <input type="text" class="form-control" name="username" id="edit_username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
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

<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alterar Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <input type="hidden" name="id" id="password_id">
                <div class="modal-body">
                    <p>Alterando senha do usuário: <strong id="password_username"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" name="password" required minlength="6">
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o usuário <strong id="delete_name"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita!</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(id, username, name) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_name').value = name;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function changePassword(id, username) {
    document.getElementById('password_id').value = id;
    document.getElementById('password_username').textContent = username;
    new bootstrap.Modal(document.getElementById('passwordModal')).show();
}

function deleteUser(id, username) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = username;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
