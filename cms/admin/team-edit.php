<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$m = ['name' => '', 'role' => '', 'photo' => '', 'bio' => '', 'is_ceo' => 0, 'sort_order' => 0];
if ($id) {
    $stmt = db()->prepare('SELECT * FROM team_members WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { http_response_code(404); die('Team member not found.'); }
    $m = $found;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        $photo = handle_upload('photo', $m['photo']);
        $fields = [
            'name' => trim($_POST['name']),
            'role' => $_POST['role'],
            'photo' => $photo,
            'bio' => $_POST['bio'],
            'is_ceo' => isset($_POST['is_ceo']) ? 1 : 0,
            'sort_order' => (int)$_POST['sort_order'],
        ];
        if ($id) {
            $cols = implode(', ', array_map(fn($k) => "$k=?", array_keys($fields)));
            db()->prepare("UPDATE team_members SET $cols WHERE id=?")->execute([...array_values($fields), $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $marks = implode(', ', array_fill(0, count($fields), '?'));
            db()->prepare("INSERT INTO team_members ($cols) VALUES ($marks)")->execute(array_values($fields));
        }
        header('Location: team.php?saved=1');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
        $m = array_merge($m, $_POST);
    }
}

$token = csrf_token();
$pageTitle = $id ? 'Edit Team Member' : 'New Team Member';
$activeNav = 'team';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar"><div class="topbar-text"><h1><?= $id ? 'Edit Team Member' : 'New Team Member' ?></h1></div></div>
<?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">

  <div class="field"><label>Name</label><input type="text" name="name" value="<?= htmlspecialchars($m['name']) ?>" required></div>
  <div class="field"><label>Role</label><input type="text" name="role" value="<?= htmlspecialchars($m['role'] ?? '') ?>"></div>

  <div class="field">
    <label>Photo</label>
    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
    <?php if ($m['photo']): ?>
      <div class="preview-thumb-wrap">
        <img class="preview-thumb" src="<?= htmlspecialchars($m['photo']) ?>" alt="">
        <div class="current-file"><a href="<?= htmlspecialchars($m['photo']) ?>" target="_blank">view full size</a></div>
      </div>
    <?php endif; ?>
  </div>

  <div class="field"><label>Bio</label><textarea name="bio" rows="5"><?= htmlspecialchars($m['bio'] ?? '') ?></textarea></div>

  <div class="row2">
    <div class="field"><label class="checkbox-field"><input type="checkbox" name="is_ceo" value="1" <?= $m['is_ceo'] ? 'checked' : '' ?>> Featured as CEO card</label></div>
    <div class="field"><label>Sort order</label><input type="number" name="sort_order" value="<?= (int)$m['sort_order'] ?>"></div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Save Member</button>
    <a href="team.php" class="btn secondary">Cancel</a>
  </div>
</form>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
