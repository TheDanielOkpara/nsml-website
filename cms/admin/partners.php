<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/listing.php';
require_login();
ensure_partner_logos_table();

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM partner_logos WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: partners.php?deleted=1');
    exit;
}
if (isset($_GET['toggle'])) {
    db()->prepare('UPDATE partner_logos SET is_active = 1 - is_active WHERE id = ?')->execute([(int)$_GET['toggle']]);
    header('Location: partners.php');
    exit;
}

$result = paginate_query(
    'SELECT COUNT(*) FROM partner_logos %WHERE%',
    'SELECT * FROM partner_logos %WHERE% ORDER BY row_num ASC, sort_order ASC, id ASC',
    ['name'],
    20
);
$rows = $result['rows'];
$pageTitle = 'Partner Logos';
$activeNav = 'partners';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Partner Logos</h1>
    <p class="page-sub" style="margin:0;">Logos shown in the scrolling "Trusted Partners &amp; Affiliations" marquee. Row 1 and Row 2 scroll in opposite directions.</p>
  </div>
  <a href="partners-edit.php" class="btn">+ New Logo</a>
</div>

<?php if (!empty($_GET['saved'])): ?><div class="flash">Logo saved.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Logo deleted.</div><?php endif; ?>
<?php render_search_box('Search by name…'); ?>

<div class="panel">
<table>
  <thead><tr><th>Logo</th><th>Name</th><th>Row</th><th>Active</th><th>Order</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $p): ?>
    <tr>
      <td style="background:#f5f6f9;"><img src="<?= htmlspecialchars(asset_url($p['image_path'])) ?>" alt="" style="width:96px;height:44px;object-fit:contain;display:block;"></td>
      <td style="font-weight:600;"><?= htmlspecialchars($p['name']) ?></td>
      <td>Row <?= (int)$p['row_num'] ?></td>
      <td><a href="partners.php?toggle=<?= $p['id'] ?>" class="pill <?= $p['is_active'] ? 'yes' : 'no' ?>" style="text-decoration:none; cursor:pointer;"><?= $p['is_active'] ? 'Active' : 'Hidden' ?></a></td>
      <td><?= (int)$p['sort_order'] ?></td>
      <td class="actions-cell">
        <a href="partners-edit.php?id=<?= $p['id'] ?>" class="btn sm secondary">Edit</a>
        <a href="partners.php?delete=<?= $p['id'] ?>" class="btn sm danger" data-confirm="This will permanently remove the logo from the marquee. This can't be undone.">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="empty-cell"><?= $result['q'] !== '' ? 'No logos match your search.' : 'No partner logos yet.' ?></td></tr><?php endif; ?>
  </tbody>
</table>
<?php render_pagination($result['page'], $result['totalPages']); ?>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
