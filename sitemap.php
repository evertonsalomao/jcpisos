<?php
header('Content-Type: application/xml; charset=utf-8');

require_once 'qube-manager/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Pega o protocolo (http ou https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = $protocol . $_SERVER['HTTP_HOST'];

// Busca todas as cidades publicadas
$query = "SELECT slug, updated_at FROM qube_cities WHERE is_published = 1 ORDER BY order_index ASC, name ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Página Principal -->
    <url>
        <loc><?php echo $domain; ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
    </url>

    <!-- Páginas Estáticas -->
    <url>
        <loc><?php echo $domain; ?>/quem-somos.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc><?php echo $domain; ?>/produto.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc><?php echo $domain; ?>/obras.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc><?php echo $domain; ?>/terraplanagem.php</loc>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc><?php echo $domain; ?>/contato.php</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>

    <!-- Páginas de Cidades Dinâmicas - Bloquetes -->
    <?php foreach ($cities as $city): ?>
    <url>
        <loc><?php echo $domain; ?>/bloquete-em-<?php echo htmlspecialchars($city['slug']); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
        <lastmod><?php echo date('Y-m-d', strtotime($city['updated_at'])); ?></lastmod>
    </url>
    <?php endforeach; ?>

    <!-- Páginas de Cidades Dinâmicas - Pisos Intertravados -->
    <?php foreach ($cities as $city): ?>
    <url>
        <loc><?php echo $domain; ?>/piso-intertravado-em-<?php echo htmlspecialchars($city['slug']); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
        <lastmod><?php echo date('Y-m-d', strtotime($city['updated_at'])); ?></lastmod>
    </url>
    <?php endforeach; ?>
</urlset>
