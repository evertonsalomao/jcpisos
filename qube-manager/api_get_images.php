<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

if (isset($_GET['gallery_id'])) {
    $galleryId = $_GET['gallery_id'];
    $images = $supabase->select('cms_gallery_images', 'gallery_id=eq.' . urlencode($galleryId) . '&order=image_order.asc');

    echo json_encode($images ?: []);
} else {
    echo json_encode([]);
}
