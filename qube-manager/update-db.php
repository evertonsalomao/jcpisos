<?php
require_once 'config/config.php';
checkLogin();

$database = new Database();
$db = $database->getConnection();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Adicionar coluna title
        $db->exec("ALTER TABLE qube_gallery_images ADD COLUMN title VARCHAR(255) DEFAULT '' AFTER image_url");
        $message .= "Coluna 'title' adicionada com sucesso.<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            $message .= "Coluna 'title' já existe.<br>";
        } else {
            $message .= "Erro ao adicionar coluna 'title': " . $e->getMessage() . "<br>";
            $messageType = 'danger';
        }
    }

    try {
        // Renomear image_url para image_path
        $db->exec("ALTER TABLE qube_gallery_images CHANGE COLUMN image_url image_path TEXT NOT NULL");
        $message .= "Coluna 'image_url' renomeada para 'image_path' com sucesso.<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), "Unknown column 'image_url'") !== false) {
            $message .= "Coluna já foi renomeada para 'image_path'.<br>";
        } else {
            $message .= "Erro ao renomear coluna: " . $e->getMessage() . "<br>";
            $messageType = 'danger';
        }
    }

    if (empty($messageType)) {
        $messageType = 'success';
        $message .= "<br><strong>Banco de dados atualizado com sucesso!</strong>";
    }
}

include 'includes/header.php';
?>

<div class="content-card">
    <h5 class="mb-4">Atualizar Banco de Dados</h5>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Atenção!</strong> Esta página irá atualizar a estrutura do banco de dados para adicionar suporte ao novo sistema de upload de imagens.
    </div>

    <h6>Alterações que serão feitas:</h6>
    <ul>
        <li>Adicionar coluna <code>title</code> na tabela <code>qube_gallery_images</code></li>
        <li>Renomear coluna <code>image_url</code> para <code>image_path</code> na tabela <code>qube_gallery_images</code></li>
    </ul>

    <form method="POST" onsubmit="return confirm('Tem certeza que deseja atualizar o banco de dados?');">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-database"></i> Atualizar Banco de Dados
        </button>
        <a href="galleries.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
