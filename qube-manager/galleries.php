<?php
require_once 'config/config.php';
checkLogin();

$pageTitle = 'Galerias';

$database = new Database();
$db = $database->getConnection();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'] ?? '';

    if (!empty($id)) {
        $query = "DELETE FROM qube_galleries WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $message = 'Galeria excluída com sucesso!';
            $messageType = 'success';
        } else {
            $message = 'Erro ao excluir galeria';
            $messageType = 'danger';
        }
    }
}

$filterCategory = $_GET['category'] ?? '';

$query = "SELECT g.*, c.name as category_name,
          (SELECT COUNT(*) FROM qube_gallery_images WHERE gallery_id = g.id) as images_count
          FROM qube_galleries g
          LEFT JOIN qube_categories c ON g.category_id = c.id";

if (!empty($filterCategory)) {
    $query .= " WHERE g.category_id = :category_id";
}

$query .= " ORDER BY g.order_index ASC, g.created_at DESC";

$stmt = $db->prepare($query);

if (!empty($filterCategory)) {
    $stmt->bindParam(':category_id', $filterCategory);
}

$stmt->execute();
$galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);

$queryCategories = "SELECT * FROM qube_categories ORDER BY order_index ASC, name ASC";
$stmtCategories = $db->prepare($queryCategories);
$stmtCategories->execute();
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

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
        <h5 class="mb-0">Gerenciar Galerias</h5>
        <a href="gallery-edit.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova Galeria
        </a>
    </div>

    <div class="mb-4">
        <div class="btn-group" role="group">
            <a href="galleries.php" class="btn btn-outline-primary <?php echo empty($filterCategory) ? 'active' : ''; ?>">
                Todas
            </a>
            <?php foreach ($categories as $category): ?>
                <a href="galleries.php?category=<?php echo $category['id']; ?>" class="btn btn-outline-primary <?php echo $filterCategory == $category['id'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (count($galleries) > 0): ?>
        <div class="row">
            <?php foreach ($galleries as $gallery): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ($gallery['featured_image']): ?>
                            <img src="<?php echo htmlspecialchars($gallery['featured_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($gallery['title']); ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($gallery['title']); ?></h5>
                            <p class="card-text">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($gallery['category_name']); ?></span>
                                <?php if ($gallery['published']): ?>
                                    <span class="badge bg-success">Publicado</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Rascunho</span>
                                <?php endif; ?>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-images"></i> <?php echo $gallery['images_count']; ?> foto(s)
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <a href="gallery-edit.php?id=<?php echo $gallery['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="deleteGallery('<?php echo $gallery['id']; ?>', '<?php echo htmlspecialchars($gallery['title']); ?>')">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Nenhuma galeria encontrada.
            <a href="gallery-edit.php" class="alert-link">Criar primeira galeria</a>
        </div>
    <?php endif; ?>
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
                    <p>Tem certeza que deseja excluir a galeria <strong id="delete_name"></strong>?</p>
                    <p class="text-danger"><small>Esta ação excluirá todas as imagens da galeria!</small></p>
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
function deleteGallery(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
