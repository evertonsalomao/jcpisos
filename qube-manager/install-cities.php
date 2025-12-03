<?php
require_once 'config/config.php';
checkLogin();

$database = new Database();
$db = $database->getConnection();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $sql = file_get_contents(__DIR__ . '/create-cities-table.sql');
        $db->exec($sql);
        $message = 'Tabela de cidades criada com sucesso!';
        $messageType = 'success';
    } catch (Exception $e) {
        $message = 'Erro: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$pageTitle = 'Instalar Tabela de Cidades';
include 'includes/header.php';
?>

<div class="content-card">
    <h5 class="mb-4">Instalação da Tabela de Cidades</h5>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <p>Este instalador criará a tabela <code>qube_cities</code> no banco de dados.</p>

    <form method="POST">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-database"></i> Criar Tabela
        </button>
        <a href="cities.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
