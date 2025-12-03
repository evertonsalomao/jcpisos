<?php
require_once 'config/config.php';
checkLogin();

$database = new Database();
$db = $database->getConnection();

$message = '';
$messageType = '';

// Função para gerar UUID
function generateCityUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Cidades exemplo da região de Sorocaba/SP
$cities = [
    ['name' => 'Sorocaba', 'slug' => 'sorocaba', 'order' => 1],
    ['name' => 'São Paulo', 'slug' => 'sao-paulo', 'order' => 2],
    ['name' => 'Votorantim', 'slug' => 'votorantim', 'order' => 3],
    ['name' => 'Itu', 'slug' => 'itu', 'order' => 4],
    ['name' => 'Salto', 'slug' => 'salto', 'order' => 5],
    ['name' => 'Araçoiaba da Serra', 'slug' => 'aracoiaba-da-serra', 'order' => 6],
    ['name' => 'Piedade', 'slug' => 'piedade', 'order' => 7],
    ['name' => 'Porto Feliz', 'slug' => 'porto-feliz', 'order' => 8],
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db->beginTransaction();

        $insertedCount = 0;
        $skippedCount = 0;

        foreach ($cities as $city) {
            // Verifica se já existe
            $checkQuery = "SELECT COUNT(*) FROM qube_cities WHERE slug = :slug";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':slug', $city['slug']);
            $checkStmt->execute();

            if ($checkStmt->fetchColumn() > 0) {
                $skippedCount++;
                continue;
            }

            // Insere cidade
            $id = generateCityUUID();
            $query = "INSERT INTO qube_cities (id, name, slug, is_published, order_index)
                     VALUES (:id, :name, :slug, 1, :order_index)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $city['name']);
            $stmt->bindParam(':slug', $city['slug']);
            $stmt->bindParam(':order_index', $city['order']);
            $stmt->execute();

            $insertedCount++;
        }

        $db->commit();

        // Regenerar sitemap
        @file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/sitemap.php?regenerate=1');

        $message = "Inseridas $insertedCount cidades. $skippedCount já existiam.";
        $messageType = 'success';

    } catch (Exception $e) {
        $db->rollBack();
        $message = 'Erro: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$pageTitle = 'Inserir Cidades Exemplo';
include 'includes/header.php';
?>

<div class="content-card">
    <h5 class="mb-4">Inserir Cidades de Exemplo</h5>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <p>Este script insere <?php echo count($cities); ?> cidades de exemplo da região de Sorocaba/SP:</p>

    <ul class="mb-4">
        <?php foreach ($cities as $city): ?>
            <li><strong><?php echo $city['name']; ?></strong> - <code>/bloquete-em-<?php echo $city['slug']; ?></code></li>
        <?php endforeach; ?>
    </ul>

    <form method="POST">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-database"></i> Inserir Cidades
        </button>
        <a href="cities.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
