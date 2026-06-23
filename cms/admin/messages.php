<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Mark one as read, or delete.
if (isset($_GET['read'])) {
    db()->prepare('UPDATE contact_submissions SET is_read = 1 WHERE id = ?')->execute([(int)$_GET['read']]);
    header('Location: messages.php'); exit;
}
if (isset($_GET['delete'])) {
    db()->prepare('DELETE FROM contact_submissions WHERE id = ?')->execute([(int)$_GET['delete']]);
    header('Location: messages.php?deleted=1'); exit;
}

$rows = db()->query('SELECT * FROM contact_submissions ORDER BY created_at DESC, id DESC')->fetchAll();
$pageTitle = 'Messages';
$activeNav = 'messages';
require __DIR__ . '/layout-top.php';
?>

<h1>Contact Messages</h1>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Message deleted.</div><?php endif; ?>

<table>
  <thead><tr><th>Received</th><th>Name</th><th>Email</th><th>Interest</th><th>Message</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $m): ?>
    <tr style="<?= $m['is_read'] ? '' : 'font-weight:600;background:#fbfdff;' ?>">
      <td><?= htmlspecialchars(date('M j, Y g:ia', strtotime($m['created_at']))) ?></td>
      <td><?= htmlspecialchars(trim($m['first_name'] . ' ' . $m['last_name'])) ?><?= $m['phone'] ? '<br><span style="font-weight:400;color:#888;font-size:0.8rem;">' . htmlspecialchars($m['phone']) . '</span>' : '' ?></td>
      <td><a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a></td>
      <td><?= htmlspecialchars($m['interest'] ?? '') ?></td>
      <td style="max-width:340px;font-weight:400;"><?= nl2br(htmlspecialchars(mb_substr($m['message'] ?? '', 0, 400))) ?></td>
      <td style="white-space:nowrap;">
        <?php if (!$m['is_read']): ?><a href="messages.php?read=<?= $m['id'] ?>" class="btn secondary">Mark read</a><?php endif; ?>
        <a href="messages.php?delete=<?= $m['id'] ?>" class="btn danger" onclick="return confirm('Delete this message?');">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6">No messages yet.</td></tr><?php endif; ?>
  </tbody>
</table>

<?php require __DIR__ . '/layout-bottom.php'; ?>
