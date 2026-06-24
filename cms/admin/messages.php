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

<div class="topbar">
  <div class="topbar-text">
    <h1>Contact Messages</h1>
    <p class="page-sub" style="margin:0;">Submissions from the site's contact form.</p>
  </div>
</div>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Message deleted.</div><?php endif; ?>

<div class="panel">
<table>
  <thead><tr><th>Received</th><th>Name</th><th>Email</th><th>Interest</th><th>Message</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $m): ?>
    <tr style="<?= $m['is_read'] ? '' : 'background:#fffaf0;' ?>">
      <td style="white-space:nowrap;color:var(--text-muted);"><?= htmlspecialchars(date('M j, Y g:ia', strtotime($m['created_at']))) ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars(trim($m['first_name'] . ' ' . $m['last_name'])) ?><?php if (!$m['is_read']): ?> <span class="pill unread">New</span><?php endif; ?><?= $m['phone'] ? '<br><span style="font-weight:400;color:var(--text-muted);font-size:0.8rem;">' . htmlspecialchars($m['phone']) . '</span>' : '' ?></td>
      <td><a href="mailto:<?= htmlspecialchars($m['email']) ?>" style="color:var(--green-dark);text-decoration:none;font-weight:500;"><?= htmlspecialchars($m['email']) ?></a></td>
      <td><?= htmlspecialchars($m['interest'] ?? '') ?></td>
      <td style="max-width:340px;color:var(--text-sub);"><?= nl2br(htmlspecialchars(mb_substr($m['message'] ?? '', 0, 400))) ?></td>
      <td class="actions-cell">
        <?php if (!$m['is_read']): ?><a href="messages.php?read=<?= $m['id'] ?>" class="btn sm secondary">Mark read</a><?php endif; ?>
        <a href="messages.php?delete=<?= $m['id'] ?>" class="btn sm danger" onclick="return confirm('Delete this message?');">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (!$rows): ?><tr><td colspan="6" class="empty-cell">No messages yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
