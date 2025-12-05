<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['qube_user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Buscar produtos
$query = "SELECT * FROM qube_products ORDER BY order_index ASC, title ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar cores para o formulário
$queryColors = "SELECT * FROM qube_colors ORDER BY order_index ASC";
$stmtColors = $db->prepare($queryColors);
$stmtColors->execute();
$colors = $stmtColors->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciar Produtos</h1>
        <a href="product-edit.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Produto
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Ordem</th>
                        <th>Visualizar</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <img src="/<?php echo htmlspecialchars($product['image_path']); ?>"
                                 alt="<?php echo htmlspecialchars($product['title']); ?>"
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><?php echo htmlspecialchars($product['title']); ?></td>
                        <td><code><?php echo htmlspecialchars($product['slug']); ?></code></td>
                        <td>
                            <span class="badge bg-<?php echo $product['is_published'] ? 'success' : 'secondary'; ?>">
                                <?php echo $product['is_published'] ? 'Publicado' : 'Rascunho'; ?>
                            </span>
                        </td>
                        <td><?php echo $product['order_index']; ?></td>
                        <td>
                            <a href="/produto/<?php echo $product['slug']; ?>" target="_blank" class="text-primary">
                                <i class="fas fa-external-link-alt"></i> Ver Página
                            </a>
                        </td>
                        <td class="table-actions">
                            <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct('<?php echo $product['id']; ?>', '<?php echo htmlspecialchars($product['title']); ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Deletar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="product-delete.php">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="delete_id">
                    <p>Tem certeza que deseja excluir o produto <strong id="delete_title"></strong>?</p>
                    <p class="text-danger"><strong>Esta ação não pode ser desfeita!</strong></p>
                    <p>Todos os dados relacionados (dimensões, vantagens, aplicações, FAQs) serão excluídos.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Sim, Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteProduct(id, title) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_title').textContent = title;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
