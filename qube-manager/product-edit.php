<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$productId = $_GET['id'] ?? null;
$isEdit = !empty($productId);

// Se edição, busca dados do produto
$product = null;
$dimensions = [];
$advantages = [];
$applications = [];
$faqs = [];
$selectedColors = [];

if ($isEdit) {
    // Buscar produto
    $query = "SELECT * FROM qube_products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $_SESSION['error'] = 'Produto não encontrado';
        header('Location: products.php');
        exit;
    }

    // Buscar dimensões
    $query = "SELECT * FROM qube_product_dimensions WHERE product_id = :id ORDER BY order_index ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    $dimensions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar vantagens
    $query = "SELECT * FROM qube_product_advantages WHERE product_id = :id ORDER BY order_index ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    $advantages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar aplicações
    $query = "SELECT * FROM qube_product_applications WHERE product_id = :id ORDER BY order_index ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar FAQs
    $query = "SELECT * FROM qube_product_faqs WHERE product_id = :id ORDER BY order_index ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar cores selecionadas
    $query = "SELECT color_id FROM qube_product_colors WHERE product_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $productId);
    $stmt->execute();
    $selectedColors = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Buscar todas as cores disponíveis
$query = "SELECT * FROM qube_colors ORDER BY order_index ASC, name ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$allColors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<style>
.repeater-item {
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}
.repeater-item .btn-remove {
    float: right;
}
.color-checkbox {
    display: inline-block;
    margin-right: 20px;
    margin-bottom: 10px;
}
.color-checkbox img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 5px;
    vertical-align: middle;
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $isEdit ? 'Editar Produto' : 'Novo Produto'; ?></h1>
        <a href="products.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form method="POST" action="product-save.php" enctype="multipart/form-data">
        <?php if ($isEdit): ?>
            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
        <?php endif; ?>

        <!-- Informações Básicas -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informações Básicas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Título do Produto *</label>
                        <input type="text" class="form-control" name="title"
                               value="<?php echo $product['title'] ?? ''; ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (URL) *</label>
                        <input type="text" class="form-control" name="slug"
                               value="<?php echo $product['slug'] ?? ''; ?>" required>
                        <small class="text-muted">Ex: piso-16-faces</small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Descrição Principal *</label>
                        <textarea class="form-control" name="description" rows="4" required><?php echo $product['description'] ?? ''; ?></textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Descrição Abaixo da Imagem</label>
                        <textarea class="form-control" name="description_below_image" rows="3"><?php echo $product['description_below_image'] ?? ''; ?></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Imagem do Produto *</label>
                        <input type="file" class="form-control" name="image" accept="image/*" <?php echo !$isEdit ? 'required' : ''; ?>>
                        <?php if ($isEdit && $product['image_path']): ?>
                            <div class="mt-2">
                                <img src="/<?php echo $product['image_path']; ?>" style="width: 150px; border-radius: 4px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="is_published">
                            <option value="0" <?php echo isset($product['is_published']) && !$product['is_published'] ? 'selected' : ''; ?>>Rascunho</option>
                            <option value="1" <?php echo isset($product['is_published']) && $product['is_published'] ? 'selected' : ''; ?>>Publicado</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ordem</label>
                        <input type="number" class="form-control" name="order_index"
                               value="<?php echo $product['order_index'] ?? 0; ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Cores -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Cores Disponíveis</h5>
            </div>
            <div class="card-body">
                <?php if (empty($allColors)): ?>
                    <div class="alert alert-warning">
                        Nenhuma cor cadastrada. <a href="colors.php">Cadastre cores aqui</a>.
                    </div>
                <?php else: ?>
                    <?php foreach ($allColors as $color): ?>
                        <div class="color-checkbox">
                            <label>
                                <input type="checkbox" name="colors[]" value="<?php echo $color['id']; ?>"
                                       <?php echo in_array($color['id'], $selectedColors) ? 'checked' : ''; ?>>
                                <img src="/<?php echo $color['image_path']; ?>" alt="<?php echo $color['name']; ?>">
                                <?php echo htmlspecialchars($color['name']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Dimensões -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dimensões e Especificações Técnicas</h5>
            </div>
            <div class="card-body">
                <div id="dimensions-container">
                    <?php if (!empty($dimensions)): ?>
                        <?php foreach ($dimensions as $index => $dim): ?>
                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeItem(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label>Dimensão</label>
                                        <input type="text" class="form-control" name="dimensions[<?php echo $index; ?>][dimension]"
                                               value="<?php echo htmlspecialchars($dim['dimension']); ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label>Espessura</label>
                                        <input type="text" class="form-control" name="dimensions[<?php echo $index; ?>][thickness]"
                                               value="<?php echo htmlspecialchars($dim['thickness']); ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label>Resistência (MPA)</label>
                                        <input type="text" class="form-control" name="dimensions[<?php echo $index; ?>][resistance]"
                                               value="<?php echo htmlspecialchars($dim['resistance']); ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label>Ordem</label>
                                        <input type="number" class="form-control" name="dimensions[<?php echo $index; ?>][order_index]"
                                               value="<?php echo $dim['order_index']; ?>">
                                    </div>
                                    <div class="col-md-12">
                                        <label>Indicação de Uso</label>
                                        <textarea class="form-control" name="dimensions[<?php echo $index; ?>][usage_indication]" rows="2" required><?php echo htmlspecialchars($dim['usage_indication']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addDimension()">
                    <i class="fas fa-plus"></i> Adicionar Dimensão
                </button>
            </div>
        </div>

        <!-- Vantagens -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Vantagens do Produto</h5>
            </div>
            <div class="card-body">
                <div id="advantages-container">
                    <?php if (!empty($advantages)): ?>
                        <?php foreach ($advantages as $index => $adv): ?>
                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeItem(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="row">
                                    <div class="col-md-10">
                                        <label>Texto da Vantagem</label>
                                        <textarea class="form-control" name="advantages[<?php echo $index; ?>][text]" rows="2" required><?php echo htmlspecialchars($adv['text']); ?></textarea>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Ordem</label>
                                        <input type="number" class="form-control" name="advantages[<?php echo $index; ?>][order_index]"
                                               value="<?php echo $adv['order_index']; ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addAdvantage()">
                    <i class="fas fa-plus"></i> Adicionar Vantagem
                </button>
            </div>
        </div>

        <!-- Aplicações -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Aplicações do Produto</h5>
            </div>
            <div class="card-body">
                <div id="applications-container">
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $index => $app): ?>
                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeItem(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="row">
                                    <div class="col-md-10">
                                        <label>Texto da Aplicação</label>
                                        <textarea class="form-control" name="applications[<?php echo $index; ?>][text]" rows="2" required><?php echo htmlspecialchars($app['text']); ?></textarea>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Ordem</label>
                                        <input type="number" class="form-control" name="applications[<?php echo $index; ?>][order_index]"
                                               value="<?php echo $app['order_index']; ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addApplication()">
                    <i class="fas fa-plus"></i> Adicionar Aplicação
                </button>
            </div>
        </div>

        <!-- FAQs -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Perguntas Frequentes (FAQs)</h5>
            </div>
            <div class="card-body">
                <div id="faqs-container">
                    <?php if (!empty($faqs)): ?>
                        <?php foreach ($faqs as $index => $faq): ?>
                            <div class="repeater-item">
                                <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeItem(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <div class="row">
                                    <div class="col-md-10">
                                        <label>Pergunta</label>
                                        <input type="text" class="form-control mb-2" name="faqs[<?php echo $index; ?>][question]"
                                               value="<?php echo htmlspecialchars($faq['question']); ?>" required>
                                        <label>Resposta</label>
                                        <textarea class="form-control" name="faqs[<?php echo $index; ?>][answer]" rows="2" required><?php echo htmlspecialchars($faq['answer']); ?></textarea>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Ordem</label>
                                        <input type="number" class="form-control" name="faqs[<?php echo $index; ?>][order_index]"
                                               value="<?php echo $faq['order_index']; ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addFaq()">
                    <i class="fas fa-plus"></i> Adicionar FAQ
                </button>
            </div>
        </div>

        <div class="text-end mb-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Salvar Produto
            </button>
        </div>
    </form>
</div>

<script>
let dimensionIndex = <?php echo !empty($dimensions) ? count($dimensions) : 0; ?>;
let advantageIndex = <?php echo !empty($advantages) ? count($advantages) : 0; ?>;
let applicationIndex = <?php echo !empty($applications) ? count($applications) : 0; ?>;
let faqIndex = <?php echo !empty($faqs) ? count($faqs) : 0; ?>;

function removeItem(btn) {
    btn.closest('.repeater-item').remove();
}

function addDimension() {
    const html = `
        <div class="repeater-item">
            <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeItem(this)">
                <i class="fas fa-times"></i>
            </button>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label>Dimensão</label>
                    <input type="text" class="form-control" name="dimensions[${dimensionIndex}][dimension]" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Espessura</label>
                    <input type="text" class="form-control" name="dimensions[${dimensionIndex}][thickness]" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Resistência (MPA)</label>
                    <input type="text" class="form-control" name="dimensions[${dimensionIndex}][resistance]" required>
                </div>
                <div class="col-md-3 mb-2">
                    <label>Ordem</label>
                    <input type="number" class="form-control" name="dimensions[${dimensionIndex}][order_index]" value="0">
                </div>
                <div class="col-md-12">
                    <label>Indicação de Uso</label>
                    <textarea class="form-control" name="dimensions[${dimensionIndex}][usage_indication]" rows="2" required></textarea>
                </div>
            </div>
        </div>
    `;
    document.getElementById('dimensions-container').insertAdjacentHTML('beforeend', html);
    dimensionIndex++;
}

function addAdvantage() {
    const html = `
        <div class="repeater-item">
            <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeItem(this)">
                <i class="fas fa-times"></i>
            </button>
            <div class="row">
                <div class="col-md-10">
                    <label>Texto da Vantagem</label>
                    <textarea class="form-control" name="advantages[${advantageIndex}][text]" rows="2" required></textarea>
                </div>
                <div class="col-md-2">
                    <label>Ordem</label>
                    <input type="number" class="form-control" name="advantages[${advantageIndex}][order_index]" value="0">
                </div>
            </div>
        </div>
    `;
    document.getElementById('advantages-container').insertAdjacentHTML('beforeend', html);
    advantageIndex++;
}

function addApplication() {
    const html = `
        <div class="repeater-item">
            <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeItem(this)">
                <i class="fas fa-times"></i>
            </button>
            <div class="row">
                <div class="col-md-10">
                    <label>Texto da Aplicação</label>
                    <textarea class="form-control" name="applications[${applicationIndex}][text]" rows="2" required></textarea>
                </div>
                <div class="col-md-2">
                    <label>Ordem</label>
                    <input type="number" class="form-control" name="applications[${applicationIndex}][order_index]" value="0">
                </div>
            </div>
        </div>
    `;
    document.getElementById('applications-container').insertAdjacentHTML('beforeend', html);
    applicationIndex++;
}

function addFaq() {
    const html = `
        <div class="repeater-item">
            <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeItem(this)">
                <i class="fas fa-times"></i>
            </button>
            <div class="row">
                <div class="col-md-10">
                    <label>Pergunta</label>
                    <input type="text" class="form-control mb-2" name="faqs[${faqIndex}][question]" required>
                    <label>Resposta</label>
                    <textarea class="form-control" name="faqs[${faqIndex}][answer]" rows="2" required></textarea>
                </div>
                <div class="col-md-2">
                    <label>Ordem</label>
                    <input type="number" class="form-control" name="faqs[${faqIndex}][order_index]" value="0">
                </div>
            </div>
        </div>
    `;
    document.getElementById('faqs-container').insertAdjacentHTML('beforeend', html);
    faqIndex++;
}

// Auto-gerar slug a partir do título
document.querySelector('input[name="title"]').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
    document.querySelector('input[name="slug"]').value = slug;
});
</script>

<?php include 'includes/footer.php'; ?>
