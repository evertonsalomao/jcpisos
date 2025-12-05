<?php
// Carrega dinamicamente a página de produto baseada no slug da URL
require_once 'qube-manager/config/database.php';

// Pega o slug da URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /');
    exit;
}

// Busca produto no banco
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM qube_products WHERE slug = :slug AND is_published = 1 LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':slug', $slug);
$stmt->execute();
$productData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$productData) {
    header('HTTP/1.0 404 Not Found');
    include('404.php');
    exit;
}

// Buscar cores do produto
$query = "SELECT c.* FROM qube_colors c
          INNER JOIN qube_product_colors pc ON c.id = pc.color_id
          WHERE pc.product_id = :product_id
          ORDER BY pc.order_index ASC";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $productData['id']);
$stmt->execute();
$productColors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar dimensões
$query = "SELECT * FROM qube_product_dimensions WHERE product_id = :product_id ORDER BY order_index ASC";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $productData['id']);
$stmt->execute();
$productDimensions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar vantagens
$query = "SELECT * FROM qube_product_advantages WHERE product_id = :product_id ORDER BY order_index ASC";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $productData['id']);
$stmt->execute();
$productAdvantages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar aplicações
$query = "SELECT * FROM qube_product_applications WHERE product_id = :product_id ORDER BY order_index ASC";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $productData['id']);
$stmt->execute();
$productApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar galerias relacionadas ao produto
$query = "SELECT g.*, gi.image_path FROM qube_galleries g
          INNER JOIN qube_gallery_products gp ON g.id = gp.gallery_id
          LEFT JOIN qube_gallery_images gi ON g.id = gi.gallery_id
          WHERE gp.product_id = :product_id AND g.published = 1
          GROUP BY g.id
          ORDER BY g.order_index ASC
          LIMIT 12";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $productData['id']);
$stmt->execute();
$productGalleries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar FAQs
$query = "SELECT * FROM qube_product_faqs WHERE product_id = :product_id ORDER BY order_index ASC";
$stmt = $db->prepare($query);
$stmt->bindParam(':product_id', $productData['id']);
$stmt->execute();
$productFaqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($productData['title']); ?> - JC Pisos Intertravados de Concreto Sorocaba SP</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="<?php echo htmlspecialchars($productData['title']); ?>" name="keywords">
    <meta content="<?php echo htmlspecialchars(substr($productData['description'], 0, 160)); ?>" name="description">

    <?php
    define('U', '');
    define('MENU', 0);
    include(U . 'includes/header-interna.php');
    ?>
</head>

