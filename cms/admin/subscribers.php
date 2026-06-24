<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM subscribers WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: subscribers.php?deleted=1'); exit;
}

// CSV export of active subscribers.
if (isset($_GET['export'])) {
    $rows = db()->query('SELECT email, created_at FROM subscribers WHERE is_active = 1 ORDER BY created_at DESC')->fetchAll();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="subscribers.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['email', 'subscribed_at'], ',', '"', '\\');
    foreach ($rows as $r) { fputcsv($out, [$r['email'], $r['created_at']], ',', '"', '\\'); }
    fclose($out);
    exit;
}

$rows = db()->query('SELECT * FROM subscribers ORDER BY created_at DESC, id DESC')->fetchAll();
$pageTitle = 'Subscribers';
$activeNav = 'subscribers';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Newsletter Subscribers</h1>
    <p class="page-sub" style="margin:0;"><?= count($rows) ?> total subscribers.</p>
  </div>
  <a href="subscribers.php?export=1" class="btn">Export CSV</a>
</div>

<?php if (!empty($_GET['deleted'])): ?><div class="flash">Subscriber removed.</div><?php endif; ?>

<div class="panel">
<table>
  <thead><tr><th>Email</th><th>Subscribed</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $s): ?>
    <tr>
      <td style="font-weight:600;"><?= htmlspecialchars($s['email']) ?></td>
      <td style="color:var(--text-muted);"><?= htmlspecialchars(date('M j, Y g:ia', strtotime($s['created_at']))) ?></td>
      <td><span class="pill <?= $s['is_active'] ? 'yes' : 'no' ?>"><?= $s['is_active'] ? 'Active' : 'Unsubscribed' ?></span></td>
      <td class="actions-cell"><a href="subscribers.php?delete=<?= $s['id'] ?>" class="btn sm danger" onclick="return confirm('Remove this subscriber?');">Delete</a></td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="4" class="empty-cell">No subscribers yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
