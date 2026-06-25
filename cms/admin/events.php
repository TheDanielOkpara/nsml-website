<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/listing.php';
require_login();
ensure_event_banners_table();

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM event_banners WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: events.php?deleted=1');
    exit;
}
if (isset($_GET['toggle'])) {
    db()->prepare('UPDATE event_banners SET is_active = 1 - is_active WHERE id = ?')->execute([(int)$_GET['toggle']]);
    header('Location: events.php');
    exit;
}

$result = paginate_query(
    'SELECT COUNT(*) FROM event_banners %WHERE%',
    'SELECT * FROM event_banners %WHERE% ORDER BY sort_order ASC, id DESC',
    ['title', 'link_url']
);
$rows = $result['rows'];
$pageTitle = 'Next Events';
$activeNav = 'events';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Next Events</h1>
    <p class="page-sub" style="margin:0;">Banners shown in the "Next Events" section on the homepage, just above Latest News.</p>
  </div>
  <a href="events-edit.php" class="btn">+ New Banner</a>
</div>

<?php if (!empty($_GET['saved'])): ?><div class="flash">Banner saved.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Banner deleted.</div><?php endif; ?>
<?php render_search_box('Search by title or link…'); ?>

<div class="panel">
<table>
  <thead><tr><th>Banner</th><th>Title</th><th>Link</th><th>Active</th><th>Order</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $b): ?>
    <tr>
      <td><img class="thumb" src="<?= htmlspecialchars(asset_url($b['image_path'])) ?>" alt="" style="width:96px;height:44px;"></td>
      <td style="font-weight:600;"><?= htmlspecialchars($b['title'] ?: '—') ?></td>
      <td style="max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><a href="<?= htmlspecialchars($b['link_url']) ?>" target="_blank" style="color:var(--green-deep); text-decoration:none;"><?= htmlspecialchars($b['link_url']) ?></a></td>
      <td><a href="events.php?toggle=<?= $b['id'] ?>" class="pill <?= $b['is_active'] ? 'yes' : 'no' ?>" style="text-decoration:none; cursor:pointer;"><?= $b['is_active'] ? 'Active' : 'Hidden' ?></a></td>
      <td><?= (int)$b['sort_order'] ?></td>
      <td class="actions-cell">
        <a href="events-edit.php?id=<?= $b['id'] ?>" class="btn sm secondary">Edit</a>
        <a href="events.php?delete=<?= $b['id'] ?>" class="btn sm danger" data-confirm="This will permanently remove the banner. This can't be undone.">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="empty-cell"><?= $result['q'] !== '' ? 'No banners match your search.' : 'No event banners yet.' ?></td></tr><?php endif; ?>
  </tbody>
</table>
<?php render_pagination($result['page'], $result['totalPages']); ?>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
