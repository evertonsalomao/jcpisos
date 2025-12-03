<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>JC Pisos Intertravados de Concreto Sorocaba SP - Quem Somos</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

   <?php
        define('U', '');
        define('MENU', 0);

        require_once 'qube-manager/config/database.php';

        $database = new Database();
        $db = $database->getConnection();

        $stmt = $db->prepare("SELECT * FROM qube_categories ORDER BY order_index ASC, name ASC");
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT g.*, c.slug as category_slug FROM qube_galleries g LEFT JOIN qube_categories c ON g.category_id = c.id WHERE g.published = 1 ORDER BY g.created_at DESC");
        $stmt->execute();
        $galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categoriesMap = [];
        if ($categories) {
            foreach ($categories as $category) {
                $categoriesMap[$category['id']] = $category;
            }
        }

        include(U . 'includes/header-interna.php');
    ?>
  

    <!-- Page Header Start -->
    <div class="container-fluid breadcrumb-novo">
      
           
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="" href="#">Home</a></li>
                    <li class="breadcrumb-item  active" aria-current="page">Obras</li>
                </ol>
            </nav>
    
    </div>
    <!-- Page Header End -->

    <!-- Hero Section -->
    <section class="fundo-verde">
        <div class="container">
            <div class="row ">

                  <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s">
               
                <h1 class="mb-4" style="color: #fff"> Obras com pisos intertravados da JC Pisos Intertravados</h1>
                 
            </div>
               
           
            </div>
        </div>
    </section>

      <!-- Projects Start -->
    <div class="container-xxl py-5" id="projetos">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" >
                
               
                <h6 class="text-primary">Seleção de projetos residenciais, comerciais e industriais que mostram a qualidade, resistência e estética dos nossos pisos intertravados.</h6>
            </div>

            <div class="row mt-n2 wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-12 text-center">
                    <ul class="list-inline mb-5" id="portfolio-flters">
                        <li class="mx-2 active" data-filter="*">Todos</li>
                        <?php if ($categories): ?>
                            <?php foreach ($categories as $category): ?>
                                <li class="mx-2" data-filter=".cat-<?php echo htmlspecialchars($category['slug']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="row g-4 portfolio-container wow fadeInUp" data-wow-delay="0.5s">
                <?php if ($galleries && count($galleries) > 0): ?>
                    <?php foreach ($galleries as $gallery): ?>
                        <?php
                            $categorySlug = $gallery['category_slug'] ?? '';
                            $stmtImages = $db->prepare("SELECT * FROM qube_gallery_images WHERE gallery_id = :gallery_id ORDER BY order_index ASC");
                            $stmtImages->bindParam(':gallery_id', $gallery['id']);
                            $stmtImages->execute();
                            $galleryImages = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <div class="col-lg-3 col-md-6 portfolio-item cat-<?php echo htmlspecialchars($categorySlug); ?>" data-gallery-id="<?php echo htmlspecialchars($gallery['id']); ?>">
                            <div class="portfolio-img overflow-hidden">
                                <img class="img-fluid" src="<?php echo htmlspecialchars($gallery['featured_image']); ?>" alt="<?php echo htmlspecialchars($gallery['title']); ?>">
                                <div class="portfolio-btn">
                                    <a class="btn btn-lg-square btn-outline-light rounded-circle mx-1"
                                       href="<?php echo htmlspecialchars($gallery['featured_image']); ?>"
                                       data-lightbox="gallery-<?php echo htmlspecialchars($gallery['id']); ?>">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="pt-3">
                                <p class="port mb-0"><?php echo htmlspecialchars($gallery['title']); ?></p>
                                <hr class="port w-25 my-2">
                            </div>

                            <?php if ($galleryImages && count($galleryImages) > 0): ?>
                                <?php foreach ($galleryImages as $image): ?>
                                    <a href="<?php echo htmlspecialchars($image['image_path']); ?>"
                                       data-lightbox="gallery-<?php echo htmlspecialchars($gallery['id']); ?>"
                                       style="display:none;"></a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">Nenhuma obra cadastrada no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Projects End -->
<?php   include(U . 'includes/footer.php');?>