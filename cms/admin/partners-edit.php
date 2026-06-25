<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();
ensure_partner_logos_table();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$p = ['image_path' => '', 'name' => '', 'link_url' => '', 'row_num' => 1, 'is_active' => 1, 'sort_order' => 0];
if ($id) {
    $stmt = db()->prepare('SELECT * FROM partner_logos WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { http_response_code(404); die('Partner logo not found.'); }
    $p = $found;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        $image = handle_upload('image', $p['image_path']);
        if (!$image) {
            throw new RuntimeException('A logo image is required.');
        }
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            throw new RuntimeException('A name is required (used as the image alt text).');
        }
        $fields = [
            'image_path' => $image,
            'name' => $name,
            'link_url' => trim($_POST['link_url'] ?? '') ?: null,
            'row_num' => (int)($_POST['row_num'] ?? 1) === 2 ? 2 : 1,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        if ($id) {
            $cols = implode(', ', array_map(fn($k) => "$k=?", array_keys($fields)));
            db()->prepare("UPDATE partner_logos SET $cols WHERE id=?")->execute([...array_values($fields), $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $marks = implode(', ', array_fill(0, count($fields), '?'));
            db()->prepare("INSERT INTO partner_logos ($cols) VALUES ($marks)")->execute(array_values($fields));
        }
        header('Location: partners.php?saved=1');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
        $p = array_merge($p, $_POST);
    }
}

$token = csrf_token();
$pageTitle = $id ? 'Edit Partner Logo' : 'New Partner Logo';
$activeNav = 'partners';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar"><div class="topbar-text"><h1><?= $id ? 'Edit Partner Logo' : 'New Partner Logo' ?></h1></div></div>
<?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">

  <div class="field">
    <label>Logo image <span class="hint">PNG with a transparent background works best</span></label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" <?= $p['image_path'] ? '' : 'required' ?>>
    <?php if ($p['image_path']): ?>
      <div class="preview-thumb-wrap">
        <img class="preview-thumb" src="<?= htmlspecialchars(asset_url($p['image_path'])) ?>" alt="" style="background:#f5f6f9; object-fit:contain;">
        <div class="current-file"><a href="<?= htmlspecialchars(asset_url($p['image_path'])) ?>" target="_blank">view full size</a></div>
      </div>
    <?php endif; ?>
  </div>

  <div class="field"><label>Name <span class="hint">used as the image alt text</span></label><input type="text" name="name" value="<?= htmlspecialchars($p['name']) ?>" required></div>
  <div class="field"><label>Link URL <span class="hint">optional — makes the logo clickable</span></label><input type="text" name="link_url" value="<?= htmlspecialchars($p['link_url'] ?? '') ?>" placeholder="https://example.com"></div>

  <div class="row3">
    <div class="field">
      <label>Row</label>
      <select name="row_num">
        <option value="1" <?= (int)$p['row_num'] === 1 ? 'selected' : '' ?>>Row 1</option>
        <option value="2" <?= (int)$p['row_num'] === 2 ? 'selected' : '' ?>>Row 2</option>
      </select>
    </div>
    <div class="field"><label>Sort order <span class="hint">lower shows first</span></label><input type="number" name="sort_order" value="<?= (int)$p['sort_order'] ?>"></div>
    <div class="field" style="display:flex; align-items:flex-end; padding-bottom:0.6rem;"><label class="checkbox-field"><input type="checkbox" name="is_active" value="1" <?= $p['is_active'] ? 'checked' : '' ?>> Show in marquee</label></div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Save Logo</button>
    <a href="partners.php" class="btn secondary">Cancel</a>
  </div>
</form>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
