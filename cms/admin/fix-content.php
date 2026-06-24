<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

function clean_pasted_html(string $body): string {
    // Some editors paste content copied from AI chat tools, which can
    // arrive HTML-entity-encoded (e.g. "&lt;p data-start=...&gt;") instead
    // of clean markup. Decode until stable, then strip the leftover
    // data-* attributes those tools attach to each tag.
    $prev = null;
    $i = 0;
    while ($prev !== $body && $i < 5) {
        $prev = $body;
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $i++;
    }
    $body = preg_replace('/\s+data-[a-z-]+="[^"]*"/i', '', $body);
    return trim($body);
}

$posts = db()->query('SELECT id, title, body FROM blog_posts')->fetchAll();
$affected = [];
foreach ($posts as $p) {
    $cleaned = clean_pasted_html($p['body'] ?? '');
    if ($cleaned !== $p['body']) {
        $affected[] = ['id' => $p['id'], 'title' => $p['title'], 'before' => $p['body'], 'after' => $cleaned];
    }
}

$applied = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($affected as $a) {
        db()->prepare('UPDATE blog_posts SET body = ? WHERE id = ?')->execute([$a['after'], $a['id']]);
    }
    $applied = true;
}

$token = csrf_token();
$pageTitle = 'Tools';
$activeNav = 'tools';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Content Cleanup</h1>
    <p class="page-sub" style="margin:0;">Finds and fixes blog posts where pasted content left behind raw HTML tags instead of formatted text.</p>
  </div>
</div>

<?php if ($applied): ?>
  <div class="flash">Cleaned up <?= count($affected) ?> post<?= count($affected) === 1 ? '' : 's' ?>.</div>
<?php elseif (!$affected): ?>
  <div class="flash">No issues found — every post's content looks clean.</div>
<?php else: ?>
  <div class="flash error">Found <?= count($affected) ?> post<?= count($affected) === 1 ? '' : 's' ?> with leftover raw HTML in the body. Review below, then apply the fix.</div>

  <div class="panel" style="margin-bottom:1.5rem;">
    <table>
      <thead><tr><th>Post</th><th>Preview of the fix</th></tr></thead>
      <tbody>
      <?php foreach ($affected as $a): ?>
        <tr>
          <td style="font-weight:600; white-space:nowrap;"><a href="blog-edit.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['title']) ?></a></td>
          <td>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.25rem;">Before</div>
            <div style="font-size:0.8125rem; color:#922b21; background:#fdecea; border-radius:var(--r-sm); padding:0.5rem 0.75rem; margin-bottom:0.5rem; max-height:80px; overflow:auto; font-family:monospace;"><?= htmlspecialchars(mb_substr($a['before'], 0, 300)) ?></div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.25rem;">After</div>
            <div style="font-size:0.8125rem; color:var(--green-dark); background:var(--green-glow); border-radius:var(--r-sm); padding:0.5rem 0.75rem; max-height:80px; overflow:auto;"><?= htmlspecialchars(mb_substr($a['after'], 0, 300)) ?></div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
    <button type="submit" class="btn">Apply Fix to <?= count($affected) ?> Post<?= count($affected) === 1 ? '' : 's' ?></button>
  </form>
<?php endif; ?>

<?php require __DIR__ . '/layout-bottom.php'; ?>
