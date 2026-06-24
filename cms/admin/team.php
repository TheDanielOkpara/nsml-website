<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if (isset($_GET['delete'])) {
    $stmt = db()->prepare('DELETE FROM team_members WHERE id = ?');
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: team.php?deleted=1');
    exit;
}

$rows = db()->query('SELECT * FROM team_members ORDER BY is_ceo DESC, sort_order ASC, id ASC')->fetchAll();
$pageTitle = 'Team';
$activeNav = 'team';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Team</h1>
    <p class="page-sub" style="margin:0;">People shown on the About page.</p>
  </div>
  <a href="team-edit.php" class="btn">+ New Member</a>
</div>

<?php if (!empty($_GET['saved'])): ?><div class="flash">Team member saved.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Team member deleted.</div><?php endif; ?>

<div class="panel">
<table>
  <thead><tr><th>Photo</th><th>Name</th><th>Role</th><th>CEO</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $m): ?>
    <tr>
      <td><?php if ($m['photo']): ?><img class="thumb" src="../<?= htmlspecialchars($m['photo']) ?>" alt=""><?php else: ?><div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:0.6875rem;">No photo</div><?php endif; ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars($m['name']) ?></td>
      <td><?= htmlspecialchars($m['role'] ?? '') ?></td>
      <td><span class="pill <?= $m['is_ceo'] ? 'yes' : 'no' ?>"><?= $m['is_ceo'] ? 'Yes' : 'No' ?></span></td>
      <td class="actions-cell">
        <a href="team-edit.php?id=<?= $m['id'] ?>" class="btn sm secondary">Edit</a>
        <a href="team.php?delete=<?= $m['id'] ?>" class="btn sm danger" onclick="return confirm('Delete this team member?');">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="5" class="empty-cell">No team members yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
