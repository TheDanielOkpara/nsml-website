<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if (isset($_GET['delete'])) {
    $stmt = db()->prepare('DELETE FROM properties WHERE id = ?');
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: properties.php?deleted=1');
    exit;
}

$rows = db()->query('SELECT * FROM properties ORDER BY sort_order ASC, id ASC')->fetchAll();
$pageTitle = 'Properties';
$activeNav = 'properties';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Properties</h1>
    <p class="page-sub" style="margin:0;">Events and properties shown on the Properties page.</p>
  </div>
  <a href="properties-edit.php" class="btn">+ New Property</a>
</div>

<?php if (!empty($_GET['saved'])): ?><div class="flash">Property saved.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Property deleted.</div><?php endif; ?>

<div class="panel">
<table>
  <thead><tr><th>Hero</th><th>Title</th><th>Tag</th><th>Featured</th><th>Upcoming</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $p): ?>
    <tr>
      <td><?php if ($p['hero_image']): ?><img class="thumb" src="<?= htmlspecialchars(asset_url($p['hero_image'])) ?>" alt=""><?php else: ?><div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:0.6875rem;">No image</div><?php endif; ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars($p['title']) ?></td>
      <td><?= htmlspecialchars($p['tag'] ?? '') ?></td>
      <td><span class="pill <?= $p['is_featured'] ? 'yes' : 'no' ?>"><?= $p['is_featured'] ? 'Yes' : 'No' ?></span></td>
      <td><span class="pill <?= $p['is_upcoming'] ? 'yes' : 'no' ?>"><?= $p['is_upcoming'] ? 'Yes' : 'No' ?></span></td>
      <td class="actions-cell">
        <a href="property-images.php?property_id=<?= $p['id'] ?>" class="btn sm secondary">Gallery</a>
        <a href="properties-edit.php?id=<?= $p['id'] ?>" class="btn sm secondary">Edit</a>
        <a href="properties.php?delete=<?= $p['id'] ?>" class="btn sm danger" data-confirm="This will permanently delete the property and its page. This can't be undone.">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="empty-cell">No properties yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
