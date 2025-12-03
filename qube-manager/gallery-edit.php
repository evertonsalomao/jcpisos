<?php
require_once 'config/config.php';
checkLogin();

$database = new Database();
$db = $database->getConnection();

function optimizeAndSaveImage($sourcePath, $destPath, $extension) {
    $maxWidth = 1200;
    $quality = 85;

    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }

    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];

    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    if (!$sourceImage) {
        return false;
    }

    if ($originalWidth > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = intval(($originalHeight / $originalWidth) * $maxWidth);
    } else {
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
    }

    $destImage = imagecreatetruecolor($newWidth, $newHeight);

    if ($extension == 'png' || $extension == 'gif' || $extension == 'webp') {
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
        imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    $result = false;
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $result = imagejpeg($destImage, $destPath, $quality);
            break;
        case 'png':
            $result = imagepng($destImage, $destPath, 9);
            break;
        case 'gif':
            $result = imagegif($destImage, $destPath);
            break;
        case 'webp':
            $result = imagewebp($destImage, $destPath, $quality);
            break;
    }

    imagedestroy($sourceImage);
    imagedestroy($destImage);

    return $result;
}

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
        $featuredImage = $gallery['featured_image'] ?? '';
        $published = isset($_POST['published']) ? 1 : 0;
        $orderIndex = $_POST['order_index'] ?? 0;

        if (!empty($title) && !empty($categoryId)) {
            if ($isEdit) {
                $query = "UPDATE qube_galleries SET title = :title, category_id = :category_id, featured_image = :featured_image, published = :published, order_index = :order_index WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $galleryId);
            } else {
                $galleryId = generateUUID();
                $query = "INSERT INTO qube_galleries (id, title, category_id, featured_image, published, order_index) VALUES (:id, :title, :category_id, :featured_image, :published, :order_index)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $galleryId);
            }

            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':category_id', $categoryId);
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
        if (!empty($_FILES['images']['name'][0]) && !empty($galleryId)) {
            $uploadDir = 'uploads/';
            $uploadedCount = 0;
            $errorCount = 0;

            $query = "SELECT COALESCE(MAX(order_index), -1) + 1 as next_order FROM qube_gallery_images WHERE gallery_id = :gallery_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':gallery_id', $galleryId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nextOrder = $result['next_order'];

            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $originalName = $_FILES['images']['name'][$key];
                    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (!in_array($extension, $allowedExtensions)) {
                        $errorCount++;
                        continue;
                    }

                    $fileName = uniqid() . '_' . time() . '.' . $extension;
                    $filePath = $uploadDir . $fileName;

                    if (optimizeAndSaveImage($tmpName, $filePath, $extension)) {
                        $imageId = generateUUID();
                        $query = "INSERT INTO qube_gallery_images (id, gallery_id, image_path, title, order_index) VALUES (:id, :gallery_id, :image_path, :title, :order_index)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':id', $imageId);
                        $stmt->bindParam(':gallery_id', $galleryId);
                        $stmt->bindParam(':image_path', $filePath);
                        $emptyTitle = '';
                        $stmt->bindParam(':title', $emptyTitle);
                        $stmt->bindParam(':order_index', $nextOrder);

                        if ($stmt->execute()) {
                            $uploadedCount++;
                            $nextOrder++;
                        } else {
                            $errorCount++;
                        }
                    } else {
                        $errorCount++;
                    }
                }
            }

            if ($uploadedCount > 0) {
                $message = $uploadedCount . ' imagem(ns) adicionada(s) com sucesso!';
                $messageType = 'success';
            }
            if ($errorCount > 0) {
                $message .= ' ' . $errorCount . ' imagem(ns) não pôde(ram) ser processada(s).';
                $messageType = $uploadedCount > 0 ? 'warning' : 'danger';
            }
        }
    } elseif ($action == 'delete_image') {
        $imageId = $_POST['image_id'] ?? '';

        if (!empty($imageId)) {
            $query = "SELECT image_path FROM qube_gallery_images WHERE id = :id AND gallery_id = :gallery_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $imageId);
            $stmt->bindParam(':gallery_id', $galleryId);
            $stmt->execute();
            $image = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($image) {
                $query = "DELETE FROM qube_gallery_images WHERE id = :id AND gallery_id = :gallery_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $imageId);
                $stmt->bindParam(':gallery_id', $galleryId);

                if ($stmt->execute()) {
                    if (file_exists($image['image_path'])) {
                        unlink($image['image_path']);
                    }
                    $message = 'Imagem removida com sucesso!';
                    $messageType = 'success';
                } else {
                    $message = 'Erro ao remover imagem';
                    $messageType = 'danger';
                }
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
    $query = "SELECT * FROM qube_gallery_images WHERE gallery_id = :gallery_id ORDER BY order_index ASC";
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
            <i class="fas fa-plus"></i> Adicionar Imagens
        </button>
    </div>

    <?php if (count($images) > 0): ?>
        <div class="row">
            <?php foreach ($images as $image): ?>
                <div class="col-md-3 mb-3">
                    <div class="card<?php echo ($gallery['featured_image'] ?? '') == $image['image_path'] ? ' border-primary' : ''; ?> position-relative">
                        <?php if (($gallery['featured_image'] ?? '') == $image['image_path']): ?>
                            <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                <span class="badge bg-primary"><i class="fas fa-star"></i> Capa</span>
                            </div>
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" class="card-img-top" alt="Imagem da galeria" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2">
                            <div class="btn-group btn-group-sm w-100" role="group">
                                <button class="btn btn-outline-primary" onclick="setFeatured('<?php echo htmlspecialchars($image['image_path'], ENT_QUOTES); ?>')" title="Definir como capa">
                                    <i class="fas fa-star"></i> Capa
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteImage('<?php echo $image['id']; ?>')" title="Remover imagem">
                                    <i class="fas fa-trash"></i> Remover
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Nenhuma imagem adicionada. Clique em "Adicionar Imagens" para começar.
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="addImageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Imagens</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_image">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Selecionar Imagens *</label>
                        <input type="file" class="form-control" name="images[]" accept="image/*" multiple required>
                        <small class="text-muted">Você pode selecionar múltiplas imagens. Formatos: JPG, PNG, GIF, WEBP</small>
                        <small class="text-muted d-block mt-1">As imagens serão otimizadas automaticamente (largura máxima: 1200px)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Fazer Upload</button>
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
