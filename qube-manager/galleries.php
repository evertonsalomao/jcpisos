<?php
require_once 'config/config.php';
checkLogin();

$pageTitle = 'Galerias';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $title = trim($_POST['title']);
            $category_id = $_POST['category_id'];

            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
                $uploadDir = __DIR__ . '/uploads/galleries/';
                $featuredFileName = uniqid() . '_' . basename($_FILES['featured_image']['name']);
                $featuredFilePath = $uploadDir . $featuredFileName;

                if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $featuredFilePath)) {
                    $data = [
                        'title' => $title,
                        'category_id' => $category_id,
                        'featured_image' => 'qube-manager/uploads/galleries/' . $featuredFileName
                    ];

                    $gallery = $supabase->insert('cms_galleries', $data);

                    if ($gallery && isset($gallery[0]['id'])) {
                        $galleryId = $gallery[0]['id'];

                        if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                            $imageOrder = 0;
                            foreach ($_FILES['images']['name'] as $key => $name) {
                                if ($_FILES['images']['error'][$key] === 0) {
                                    $fileName = uniqid() . '_' . basename($name);
                                    $filePath = $uploadDir . $fileName;

                                    if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $filePath)) {
                                        $imageData = [
                                            'gallery_id' => $galleryId,
                                            'image_path' => 'qube-manager/uploads/galleries/' . $fileName,
                                            'image_order' => $imageOrder++
                                        ];
                                        $supabase->insert('cms_gallery_images', $imageData);
                                    }
                                }
                            }
                        }

                        $message = 'Galeria criada com sucesso!';
                        $messageType = 'success';
                    }
                }
            } else {
                $message = 'Por favor, selecione uma imagem de destaque';
                $messageType = 'danger';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = $_POST['id'];

            $gallery = $supabase->select('cms_galleries', 'id=eq.' . urlencode($id));
            if ($gallery && isset($gallery[0])) {
                $images = $supabase->select('cms_gallery_images', 'gallery_id=eq.' . urlencode($id));

                if ($images) {
                    foreach ($images as $image) {
                        $imagePath = __DIR__ . '/' . $image['image_path'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }
                }

                $featuredPath = __DIR__ . '/' . $gallery[0]['featured_image'];
                if (file_exists($featuredPath)) {
                    unlink($featuredPath);
                }

                if ($supabase->delete('cms_galleries', 'id=eq.' . urlencode($id))) {
                    $message = 'Galeria excluída com sucesso!';
                    $messageType = 'success';
                }
            }
        } elseif ($_POST['action'] === 'add_images') {
            $galleryId = $_POST['gallery_id'];

            if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $uploadDir = __DIR__ . '/uploads/galleries/';

                $existingImages = $supabase->select('cms_gallery_images', 'gallery_id=eq.' . urlencode($galleryId) . '&order=image_order.desc&limit=1');
                $imageOrder = $existingImages && isset($existingImages[0]) ? $existingImages[0]['image_order'] + 1 : 0;

                foreach ($_FILES['images']['name'] as $key => $name) {
                    if ($_FILES['images']['error'][$key] === 0) {
                        $fileName = uniqid() . '_' . basename($name);
                        $filePath = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $filePath)) {
                            $imageData = [
                                'gallery_id' => $galleryId,
                                'image_path' => 'qube-manager/uploads/galleries/' . $fileName,
                                'image_order' => $imageOrder++
                            ];
                            $supabase->insert('cms_gallery_images', $imageData);
                        }
                    }
                }

                $message = 'Imagens adicionadas com sucesso!';
                $messageType = 'success';
            }
        } elseif ($_POST['action'] === 'delete_image') {
            $imageId = $_POST['image_id'];

            $image = $supabase->select('cms_gallery_images', 'id=eq.' . urlencode($imageId));
            if ($image && isset($image[0])) {
                $imagePath = __DIR__ . '/' . $image[0]['image_path'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                if ($supabase->delete('cms_gallery_images', 'id=eq.' . urlencode($imageId))) {
                    $message = 'Imagem excluída com sucesso!';
                    $messageType = 'success';
                }
            }
        }
    }
}

