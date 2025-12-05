<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['qube_user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: products.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    $productId = $_POST['id'];

    // Busca imagem para deletar
    $query = "SELECT image_path FROM qube_products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Deleta produto (cascade delete cuida do resto)
    $query = "DELETE FROM qube_products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);

    if ($stmt->execute()) {
        // Remove imagem do servidor
        if ($product && file_exists($product['image_path'])) {
            unlink($product['image_path']);
        }

        $_SESSION['success'] = 'Produto excluído com sucesso!';
    } else {
        $_SESSION['error'] = 'Erro ao excluir produto.';
    }

} catch (Exception $e) {
    $_SESSION['error'] = 'Erro ao excluir produto: ' . $e->getMessage();
}

header('Location: products.php');
exit;
?>
