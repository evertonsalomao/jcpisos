<?php
session_start();
require_once 'config/database.php';

// Verifica se usuário está logado
if (!isset($_SESSION['qube_user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Processar upload de imagem
function uploadColorImage($file) {
    $uploadDir = 'uploads/colors/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }
    return false;
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = $_POST['name'];
        $order = $_POST['order_index'] ?? 0;

        // Upload da imagem
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $imagePath = uploadColorImage($_FILES['image']);

            if ($imagePath) {
                $query = "INSERT INTO qube_colors (name, image_path, order_index) VALUES (:name, :image_path, :order_index)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':image_path', $imagePath);
                $stmt->bindParam(':order_index', $order);

                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Cor criada com sucesso!';
                } else {
                    $_SESSION['error'] = 'Erro ao criar cor.';
                }
            } else {
                $_SESSION['error'] = 'Erro ao fazer upload da imagem.';
            }
        }
        header('Location: colors.php');
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $order = $_POST['order_index'] ?? 0;

        // Se tem nova imagem
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $imagePath = uploadColorImage($_FILES['image']);

            if ($imagePath) {
                // Remove imagem antiga
                $queryOld = "SELECT image_path FROM qube_colors WHERE id = :id";
                $stmtOld = $db->prepare($queryOld);
                $stmtOld->bindParam(':id', $id);
                $stmtOld->execute();
                $oldImage = $stmtOld->fetch(PDO::FETCH_ASSOC);
                if ($oldImage && file_exists($oldImage['image_path'])) {
                    unlink($oldImage['image_path']);
                }

                $query = "UPDATE qube_colors SET name = :name, image_path = :image_path, order_index = :order_index, updated_at = now() WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':image_path', $imagePath);
            } else {
                $query = "UPDATE qube_colors SET name = :name, order_index = :order_index, updated_at = now() WHERE id = :id";
                $stmt = $db->prepare($query);
            }
        } else {
            $query = "UPDATE qube_colors SET name = :name, order_index = :order_index, updated_at = now() WHERE id = :id";
            $stmt = $db->prepare($query);
        }

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':order_index', $order);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Cor atualizada com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao atualizar cor.';
        }

        header('Location: colors.php');
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'];

        // Remove imagem do servidor
        $queryImg = "SELECT image_path FROM qube_colors WHERE id = :id";
        $stmtImg = $db->prepare($queryImg);
        $stmtImg->bindParam(':id', $id);
        $stmtImg->execute();
        $imageData = $stmtImg->fetch(PDO::FETCH_ASSOC);
        if ($imageData && file_exists($imageData['image_path'])) {
            unlink($imageData['image_path']);
        }

        $query = "DELETE FROM qube_colors WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Cor excluída com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir cor.';
        }

        header('Location: colors.php');
        exit;
    }
}

// Buscar todas as cores
$query = "SELECT * FROM qube_colors ORDER BY order_index ASC, name ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$colors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Cores</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus"></i> Nova Cor
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Ordem</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($colors as $color): ?>
                    <tr>
                        <td>
                            <img src="/<?php echo htmlspecialchars($color['image_path']); ?>"
                                 alt="<?php echo htmlspecialchars($color['name']); ?>"
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><?php echo htmlspecialchars($color['name']); ?></td>
                        <td><?php echo $color['order_index']; ?></td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-primary"
                                    onclick="editColor('<?php echo $color['id']; ?>', '<?php echo htmlspecialchars($color['name']); ?>', '<?php echo $color['order_index']; ?>', '<?php echo htmlspecialchars($color['image_path']); ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta cor?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $color['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Criar -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Cor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">

                    <div class="mb-3">
                        <label class="form-label">Nome da Cor *</label>
                        <input type="text" class="form-control" name="name" required>
                        <small class="text-muted">Ex: Terra Cota, Vermelho, Grafite</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagem da Cor *</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                        <small class="text-muted">Formatos aceitos: JPG, PNG</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ordem de Exibição</label>
                        <input type="number" class="form-control" name="order_index" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Cor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Cor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label">Imagem Atual</label>
                        <div>
                            <img id="edit_current_image" src="" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nome da Cor *</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nova Imagem (opcional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Deixe em branco para manter a imagem atual</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ordem de Exibição</label>
                        <input type="number" class="form-control" name="order_index" id="edit_order">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editColor(id, name, order, imagePath) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_order').value = order;
    document.getElementById('edit_current_image').src = '/' + imagePath;

    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
