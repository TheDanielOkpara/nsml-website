<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$p = [
    'slug' => '', 'title' => '', 'tag' => '', 'badge' => '', 'hero_image' => '', 'logo_image' => '',
    'description' => '', 'stat1_val' => '', 'stat1_lbl' => '', 'stat2_val' => '', 'stat2_lbl' => '',
    'stat3_val' => '', 'stat3_lbl' => '', 'detail_url' => '', 'is_featured' => 0, 'is_upcoming' => 0, 'sort_order' => 0,
];
if ($id) {
    $stmt = db()->prepare('SELECT * FROM properties WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { http_response_code(404); die('Property not found.'); }
    $p = $found;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']) ?: preg_replace('/[^a-z0-9-]+/', '-', strtolower($title));
        $slug = trim($slug, '-');
        $heroImage = handle_upload('hero_image', $p['hero_image']);
        $logoImage = handle_upload('logo_image', $p['logo_image']);

        $fields = [
            'slug' => $slug,
            'title' => $title,
            'tag' => $_POST['tag'],
            'badge' => $_POST['badge'],
            'hero_image' => $heroImage,
            'logo_image' => $logoImage,
            'description' => $_POST['description'],
            'stat1_val' => $_POST['stat1_val'], 'stat1_lbl' => $_POST['stat1_lbl'],
            'stat2_val' => $_POST['stat2_val'], 'stat2_lbl' => $_POST['stat2_lbl'],
            'stat3_val' => $_POST['stat3_val'], 'stat3_lbl' => $_POST['stat3_lbl'],
            'detail_url' => $_POST['detail_url'],
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_upcoming' => isset($_POST['is_upcoming']) ? 1 : 0,
            'sort_order' => (int)$_POST['sort_order'],
        ];

        if ($id) {
            $cols = implode(', ', array_map(fn($k) => "$k=?", array_keys($fields)));
            db()->prepare("UPDATE properties SET $cols WHERE id=?")->execute([...array_values($fields), $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $marks = implode(', ', array_fill(0, count($fields), '?'));
            db()->prepare("INSERT INTO properties ($cols) VALUES ($marks)")->execute(array_values($fields));
        }
        header('Location: properties.php?saved=1');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
        $p = array_merge($p, $_POST);
    }
}

$token = csrf_token();
$pageTitle = $id ? 'Edit Property' : 'New Property';
$activeNav = 'properties';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar"><div class="topbar-text"><h1><?= $id ? 'Edit Property' : 'New Property' ?></h1></div></div>
<?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card" style="max-width:820px;">
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">

  <div class="field"><label>Title</label><input type="text" name="title" value="<?= htmlspecialchars($p['title']) ?>" required></div>
  <div class="field"><label>Slug</label><input type="text" name="slug" value="<?= htmlspecialchars($p['slug']) ?>"></div>

  <div class="field-group">
    <div class="field-group-title">Display</div>
    <div class="row3">
      <div class="field"><label>Tag <span class="hint">e.g. "Heritage Race"</span></label><input type="text" name="tag" value="<?= htmlspecialchars($p['tag'] ?? '') ?>"></div>
      <div class="field"><label>Badge <span class="hint">optional ribbon</span></label><input type="text" name="badge" value="<?= htmlspecialchars($p['badge'] ?? '') ?>"></div>
      <div class="field"><label>Sort order</label><input type="number" name="sort_order" value="<?= (int)$p['sort_order'] ?>"></div>
    </div>
  </div>

  <div class="field-group">
    <div class="field-group-title">Media &amp; Link</div>
    <div class="row3">
      <div class="field">
        <label>Hero image</label>
        <input type="file" name="hero_image" accept="image/jpeg,image/png,image/webp">
        <?php if ($p['hero_image']): ?><div class="current-file">Current: <a href="../<?= htmlspecialchars($p['hero_image']) ?>" target="_blank">view</a></div><?php endif; ?>
      </div>
      <div class="field">
        <label>Logo image</label>
        <input type="file" name="logo_image" accept="image/jpeg,image/png,image/webp">
        <?php if ($p['logo_image']): ?><div class="current-file">Current: <a href="../<?= htmlspecialchars($p['logo_image']) ?>" target="_blank">view</a></div><?php endif; ?>
      </div>
      <div class="field"><label>Detail page link</label><input type="text" name="detail_url" value="<?= htmlspecialchars($p['detail_url'] ?? '') ?>" placeholder="e.g. ijebu-marathon.html"></div>
    </div>
  </div>

  <div class="field"><label>Description</label><textarea name="description" rows="4"><?= htmlspecialchars($p['description'] ?? '') ?></textarea></div>

  <div class="field-group">
    <div class="field-group-title">Stats</div>
    <div class="row3">
      <div class="field"><label>Stat 1 value</label><input type="text" name="stat1_val" value="<?= htmlspecialchars($p['stat1_val'] ?? '') ?>"></div>
      <div class="field"><label>Stat 1 label</label><input type="text" name="stat1_lbl" value="<?= htmlspecialchars($p['stat1_lbl'] ?? '') ?>"></div>
      <div></div>
    </div>
    <div class="row3">
      <div class="field"><label>Stat 2 value</label><input type="text" name="stat2_val" value="<?= htmlspecialchars($p['stat2_val'] ?? '') ?>"></div>
      <div class="field"><label>Stat 2 label</label><input type="text" name="stat2_lbl" value="<?= htmlspecialchars($p['stat2_lbl'] ?? '') ?>"></div>
      <div></div>
    </div>
    <div class="row3">
      <div class="field"><label>Stat 3 value</label><input type="text" name="stat3_val" value="<?= htmlspecialchars($p['stat3_val'] ?? '') ?>"></div>
      <div class="field"><label>Stat 3 label</label><input type="text" name="stat3_lbl" value="<?= htmlspecialchars($p['stat3_lbl'] ?? '') ?>"></div>
      <div></div>
    </div>
  </div>

  <div class="field">
    <label class="checkbox-field"><input type="checkbox" name="is_featured" value="1" <?= $p['is_featured'] ? 'checked' : '' ?>> Featured (wide card at top)</label>
  </div>
  <div class="field">
    <label class="checkbox-field"><input type="checkbox" name="is_upcoming" value="1" <?= $p['is_upcoming'] ? 'checked' : '' ?>> Upcoming (shows in "Coming Soon" band)</label>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Save Property</button>
    <a href="properties.php" class="btn secondary">Cancel</a>
  </div>
</form>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
