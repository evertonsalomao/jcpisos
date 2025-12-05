<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: products.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    $db->beginTransaction();

    $productId = $_POST['product_id'] ?? null;
    $isEdit = !empty($productId);

    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $description = $_POST['description'];
    $descriptionBelowImage = $_POST['description_below_image'] ?? '';
    $isPublished = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;
    $orderIndex = $_POST['order_index'] ?? 0;

    // Upload de imagem
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/products/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;

            // Se edição, remove imagem antiga
            if ($isEdit) {
                $queryOld = "SELECT image_path FROM qube_products WHERE id = :id";
                $stmtOld = $db->prepare($queryOld);
                $stmtOld->bindParam(':id', $productId);
                $stmtOld->execute();
                $oldImage = $stmtOld->fetch(PDO::FETCH_ASSOC);
                if ($oldImage && file_exists($oldImage['image_path'])) {
                    unlink($oldImage['image_path']);
                }
            }
        }
    }

    // CRIAR ou ATUALIZAR produto
    if ($isEdit) {
        if ($imagePath) {
            $query = "UPDATE qube_products SET title = :title, slug = :slug, description = :description,
                      description_below_image = :desc_below, image_path = :image, is_published = :published,
                      order_index = :order, updated_at = now() WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':image', $imagePath);
        } else {
            $query = "UPDATE qube_products SET title = :title, slug = :slug, description = :description,
                      description_below_image = :desc_below, is_published = :published,
                      order_index = :order, updated_at = now() WHERE id = :id";
            $stmt = $db->prepare($query);
        }
        $stmt->bindParam(':id', $productId);
    } else {
        if (!$imagePath) {
            throw new Exception('Imagem é obrigatória para novos produtos');
        }

        $query = "INSERT INTO qube_products (title, slug, description, description_below_image, image_path, is_published, order_index)
                  VALUES (:title, :slug, :description, :desc_below, :image, :published, :order)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':image', $imagePath);
    }

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':slug', $slug);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':desc_below', $descriptionBelowImage);
    $stmt->bindParam(':published', $isPublished);
    $stmt->bindParam(':order', $orderIndex);
    $stmt->execute();

    if (!$isEdit) {
        $productId = $db->lastInsertId();
    }

    // CORES: Remove e recria
    $query = "DELETE FROM qube_product_colors WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    if (!empty($_POST['colors'])) {
        $colorOrder = 0;
        foreach ($_POST['colors'] as $colorId) {
            $query = "INSERT INTO qube_product_colors (product_id, color_id, order_index) VALUES (:product_id, :color_id, :order)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':color_id', $colorId);
            $stmt->bindParam(':order', $colorOrder);
            $stmt->execute();
            $colorOrder++;
        }
    }

    // DIMENSÕES: Remove e recria
    $query = "DELETE FROM qube_product_dimensions WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    if (!empty($_POST['dimensions'])) {
        foreach ($_POST['dimensions'] as $dim) {
            $query = "INSERT INTO qube_product_dimensions (product_id, dimension, thickness, resistance, usage_indication, order_index)
                      VALUES (:product_id, :dimension, :thickness, :resistance, :usage, :order)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':dimension', $dim['dimension']);
            $stmt->bindParam(':thickness', $dim['thickness']);
            $stmt->bindParam(':resistance', $dim['resistance']);
            $stmt->bindParam(':usage', $dim['usage_indication']);
            $stmt->bindParam(':order', $dim['order_index']);
            $stmt->execute();
        }
    }

    // VANTAGENS: Remove e recria
    $query = "DELETE FROM qube_product_advantages WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    if (!empty($_POST['advantages'])) {
        foreach ($_POST['advantages'] as $adv) {
            $query = "INSERT INTO qube_product_advantages (product_id, text, order_index) VALUES (:product_id, :text, :order)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':text', $adv['text']);
            $stmt->bindParam(':order', $adv['order_index']);
            $stmt->execute();
        }
    }

    // APLICAÇÕES: Remove e recria
    $query = "DELETE FROM qube_product_applications WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    if (!empty($_POST['applications'])) {
        foreach ($_POST['applications'] as $app) {
            $query = "INSERT INTO qube_product_applications (product_id, text, order_index) VALUES (:product_id, :text, :order)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':text', $app['text']);
            $stmt->bindParam(':order', $app['order_index']);
            $stmt->execute();
        }
    }

    // FAQs: Remove e recria
    $query = "DELETE FROM qube_product_faqs WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    if (!empty($_POST['faqs'])) {
        foreach ($_POST['faqs'] as $faq) {
            $query = "INSERT INTO qube_product_faqs (product_id, question, answer, order_index) VALUES (:product_id, :question, :answer, :order)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':question', $faq['question']);
            $stmt->bindParam(':answer', $faq['answer']);
            $stmt->bindParam(':order', $faq['order_index']);
            $stmt->execute();
        }
    }

    $db->commit();
    $_SESSION['success'] = $isEdit ? 'Produto atualizado com sucesso!' : 'Produto criado com sucesso!';
    header('Location: products.php');

} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['error'] = 'Erro ao salvar produto: ' . $e->getMessage();
    header('Location: ' . ($isEdit ? 'product-edit.php?id=' . $productId : 'product-edit.php'));
}
exit;
?>