<body>
    <!-- Page Header Start -->
    <div class="container-fluid breadcrumb-novo">
        <nav aria-label="breadcrumb animated slideInDown">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="" href="/">Home</a></li>
                <li class="breadcrumb-item"><a class="" href="/produtos">Produtos</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($productData['title']); ?></li>
            </ol>
        </nav>
    </div>
    <!-- Page Header End -->

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mt-4 mt-lg-0 centraliza-mobile">
                    <img src="/<?php echo htmlspecialchars($productData['image_path']); ?>"
                         alt="<?php echo htmlspecialchars($productData['title']); ?>"
                         class="product-image img-fluid">

                    <?php if (!empty($productData['description_below_image'])): ?>
                        <p class="feature-text text-center show-lg">
                            <?php echo htmlspecialchars($productData['description_below_image']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="col-lg-6">
                    <h2 class="product-title">JC Indica</h2>
                    <h1 class="product-subtitle"><?php echo htmlspecialchars($productData['title']); ?></h1>
                    <hr>
                    <p class="product-description">
                        <?php echo nl2br(htmlspecialchars($productData['description'])); ?>
                    </p>

                    <?php if (!empty($productData['description_below_image'])): ?>
                        <p class="feature-text text-center show-xs">
                            <?php echo htmlspecialchars($productData['description_below_image']); ?>
                        </p>
                    <?php endif; ?>

                    <button class="btn btn-primary py-3 px-5" type="button">Solicitar Orçamento</button>

                    <?php if (!empty($productColors)): ?>
                        <hr style="border-top: 1px solid #444">
                        <h3 class="section-title">CORES</h3>
                        <div class="container padding-zero">
                            <div class="row">
                                <?php foreach ($productColors as $color): ?>
                                    <div class="col-6 col-sm-4 color-item">
                                        <div><img src="/<?php echo htmlspecialchars($color['image_path']); ?>" alt="<?php echo htmlspecialchars($color['name']); ?>"></div>
                                        <p class="color-name"><?php echo strtoupper(htmlspecialchars($color['name'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($productDimensions)): ?>
    <!-- Dimensões -->
    <div class="container my-5">
        <h4 class="mb-4" style="font-weight: 400;">Dimensões e especificações técnicas</h4>

        <!-- DESKTOP TABLE -->
        <div class="d-none d-lg-block">
            <table class="table table-custom table-equal-desktop">
                <thead>
                    <tr>
                        <th>Dimensão</th>
                        <th>Espessura</th>
                        <th>Resistência (MPA)</th>
                        <th>Indicação de uso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productDimensions as $dim): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dim['dimension']); ?></td>
                            <td><?php echo htmlspecialchars($dim['thickness']); ?></td>
                            <td><?php echo htmlspecialchars($dim['resistance']); ?></td>
                            <td><?php echo htmlspecialchars($dim['usage_indication']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE CARDS -->
        <div class="d-lg-none">
            <?php foreach ($productDimensions as $dim): ?>
                <div class="spec-card">
                    <div class="spec-card-title">Dimensão</div>
                    <p><?php echo htmlspecialchars($dim['dimension']); ?></p>

                    <div class="spec-card-title">Espessura</div>
                    <p><?php echo htmlspecialchars($dim['thickness']); ?></p>

                    <div class="spec-card-title">Resistência (MPA)</div>
                    <p><?php echo htmlspecialchars($dim['resistance']); ?></p>

                    <div class="spec-card-title">Indicação de uso</div>
                    <p><?php echo htmlspecialchars($dim['usage_indication']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($productAdvantages)): ?>
    <!-- Vantagens -->
    <div class="container my-5">
        <div class="row d-none d-lg-flex g-3">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s">
                <h1 class="mb-4">Vantagens do <?php echo htmlspecialchars($productData['title']); ?></h1>
            </div>

            <?php foreach ($productAdvantages as $adv): ?>
                <div class="col-5-custom">
                    <div class="feature-card">
                        <p><?php echo nl2br(htmlspecialchars($adv['text'])); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- MOBILE CAROUSEL -->
        <div id="featuresCarousel" class="carousel slide d-lg-none" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($productAdvantages as $index => $adv): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <div class="feature-card mx-3">
                            <p><?php echo nl2br(htmlspecialchars($adv['text'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#featuresCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#featuresCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($productGalleries)): ?>
    <!-- Obras Realizadas -->
    <div class="container-xxl py-5" id="projetos">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s">
                <h1 class="mb-4">Obras realizadas com <?php echo htmlspecialchars($productData['title']); ?></h1>
            </div>

            <div class="row g-4 portfolio-container wow fadeInUp" data-wow-delay="0.5s">
                <?php foreach ($productGalleries as $gallery): ?>
                    <div class="col-lg-3 col-md-6 portfolio-item third">
                        <div class="portfolio-img overflow-hidden">
                            <img class="img-fluid" src="/<?php echo htmlspecialchars($gallery['image_path']); ?>" alt="<?php echo htmlspecialchars($gallery['title']); ?>">
                            <div class="portfolio-btn">
                                <a class="btn btn-lg-square btn-outline-light rounded-circle mx-1"
                                   href="/<?php echo htmlspecialchars($gallery['image_path']); ?>"
                                   data-lightbox="portfolio">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <div class="pt-3">
                            <p class="port mb-0"><?php echo htmlspecialchars($gallery['title']); ?></p>
                            <hr class="port w-25 my-2">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($productFaqs)): ?>
    <!-- FAQs -->
    <div class="container-xxl novo-height">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s">
                <h1 class="mb-4">Perguntas frequentes sobre <?php echo htmlspecialchars($productData['title']); ?></h1>
            </div>

            <div class="accordion" id="faqAccordion">
                <?php foreach ($productFaqs as $index => $faq): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                            <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo $index; ?>"
                                    aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                    aria-controls="collapse<?php echo $index; ?>">
                                <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $index; ?>"
                             class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>"
                             aria-labelledby="heading<?php echo $index; ?>"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include(U . 'includes/footer.php'); ?>
</body>
</html>
