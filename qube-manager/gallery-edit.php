<?php
require_once 'config/config.php';
checkLogin();

$database = new Database();
$db = $database->getConnection();

$galleryId = $_GET['id'] ?? '';
$isEdit = !empty($galleryId);
$pageTitle = $isEdit ? 'Editar Galeria' : 'Nova Galeria';

$message = '';
$messageType = '';

$gallery = null;
if ($isEdit) {
    $query = "SELECT * FROM qube_galleries WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $galleryId);
    $stmt->execute();
    $gallery = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$gallery) {
        header('Location: galleries.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'save') {
        $title = $_POST['title'] ?? '';
        $categoryId = $_POST['category_id'] ?? '';
        $description = $_POST['description'] ?? '';
        $featuredImage = $_POST['featured_image'] ?? '';
        $published = isset($_POST['published']) ? 1 : 0;
        $orderIndex = $_POST['order_index'] ?? 0;

        if (!empty($title) && !empty($categoryId)) {
            if ($isEdit) {
                $query = "UPDATE qube_galleries SET title = :title, category_id = :category_id, description = :description, featured_image = :featured_image, published = :published, order_index = :order_index WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $galleryId);
            } else {
                $galleryId = generateUUID();
                $query = "INSERT INTO qube_galleries (id, title, category_id, description, featured_image, published, order_index) VALUES (:id, :title, :category_id, :description, :featured_image, :published, :order_index)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $galleryId);
            }

            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':featured_image', $featuredImage);
            $stmt->bindParam(':published', $published);
            $stmt->bindParam(':order_index', $orderIndex);

            if ($stmt->execute()) {
                $message = $isEdit ? 'Galeria atualizada com sucesso!' : 'Galeria criada com sucesso!';
                $messageType = 'success';

                if (!$isEdit) {
                    header('Location: gallery-edit.php?id=' . $galleryId);
                    exit();
                }

                $query = "SELECT * FROM qube_galleries WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $galleryId);
                $stmt->execute();
                $gallery = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $message = 'Erro ao salvar galeria';
                $messageType = 'danger';
            }
        }
    } elseif ($action == 'add_image') {
        $imagePath = $_POST['image_path'] ?? '';
        $imageTitle = $_POST['image_title'] ?? '';

        if (!empty($imagePath) && !empty($galleryId)) {
            $query = "SELECT COALESCE(MAX(image_order), -1) + 1 as next_order FROM qube_gallery_images WHERE gallery_id = :gallery_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':gallery_id', $galleryId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextOrder = $result['next_order'];

            $imageId = generateUUID();
            $query = "INSERT INTO qube_gallery_images (id, gallery_id, image_path, title, image_order) VALUES (:id, :gallery_id, :image_path, :title, :image_order)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $imageId);
            $stmt->bindParam(':gallery_id', $galleryId);
            $stmt->bindParam(':image_path', $imagePath);
            $stmt->bindParam(':title', $imageTitle);
            $stmt->bindParam(':image_order', $nextOrder);

            if ($stmt->execute()) {
                $message = 'Imagem adicionada com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao adicionar imagem';
                $messageType = 'danger';
            }
        }
    } elseif ($action == 'delete_image') {
        $imageId = $_POST['image_id'] ?? '';

        if (!empty($imageId)) {
            $query = "DELETE FROM qube_gallery_images WHERE id = :id AND gallery_id = :gallery_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $imageId);
            $stmt->bindParam(':gallery_id', $galleryId);

            if ($stmt->execute()) {
                $message = 'Imagem removida com sucesso!';
                $messageType = 'success';
            } else {
                $message = 'Erro ao remover imagem';
                $messageType = 'danger';
            }
        }
    } elseif ($action == 'set_featured') {
        $imagePath = $_POST['image_path'] ?? '';

        if (!empty($imagePath) && !empty($galleryId)) {
            $query = "UPDATE qube_galleries SET featured_image = :featured_image WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':featured_image', $imagePath);
            $stmt->bindParam(':id', $galleryId);

            if ($stmt->execute()) {
                $message = 'Imagem de capa definida!';
                $messageType = 'success';

                $query = "SELECT * FROM qube_galleries WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $galleryId);
                $stmt->execute();
                $gallery = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }
}

$queryCategories = "SELECT * FROM qube_categories ORDER BY order_index ASC, name ASC";
$stmtCategories = $db->prepare($queryCategories);
$stmtCategories->execute();
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

$images = [];
if ($isEdit) {
    $query = "SELECT * FROM qube_gallery_images WHERE gallery_id = :gallery_id ORDER BY image_order ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':gallery_id', $galleryId);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="mb-3">
    <a href="galleries.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="content-card mb-4">
            <h5 class="mb-4">Informações da Galeria</h5>
            <form method="POST">
                <input type="hidden" name="action" value="save">

                <div class="mb-3">
                    <label class="form-label">Título *</label>
                    <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($gallery['title'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoria *</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($gallery['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($gallery['description'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagem de Capa (URL)</label>
                    <input type="text" class="form-control" name="featured_image" value="<?php echo htmlspecialchars($gallery['featured_image'] ?? ''); ?>" placeholder="../img/aplicacoes/condominios.jpg">
                    <small class="text-muted">URL completa ou relativa da imagem</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ordem de Exibição</label>
                        <input type="number" class="form-control" name="order_index" value="<?php echo $gallery['order_index'] ?? 0; ?>">
                        <small class="text-muted">Menor número aparece primeiro</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="published" id="published" <?php echo ($gallery['published'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="published">
                                Publicado
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Galeria
                    </button>
                    <a href="galleries.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="content-card mb-4">
            <h6 class="mb-3">Informações</h6>
            <p><small class="text-muted">
                <?php if ($isEdit): ?>
                    Criado em: <?php echo formatDate($gallery['created_at']); ?><br>
                    Total de imagens: <?php echo count($images); ?>
                <?php else: ?>
                    Salve a galeria para adicionar imagens
                <?php endif; ?>
            </small></p>
        </div>
    </div>
</div>

<?php if ($isEdit): ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Imagens da Galeria</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImageModal">
            <i class="fas fa-plus"></i> Adicionar Imagem
        </button>
    </div>

    <?php if (count($images) > 0): ?>
        <div class="row">
            <?php foreach ($images as $image): ?>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($image['title']); ?>" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <p class="card-text small mb-2"><?php echo htmlspecialchars($image['title']); ?></p>
                            <div class="btn-group btn-group-sm w-100" role="group">
                                <button class="btn btn-outline-primary" onclick="setFeatured('<?php echo htmlspecialchars($image['image_path'], ENT_QUOTES); ?>')">
                                    <i class="fas fa-star"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteImage('<?php echo $image['id']; ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Nenhuma imagem adicionada. Clique em "Adicionar Imagem" para começar.
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="addImageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_image">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">URL da Imagem *</label>
                        <input type="text" class="form-control" name="image_path" required placeholder="../img/aplicacoes/condominios.jpg">
                        <small class="text-muted">URL completa ou relativa da imagem</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="image_title" placeholder="Descrição da imagem">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteImageForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete_image">
    <input type="hidden" name="image_id" id="delete_image_id">
</form>

<form id="setFeaturedForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="set_featured">
    <input type="hidden" name="image_path" id="featured_image_path">
</form>

<script>
function deleteImage(imageId) {
    if (confirm('Tem certeza que deseja remover esta imagem?')) {
        document.getElementById('delete_image_id').value = imageId;
        document.getElementById('deleteImageForm').submit();
    }
}

function setFeatured(imagePath) {
    if (confirm('Definir esta imagem como capa da galeria?')) {
        document.getElementById('featured_image_path').value = imagePath;
        document.getElementById('setFeaturedForm').submit();
    }
}
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
