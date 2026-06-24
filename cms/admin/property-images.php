<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

$propertyId = isset($_GET['property_id']) ? (int)$_GET['property_id'] : 0;
$stmt = db()->prepare('SELECT * FROM properties WHERE id = ?');
$stmt->execute([$propertyId]);
$property = $stmt->fetch();
if (!$property) { http_response_code(404); die('Property not found.'); }

if (isset($_GET['delete'])) {
    $stmt = db()->prepare('SELECT image_path FROM property_images WHERE id = ? AND property_id = ?');
    $stmt->execute([(int)$_GET['delete'], $propertyId]);
    $img = $stmt->fetch();
    if ($img) {
        db()->prepare('DELETE FROM property_images WHERE id = ?')->execute([(int)$_GET['delete']]);
        $path = UPLOADS_DIR . '/' . basename($img['image_path']);
        if (is_file($path)) { @unlink($path); }
    }
    header('Location: property-images.php?property_id=' . $propertyId . '&deleted=1');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'reorder') {
            foreach ($_POST['sort_order'] ?? [] as $imageId => $order) {
                db()->prepare('UPDATE property_images SET sort_order = ? WHERE id = ? AND property_id = ?')
                    ->execute([(int)$order, (int)$imageId, $propertyId]);
            }
            header('Location: property-images.php?property_id=' . $propertyId . '&reordered=1');
            exit;
        }

        $uploaded = handle_uploads('images');
        if ($uploaded) {
            $stmt = db()->prepare('SELECT COALESCE(MAX(sort_order), 0) m FROM property_images WHERE property_id = ?');
            $stmt->execute([$propertyId]);
            $maxOrder = (int) $stmt->fetch()['m'];
            foreach ($uploaded as $i => $path) {
                db()->prepare('INSERT INTO property_images (property_id, image_path, sort_order) VALUES (?, ?, ?)')
                    ->execute([$propertyId, $path, $maxOrder + $i + 1]);
            }
        }
        header('Location: property-images.php?property_id=' . $propertyId . '&saved=1');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$images = db()->prepare('SELECT * FROM property_images WHERE property_id = ? ORDER BY sort_order ASC, id ASC');
$images->execute([$propertyId]);
$images = $images->fetchAll();

$token = csrf_token();
$pageTitle = 'Gallery — ' . $property['title'];
$activeNav = 'properties';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Gallery — <?= htmlspecialchars($property['title']) ?></h1>
    <p class="page-sub" style="margin:0;">Photos shown in the gallery section of this property's individual page.</p>
  </div>
  <a href="properties-edit.php?id=<?= $propertyId ?>" class="btn secondary">Back to Property</a>
</div>

<?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if (!empty($_GET['saved'])): ?><div class="flash">Images uploaded.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Image removed.</div><?php endif; ?>
<?php if (!empty($_GET['reordered'])): ?><div class="flash">Order saved.</div><?php endif; ?>

<div class="card" style="max-width:960px;">
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
    <div class="field">
      <label>Add photos</label>
      <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required>
      <span class="hint">Select one or more JPG, PNG, or WEBP images.</span>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn">Upload</button>
    </div>
  </form>
</div>

<?php if ($images): ?>
<div class="card" style="max-width:960px; margin-top:1.5rem;">
  <form method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="action" value="reorder">
    <div class="gallery-grid">
      <?php foreach ($images as $img): ?>
        <div class="gallery-item">
          <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="">
          <div class="gallery-item-actions">
            <label style="font-size:0.75rem; color:var(--text-muted); display:flex; align-items:center; gap:0.35rem;">
              Order
              <input type="number" name="sort_order[<?= $img['id'] ?>]" value="<?= (int)$img['sort_order'] ?>">
            </label>
            <a href="property-images.php?property_id=<?= $propertyId ?>&delete=<?= $img['id'] ?>" class="btn sm danger" onclick="return confirm('Remove this image?');">Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn secondary">Save Order</button>
    </div>
  </form>
</div>
<?php else: ?>
<div class="panel" style="margin-top:1.5rem;"><div class="empty-cell">No gallery images yet — upload some above.</div></div>
<?php endif; ?>

<?php require __DIR__ . '/layout-bottom.php'; ?>
