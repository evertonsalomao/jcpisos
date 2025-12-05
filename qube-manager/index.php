<?php
require_once 'config/config.php';
checkLogin();

$pageTitle = 'Dashboard';

$database = new Database();
$db = $database->getConnection();

$stats = [
    'products' => 0,
    'colors' => 0,
    'categories' => 0,
    'galleries' => 0,
    'images' => 0,
    'cities' => 0,
    'users' => 0
];

$query = "SELECT COUNT(*) as total FROM qube_products";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM qube_colors";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['colors'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM qube_categories";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM qube_galleries";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['galleries'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM qube_gallery_images";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['images'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM qube_cities";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['cities'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT COUNT(*) as total FROM qube_users";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$query = "SELECT g.*, c.name as category_name FROM qube_galleries g
          LEFT JOIN qube_categories c ON g.category_id = c.id
          ORDER BY g.created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$recentGalleries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Qube Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff6b00;
            --secondary-color: #1a1a1a;
            --sidebar-width: 250px;
        }

        body {
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--secondary-color);
            color: white;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            background: var(--primary-color);
            text-align: center;
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255, 107, 0, 0.1);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }

        .sidebar-menu a i {
            width: 25px;
            margin-right: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
        }

        .top-bar {
            background: white;
            padding: 15px 30px;
            margin: -30px -30px 30px -30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stats-card.primary .icon {
            background: rgba(255, 107, 0, 0.1);
            color: var(--primary-color);
        }

        .stats-card.success .icon {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .stats-card.info .icon {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .stats-card.warning .icon {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .stats-card h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }

        .stats-card p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .content-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 30px;
        }

        .btn-logout {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: #c82333;
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>Qube Manager</h4>
            <small>Painel de Gestão</small>
        </div>
        <div class="sidebar-menu">
            <a href="index.php" class="active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="products.php">
                <i class="fas fa-box"></i> Produtos
            </a>
            <a href="colors.php">
                <i class="fas fa-palette"></i> Cores
            </a>
            <a href="categories.php">
                <i class="fas fa-folder"></i> Categorias
            </a>
            <a href="galleries.php">
                <i class="fas fa-images"></i> Galerias
            </a>
            <a href="cities.php">
                <i class="fas fa-map-marked-alt"></i> Cidades
            </a>
            <a href="users.php">
                <i class="fas fa-users"></i> Usuários
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div>
                <h4 class="mb-0"><?php echo $pageTitle; ?></h4>
                <small class="text-muted">Bem-vindo, <?php echo htmlspecialchars($_SESSION['qube_username']); ?>!</small>
            </div>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>

        <div class="row">
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="stats-card primary">
                    <div class="icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3><?php echo $stats['products']; ?></h3>
                    <p>Produtos</p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="stats-card success">
                    <div class="icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3><?php echo $stats['colors']; ?></h3>
                    <p>Cores</p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="stats-card info">
                    <div class="icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <h3><?php echo $stats['categories']; ?></h3>
                    <p>Categorias</p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="stats-card warning">
                    <div class="icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <h3><?php echo $stats['galleries']; ?></h3>
                    <p>Galerias</p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="stats-card info">
                    <div class="icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <h3><?php echo $stats['images']; ?></h3>
                    <p>Imagens</p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="stats-card success">
                    <div class="icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3><?php echo $stats['cities']; ?></h3>
                    <p>Cidades</p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="stats-card warning">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3><?php echo $stats['users']; ?></h3>
                    <p>Usuários</p>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h5 class="mb-4">Galerias Recentes</h5>
            <?php if (count($recentGalleries) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentGalleries as $gallery): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($gallery['title']); ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo htmlspecialchars($gallery['category_name']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($gallery['published']): ?>
                                            <span class="badge bg-success">Publicado</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Rascunho</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo formatDate($gallery['created_at']); ?></td>
                                    <td>
                                        <a href="gallery-edit.php?id=<?php echo $gallery['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Nenhuma galeria criada ainda.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
