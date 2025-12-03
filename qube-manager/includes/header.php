<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Painel'; ?> - Qube Manager</title>
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

        .content-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #e55f00;
            border-color: #e55f00;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover,
        .btn-outline-primary.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
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
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-folder"></i> Categorias
            </a>
            <a href="galleries.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'galleries.php' ? 'active' : ''; ?>">
                <i class="fas fa-images"></i> Galerias
            </a>
            <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Usuários
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div>
                <h4 class="mb-0"><?php echo $pageTitle ?? 'Painel'; ?></h4>
                <small class="text-muted">Bem-vindo, <?php echo htmlspecialchars($_SESSION['qube_username']); ?>!</small>
            </div>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