$categories = $supabase->select('cms_categories', 'order=name.asc');
$galleries = $supabase->select('cms_galleries', 'select=*,cms_categories(name)&order=created_at.desc');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerias - Qube Manager</title>
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
            margin-bottom: 20px;
        }
        .btn-primary {
            background: #ff6b00;
            border: none;
        }
        .btn-primary:hover {
            background: #e55f00;
        }
        .gallery-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .gallery-card:hover {
            transform: translateY(-5px);
        }
        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .gallery-info {
            padding: 15px;
        }
        .gallery-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #1a1a1a;
        }
        .gallery-category {
            color: #666;
            font-size: 14px;
        }
        .gallery-actions {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }
        .file-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-upload-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }
        .file-upload-label {
            display: inline-block;
            padding: 8px 15px;
            background: #f0f0f0;
            color: #333;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .file-upload-label:hover {
            background: #e0e0e0;
        }
        .image-preview {
            display: inline-block;
            margin: 10px;
            position: relative;
        }
        .image-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .image-preview-remove {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
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
                        <a class="nav-link active" href="galleries.php">Galerias</a>
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
                <h1>Galerias de Obras</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    ➕ Nova Galeria
                </button>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if ($galleries && count($galleries) > 0): ?>
                <?php foreach ($galleries as $gallery): ?>
                    <div class="col-md-4 mb-4">
                        <div class="gallery-card">
                            <img src="../<?php echo htmlspecialchars($gallery['featured_image']); ?>" class="gallery-image" alt="<?php echo htmlspecialchars($gallery['title']); ?>">
                            <div class="gallery-info">
                                <div class="gallery-title"><?php echo htmlspecialchars($gallery['title']); ?></div>
                                <div class="gallery-category"><?php echo htmlspecialchars($gallery['cms_categories']['name'] ?? 'Sem categoria'); ?></div>
                            </div>
                            <div class="gallery-actions">
                                <button class="btn btn-sm btn-info" onclick="viewImages('<?php echo $gallery['id']; ?>')">👁️ Ver Imagens</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteGallery('<?php echo $gallery['id']; ?>', '<?php echo htmlspecialchars($gallery['title'], ENT_QUOTES); ?>')">🗑️</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="content-card text-center py-5">
                        <p class="text-muted">Nenhuma galeria cadastrada</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-header">
                        <h5 class="modal-title">Nova Galeria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Título da Galeria</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoria</label>
                            <select class="form-control" name="category_id" required>
                                <option value="">Selecione uma categoria</option>
                                <?php if ($categories): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagem de Destaque</label>
                            <input type="file" class="form-control" name="featured_image" accept="image/*" required>
                            <small class="text-muted">Esta será a imagem principal da galeria</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagens da Galeria</label>
                            <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                            <small class="text-muted">Selecione múltiplas imagens (Ctrl+Clique ou Cmd+Clique)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Galeria</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewImagesModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Imagens da Galeria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="imagesContainer">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="showAddImagesForm()">➕ Adicionar Imagens</button>
                </div>
            </div>
        </div>
    </div>

    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentGalleryId = null;

        function viewImages(galleryId) {
            currentGalleryId = galleryId;
            fetch('api_get_images.php?gallery_id=' + galleryId)
                .then(response => response.json())
                .then(data => {
                    let html = '<div class="row">';
                    if (data.length > 0) {
                        data.forEach(image => {
                            html += `
                                <div class="col-md-3 mb-3">
                                    <div class="position-relative">
                                        <img src="../${image.image_path}" class="img-fluid rounded" style="width:100%; height:200px; object-fit:cover;">
                                        <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" onclick="deleteImage('${image.id}')">🗑️</button>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = '<p class="text-center">Nenhuma imagem nesta galeria</p>';
                    }
                    html += '</div>';
                    document.getElementById('imagesContainer').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('viewImagesModal')).show();
                });
        }

        function deleteGallery(id, title) {
            if (confirm('Tem certeza que deseja excluir a galeria "' + title + '"? Todas as imagens também serão excluídas.')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        function deleteImage(imageId) {
            if (confirm('Tem certeza que deseja excluir esta imagem?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_image">
                    <input type="hidden" name="image_id" value="${imageId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showAddImagesForm() {
            const html = `
                <form method="POST" enctype="multipart/form-data" class="mt-3 p-3 border rounded">
                    <input type="hidden" name="action" value="add_images">
                    <input type="hidden" name="gallery_id" value="${currentGalleryId}">
                    <div class="mb-3">
                        <label class="form-label">Selecione as Imagens</label>
                        <input type="file" class="form-control" name="images[]" accept="image/*" multiple required>
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar Imagens</button>
                </form>
            `;
            document.getElementById('imagesContainer').insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>
