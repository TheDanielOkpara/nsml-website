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
  <h1>Properties</h1>
  <a href="properties-edit.php" class="btn">+ New Property</a>
</div>

<?php if (!empty($_GET['saved'])): ?><div class="flash">Property saved.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Property deleted.</div><?php endif; ?>

<table>
  <thead><tr><th>Hero</th><th>Title</th><th>Tag</th><th>Featured</th><th>Upcoming</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $p): ?>
    <tr>
      <td><?php if ($p['hero_image']): ?><img class="thumb" src="../<?= htmlspecialchars($p['hero_image']) ?>" alt=""><?php endif; ?></td>
      <td><?= htmlspecialchars($p['title']) ?></td>
      <td><?= htmlspecialchars($p['tag'] ?? '') ?></td>
      <td><?= $p['is_featured'] ? 'Yes' : '' ?></td>
      <td><?= $p['is_upcoming'] ? 'Yes' : '' ?></td>
      <td>
        <a href="properties-edit.php?id=<?= $p['id'] ?>" class="btn secondary">Edit</a>
        <a href="properties.php?delete=<?= $p['id'] ?>" class="btn danger" onclick="return confirm('Delete this property?');">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6">No properties yet.</td></tr><?php endif; ?>
  </tbody>
</table>

<?php require __DIR__ . '/layout-bottom.php'; ?>
