<?php
require_once 'config.php';
requireLogin();

$categories = $supabase->select('cms_categories', 'order=created_at.desc');
$galleries = $supabase->select('cms_galleries', 'order=created_at.desc&limit=10');
$users = $supabase->select('cms_users', 'select=id');

$totalCategories = $categories ? count($categories) : 0;
$totalGalleries = $galleries ? count($galleries) : 0;
$totalUsers = $users ? count($users) : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Qube Manager</title>
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
        .nav-link:hover {
            color: #ff6b00 !important;
        }
        .main-content {
            padding: 30px 0;
        }
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stats-card h3 {
            color: #666;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .stats-card .number {
            color: #1a1a1a;
            font-size: 36px;
            font-weight: 700;
        }
        .stats-card .icon {
            font-size: 40px;
            color: #ff6b00;
        }
        .welcome-card {
            background: linear-gradient(135deg, #ff6b00 0%, #ff8533 100%);
            color: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(255, 107, 0, 0.3);
        }
        .welcome-card h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .quick-actions {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .quick-actions h3 {
            margin-bottom: 20px;
            color: #1a1a1a;
        }
        .action-btn {
            display: block;
            background: #ff6b00;
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 10px;
            transition: all 0.3s;
            text-align: center;
            font-weight: 500;
        }
        .action-btn:hover {
            background: #e55f00;
            color: white;
            transform: translateX(5px);
        }
        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categorias</a>
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
        <div class="welcome-card">
            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class="mb-0">Gerencie o conteúdo do site Qube através deste painel.</p>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3>Categorias</h3>
                            <div class="number"><?php echo $totalCategories; ?></div>
                        </div>
                        <div class="icon">📁</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3>Galerias</h3>
                            <div class="number"><?php echo $totalGalleries; ?></div>
                        </div>
                        <div class="icon">🖼️</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3>Usuários</h3>
                            <div class="number"><?php echo $totalUsers; ?></div>
                        </div>
                        <div class="icon">👥</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Ações Rápidas</h3>
            <div class="row">
                <div class="col-md-4">
                    <a href="categories.php" class="action-btn">➕ Nova Categoria</a>
                </div>
                <div class="col-md-4">
                    <a href="galleries.php" class="action-btn">➕ Nova Galeria</a>
                </div>
                <div class="col-md-4">
                    <a href="users.php" class="action-btn">➕ Novo Usuário</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
