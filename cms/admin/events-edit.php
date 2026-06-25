<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();
ensure_event_banners_table();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$b = ['image_path' => '', 'link_url' => '', 'title' => '', 'is_active' => 1, 'sort_order' => 0];
if ($id) {
    $stmt = db()->prepare('SELECT * FROM event_banners WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { http_response_code(404); die('Event banner not found.'); }
    $b = $found;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        $image = handle_upload('image', $b['image_path']);
        if (!$image) {
            throw new RuntimeException('A banner image is required.');
        }
        $linkUrl = trim($_POST['link_url'] ?? '');
        if ($linkUrl === '') {
            throw new RuntimeException('A link URL is required.');
        }
        $fields = [
            'image_path' => $image,
            'link_url' => $linkUrl,
            'title' => trim($_POST['title'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        if ($id) {
            $cols = implode(', ', array_map(fn($k) => "$k=?", array_keys($fields)));
            db()->prepare("UPDATE event_banners SET $cols WHERE id=?")->execute([...array_values($fields), $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $marks = implode(', ', array_fill(0, count($fields), '?'));
            db()->prepare("INSERT INTO event_banners ($cols) VALUES ($marks)")->execute(array_values($fields));
        }
        header('Location: events.php?saved=1');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
        $b = array_merge($b, $_POST);
    }
}

$token = csrf_token();
$pageTitle = $id ? 'Edit Event Banner' : 'New Event Banner';
$activeNav = 'events';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar"><div class="topbar-text"><h1><?= $id ? 'Edit Event Banner' : 'New Event Banner' ?></h1></div></div>
<?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">

  <div class="field">
    <label>Banner image <span class="hint">recommended ~2000×550px, JPG/PNG/WEBP</span></label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" <?= $b['image_path'] ? '' : 'required' ?>>
    <?php if ($b['image_path']): ?>
      <div class="preview-thumb-wrap">
        <img class="preview-thumb preview-thumb-wide" src="<?= htmlspecialchars(asset_url($b['image_path'])) ?>" alt="">
        <div class="current-file"><a href="<?= htmlspecialchars(asset_url($b['image_path'])) ?>" target="_blank">view full size</a></div>
      </div>
    <?php endif; ?>
  </div>

  <div class="field"><label>Link URL <span class="hint">where the banner sends visitors when clicked</span></label><input type="text" name="link_url" value="<?= htmlspecialchars($b['link_url']) ?>" placeholder="https://www.enugucitymarathon.com" required></div>

  <div class="field"><label>Internal title <span class="hint">for your reference only, not shown publicly</span></label><input type="text" name="title" value="<?= htmlspecialchars($b['title'] ?? '') ?>" placeholder="Enugu City International Marathon 2026"></div>

  <div class="row2">
    <div class="field"><label class="checkbox-field"><input type="checkbox" name="is_active" value="1" <?= $b['is_active'] ? 'checked' : '' ?>> Show on homepage</label></div>
    <div class="field"><label>Sort order <span class="hint">lower shows first</span></label><input type="number" name="sort_order" value="<?= (int)$b['sort_order'] ?>"></div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Save Banner</button>
    <a href="events.php" class="btn secondary">Cancel</a>
  </div>
</form>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
