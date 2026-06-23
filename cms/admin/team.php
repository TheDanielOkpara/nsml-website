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
  <h1>Team</h1>
  <a href="team-edit.php" class="btn">+ New Member</a>
</div>

<?php if (!empty($_GET['saved'])): ?><div class="flash">Team member saved.</div><?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Team member deleted.</div><?php endif; ?>

<table>
  <thead><tr><th>Photo</th><th>Name</th><th>Role</th><th>CEO</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $m): ?>
    <tr>
      <td><?php if ($m['photo']): ?><img class="thumb" src="../<?= htmlspecialchars($m['photo']) ?>" alt=""><?php endif; ?></td>
      <td><?= htmlspecialchars($m['name']) ?></td>
      <td><?= htmlspecialchars($m['role'] ?? '') ?></td>
      <td><?= $m['is_ceo'] ? 'Yes' : '' ?></td>
      <td>
        <a href="team-edit.php?id=<?= $m['id'] ?>" class="btn secondary">Edit</a>
        <a href="team.php?delete=<?= $m['id'] ?>" class="btn danger" onclick="return confirm('Delete this team member?');">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="5">No team members yet.</td></tr><?php endif; ?>
  </tbody>
</table>

<?php require __DIR__ . '/layout-bottom.php'; ?>
