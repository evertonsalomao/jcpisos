<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Corrigindo caminhos de imagens...</h2>";

try {
    $db->beginTransaction();

    $query = "UPDATE qube_gallery_images SET image_path = REPLACE(image_path, 'qube-manager/uploads/', 'uploads/') WHERE image_path LIKE 'qube-manager/uploads/%'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $imagesUpdated = $stmt->rowCount();

    $query = "UPDATE qube_galleries SET featured_image = REPLACE(featured_image, 'qube-manager/uploads/', 'uploads/') WHERE featured_image LIKE 'qube-manager/uploads/%'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $galleriesUpdated = $stmt->rowCount();

    $db->commit();

    echo "<p style='color: green;'>✓ Caminhos atualizados com sucesso!</p>";
    echo "<p>- $imagesUpdated imagens de galeria atualizadas</p>";
    echo "<p>- $galleriesUpdated imagens de capa atualizadas</p>";
    echo "<p><a href='galleries.php'>Voltar para Galerias</a></p>";

} catch (Exception $e) {
    $db->rollBack();
    echo "<p style='color: red;'>✗ Erro ao atualizar caminhos: " . $e->getMessage() . "</p>";
}
?>
