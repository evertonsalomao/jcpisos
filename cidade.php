<?php
// Carrega dinamicamente a página de cidade baseada no slug da URL
require_once 'qube-manager/config/database.php';

// Pega o slug da URL (remove "bloquete-em-" do início)
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /');
    exit;
}

// Busca cidade no banco
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM qube_cities WHERE slug = :slug AND is_published = 1 LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':slug', $slug);
$stmt->execute();
$city = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$city) {
    header('HTTP/1.0 404 Not Found');
    include('404.php');
    exit;
}

$cityName = $city['name'];
$citySlug = $city['slug'];

// Carrega o template e substitui os placeholders
ob_start();
include('bloquete-seo.php');
$content = ob_get_clean();

// Substitui todas as ocorrências de [CIDADE] pelo nome da cidade
$content = str_replace('[CIDADE]', $cityName, $content);
$content = str_replace('Sorocaba', $cityName, $content);

// Atualiza o título
$content = preg_replace(
    '/<title>.*?<\/title>/',
    '<title>Bloquetes em ' . $cityName . ' - SP - JC Pisos Intertravados de Concreto</title>',
    $content
);

// Atualiza meta description
$content = preg_replace(
    '/<meta[^>]*name=["\']description["\'][^>]*>/',
    '<meta name="description" content="Bloquetes de concreto em ' . $cityName . '. Pisos intertravados para obras residenciais, comerciais e industriais. Orçamento grátis!">',
    $content
);

// Atualiza o H1 principal
$content = preg_replace(
    '/<div style="color: #0da7a6" id="cidade">.*?<\/div>/',
    '<div style="color: #0da7a6" id="cidade">' . $cityName . '</div>',
    $content
);

echo $content;
?>
