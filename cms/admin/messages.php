<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if (isset($_GET['delete'])) {
    $deletedId = (int) $_GET['delete'];
    db()->prepare('DELETE FROM contact_submissions WHERE id = ?')->execute([$deletedId]);
    header('Location: messages.php?deleted=1');
    exit;
}

$rows = db()->query('SELECT * FROM contact_submissions ORDER BY created_at DESC, id DESC')->fetchAll();

// Pick the open message: the one in ?id=, or the first in the list.
$selectedId = isset($_GET['id']) ? (int) $_GET['id'] : ($rows[0]['id'] ?? null);
$selected = null;
foreach ($rows as $m) {
    if ((int) $m['id'] === $selectedId) { $selected = $m; break; }
}

// Opening a message reads it, like a mail client.
if ($selected && !$selected['is_read']) {
    db()->prepare('UPDATE contact_submissions SET is_read = 1 WHERE id = ?')->execute([$selected['id']]);
    $selected['is_read'] = 1;
    foreach ($rows as &$m) { if ((int) $m['id'] === $selectedId) { $m['is_read'] = 1; } }
    unset($m);
}

$pageTitle = 'Messages';
$activeNav = 'messages';
require __DIR__ . '/layout-top.php';

function relTime(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 86400 * 7) return floor($diff / 86400) . 'd ago';
    return date('j M', strtotime($datetime));
}
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Messages</h1>
    <p class="page-sub" style="margin:0;">Submissions from the site's contact form.</p>
  </div>
</div>
<?php if (!empty($_GET['deleted'])): ?><div class="flash">Message deleted.</div><?php endif; ?>

<div class="panel mail-panel">
  <div class="mail-list">
    <?php if (!$rows): ?>
      <div class="empty-state" style="padding:3rem 1.25rem;">
        <div class="es-title">No messages yet</div>
        <div class="es-text">Contact-form submissions will appear here.</div>
      </div>
    <?php endif; ?>
    <?php foreach ($rows as $m): ?>
      <a href="messages.php?id=<?= $m['id'] ?>" class="mail-list-item <?= (int)$m['id'] === $selectedId ? 'active' : '' ?>">
        <div class="mli-top">
          <span class="mli-name"><?php if (!$m['is_read']): ?><span class="mli-dot"></span><?php endif; ?><?= htmlspecialchars(trim($m['first_name'] . ' ' . $m['last_name'])) ?: 'Anonymous' ?></span>
          <span class="mli-time"><?= relTime($m['created_at']) ?></span>
        </div>
        <div class="mli-subject"><?= htmlspecialchars($m['interest'] ?? 'General enquiry') ?></div>
        <div class="mli-snippet"><?= htmlspecialchars(mb_substr($m['message'] ?? '', 0, 80)) ?></div>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="mail-detail">
    <?php if (!$selected): ?>
      <div class="empty-state" style="padding:4rem 1.5rem; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center;">
        <div class="es-title">Select a message</div>
        <div class="es-text">Choose a message from the list to read it.</div>
      </div>
    <?php else: ?>
      <div class="mail-detail-head">
        <div>
          <h2 style="margin-bottom:0.2rem;"><?= htmlspecialchars($selected['interest'] ?? 'General enquiry') ?></h2>
          <div class="mail-from">
            <span class="mail-from-name"><?= htmlspecialchars(trim($selected['first_name'] . ' ' . $selected['last_name'])) ?: 'Anonymous' ?></span>
            &lt;<a href="mailto:<?= htmlspecialchars($selected['email']) ?>"><?= htmlspecialchars($selected['email']) ?></a>&gt;
          </div>
          <?php if ($selected['phone']): ?><div class="mail-from" style="margin-top:0.2rem;"><?= htmlspecialchars($selected['phone']) ?></div><?php endif; ?>
        </div>
        <div class="mail-detail-actions">
          <a class="btn sm secondary" href="mailto:<?= htmlspecialchars($selected['email']) ?>?subject=<?= rawurlencode('Re: ' . ($selected['interest'] ?? 'Your enquiry') . ' — NSML') ?>">Reply by email</a>
          <a class="btn sm danger" href="messages.php?delete=<?= $selected['id'] ?>" data-confirm="This will permanently delete the message. This can't be undone.">Delete</a>
        </div>
      </div>
      <div class="mail-detail-date"><?= htmlspecialchars(date('l, j F Y \a\t g:ia', strtotime($selected['created_at']))) ?></div>
      <div class="mail-body"><?= nl2br(htmlspecialchars($selected['message'] ?? '')) ?></div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
