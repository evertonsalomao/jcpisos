<?php
require_once 'config/config.php';
checkLogin();

$pageTitle = 'Cidades';

$database = new Database();
$db = $database->getConnection();

$message = '';
$messageType = '';

// Função para gerar slug
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[áàâãäå]/u', 'a', $text);
    $text = preg_replace('/[éèêë]/u', 'e', $text);
    $text = preg_replace('/[íìîï]/u', 'i', $text);
    $text = preg_replace('/[óòôõö]/u', 'o', $text);
    $text = preg_replace('/[úùûü]/u', 'u', $text);
    $text = preg_replace('/[ç]/u', 'c', $text);
    $text = preg_replace('/[^a-z0-9\s-]/u', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'create') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $order_index = intval($_POST['order_index'] ?? 0);

        if (empty($slug) && !empty($name)) {
            $slug = generateSlug($name);
        }

        if (!empty($name) && !empty($slug)) {
            try {
                $id = generateUUID();
                $query = "INSERT INTO qube_cities (id, name, slug, is_published, order_index)
                         VALUES (:id, :name, :slug, :is_published, :order_index)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':slug', $slug);
                $stmt->bindParam(':is_published', $is_published);
                $stmt->bindParam(':order_index', $order_index);

                if ($stmt->execute()) {
                    // Regenerar sitemap
                    file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/sitemap.php?regenerate=1');
                    $message = 'Cidade criada com sucesso!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Erro ao criar cidade: ' . $e->getMessage();
                $messageType = 'danger';
            }
        }
    } elseif ($action == 'update') {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $order_index = intval($_POST['order_index'] ?? 0);

        if (!empty($id) && !empty($name) && !empty($slug)) {
            try {
                $query = "UPDATE qube_cities
                         SET name = :name, slug = :slug, is_published = :is_published, order_index = :order_index
                         WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':slug', $slug);
                $stmt->bindParam(':is_published', $is_published);
                $stmt->bindParam(':order_index', $order_index);

                if ($stmt->execute()) {
                    // Regenerar sitemap
                    file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/sitemap.php?regenerate=1');
                    $message = 'Cidade atualizada com sucesso!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Erro ao atualizar cidade: ' . $e->getMessage();
                $messageType = 'danger';
            }
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'] ?? '';

        if (!empty($id)) {
            try {
                $query = "DELETE FROM qube_cities WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $id);

                if ($stmt->execute()) {
                    // Regenerar sitemap
                    file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/sitemap.php?regenerate=1');
                    $message = 'Cidade excluída com sucesso!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Erro ao excluir cidade: ' . $e->getMessage();
                $messageType = 'danger';
            }
        }
    }
}

$query = "SELECT * FROM qube_cities ORDER BY order_index ASC, name ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Gerenciar Cidades</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus"></i> Nova Cidade
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Ordem</th>
                    <th>URL</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cities as $city): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($city['name']); ?></td>
                        <td><code><?php echo htmlspecialchars($city['slug']); ?></code></td>
                        <td>
                            <span class="badge bg-<?php echo $city['is_published'] ? 'success' : 'secondary'; ?>">
                                <?php echo $city['is_published'] ? 'Publicado' : 'Rascunho'; ?>
                            </span>
                        </td>
                        <td><?php echo $city['order_index']; ?></td>
                        <td>
                            <a href="/bloquete-em-<?php echo $city['slug']; ?>" target="_blank" class="text-primary">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-primary"
                                onclick="editCity('<?php echo $city['id']; ?>', '<?php echo htmlspecialchars($city['name']); ?>', '<?php echo htmlspecialchars($city['slug']); ?>', '<?php echo $city['is_published']; ?>', '<?php echo $city['order_index']; ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger"
                                onclick="deleteCity('<?php echo $city['id']; ?>', '<?php echo htmlspecialchars($city['name']); ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Criar -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Cidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome da Cidade *</label>
                        <input type="text" class="form-control" name="name" id="create_name" required>
                        <small class="text-muted">Ex: São Paulo, Sorocaba</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug (URL amigável)</label>
                        <input type="text" class="form-control" name="slug" id="create_slug">
                        <small class="text-muted">Deixe vazio para gerar automaticamente. Ex: sao-paulo, sorocaba</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" class="form-control" name="order_index" value="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_published" id="create_published" checked>
                            <label class="form-check-label" for="create_published">
                                Publicado
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Cidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome da Cidade *</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug (URL amigável) *</label>
                        <input type="text" class="form-control" name="slug" id="edit_slug" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" class="form-control" name="order_index" id="edit_order_index">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_published" id="edit_published">
                            <label class="form-check-label" for="edit_published">
                                Publicado
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Deletar -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a cidade <strong id="delete_name"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita!</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gerar slug automaticamente ao digitar o nome
document.getElementById('create_name').addEventListener('input', function(e) {
    let name = e.target.value;
    let slug = name.toLowerCase()
        .replace(/[áàâãäå]/g, 'a')
        .replace(/[éèêë]/g, 'e')
        .replace(/[íìîï]/g, 'i')
        .replace(/[óòôõö]/g, 'o')
        .replace(/[úùûü]/g, 'u')
        .replace(/[ç]/g, 'c')
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('create_slug').value = slug;
});

function editCity(id, name, slug, published, order) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_slug').value = slug;
    document.getElementById('edit_order_index').value = order;
    document.getElementById('edit_published').checked = published == 1;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteCity(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>
