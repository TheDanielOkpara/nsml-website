<?php
// Public read-only endpoint — returns the gallery image list for a property
// page so its static HTML can hydrate the photo grid via fetch().
require_once __DIR__ . '/cms/includes/db.php';

header('Content-Type: application/json');

$page = trim($_GET['page'] ?? '');
if ($page === '') {
    echo json_encode(['images' => []]);
    exit;
}

$stmt = db()->prepare('SELECT id FROM properties WHERE detail_url = ?');
$stmt->execute([$page]);
$property = $stmt->fetch();
if (!$property) {
    echo json_encode(['images' => []]);
    exit;
}

$stmt = db()->prepare('SELECT image_path FROM property_images WHERE property_id = ? ORDER BY sort_order ASC, id ASC');
$stmt->execute([$property['id']]);
$images = array_column($stmt->fetchAll(), 'image_path');

echo json_encode(['images' => $images]);
