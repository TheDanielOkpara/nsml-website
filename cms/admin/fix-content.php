<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

function clean_pasted_html(string $body): string {
    // Some editors paste content copied from AI chat tools or Word/Outlook,
    // which can arrive HTML-entity-encoded (e.g. "&lt;p data-start=...&gt;")
    // instead of clean markup, and/or carry junk data-* / inline style
    // attributes. Decode until stable, then strip that leftover noise.
    $prev = null;
    $i = 0;
    while ($prev !== $body && $i < 5) {
        $prev = $body;
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $i++;
    }
    $body = preg_replace('/\s+data-[a-z-]+="[^"]*"/i', '', $body);
    $body = preg_replace('/\s+style="[^"]*"/i', '', $body);
    return trim($body);
}

$posts = db()->query('SELECT id, title, body, excerpt FROM blog_posts')->fetchAll();
$affected = [];
foreach ($posts as $p) {
    $cleanedBody = clean_pasted_html($p['body'] ?? '');
    $cleanedExcerpt = clean_pasted_html($p['excerpt'] ?? '');
    if ($cleanedBody !== $p['body'] || $cleanedExcerpt !== $p['excerpt']) {
        $affected[] = [
            'id' => $p['id'], 'title' => $p['title'],
            'before' => $p['body'], 'after' => $cleanedBody,
            'excerpt_before' => $p['excerpt'], 'excerpt_after' => $cleanedExcerpt,
        ];
    }
}

// Static property pages (e.g. lagos-marathon.html) may already have real
// photos hardcoded into their gallery section from before the DB-backed
// gallery existed. Find any such photos for properties that don't have any
// property_images rows yet, so they can be imported instead of re-uploaded.
function extract_existing_gallery_images(string $detailUrl): array {
    $filename = basename($detailUrl);
    if ($filename === '' || !str_ends_with($filename, '.html')) {
        return [];
    }
    $path = __DIR__ . '/../../' . $filename;
    if (!is_file($path)) {
        return [];
    }
    $html = file_get_contents($path);
    $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
    $html = preg_replace('/<!--.*?-->/s', '', $html);
    preg_match_all('/<img\s+src="([^"]+)"[^>]*class="gallery-img"/i', $html, $m);
    return $m[1] ?? [];
}

$properties = db()->query('SELECT * FROM properties')->fetchAll();
$galleryImport = [];
foreach ($properties as $prop) {
    $countStmt = db()->prepare('SELECT COUNT(*) c FROM property_images WHERE property_id = ?');
    $countStmt->execute([$prop['id']]);
    if ((int)$countStmt->fetch()['c'] > 0) {
        continue;
    }
    $images = extract_existing_gallery_images($prop['detail_url'] ?? '');
    if ($images) {
        $galleryImport[] = ['property' => $prop, 'images' => $images];
    }
}

$applied = false;
$galleryImported = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_POST['action'] ?? '') === 'import_gallery') {
        foreach ($galleryImport as $entry) {
            foreach ($entry['images'] as $i => $path) {
                db()->prepare('INSERT INTO property_images (property_id, image_path, sort_order) VALUES (?, ?, ?)')
                    ->execute([$entry['property']['id'], $path, $i + 1]);
            }
        }
        $galleryImported = true;
    } else {
        foreach ($affected as $a) {
            db()->prepare('UPDATE blog_posts SET body = ?, excerpt = ? WHERE id = ?')->execute([$a['after'], $a['excerpt_after'], $a['id']]);
        }
        $applied = true;
    }
}

$token = csrf_token();
$pageTitle = 'Tools';
$activeNav = 'tools';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1>Content Cleanup</h1>
    <p class="page-sub" style="margin:0;">Finds and fixes blog posts where pasted content (body or excerpt) left behind raw HTML tags instead of formatted text.</p>
  </div>
</div>

<?php if ($applied): ?>
  <div class="flash">Cleaned up <?= count($affected) ?> post<?= count($affected) === 1 ? '' : 's' ?>.</div>
<?php elseif (!$affected): ?>
  <div class="flash">No issues found — every post's content looks clean.</div>
<?php else: ?>
  <div class="flash error">Found <?= count($affected) ?> post<?= count($affected) === 1 ? '' : 's' ?> with leftover raw HTML. Review below, then apply the fix.</div>

  <div class="panel" style="margin-bottom:1.5rem;">
    <table>
      <thead><tr><th>Post</th><th>Preview of the fix</th></tr></thead>
      <tbody>
      <?php foreach ($affected as $a): ?>
        <tr>
          <td style="font-weight:600; white-space:nowrap;"><a href="blog-edit.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['title']) ?></a></td>
          <td>
            <?php if ($a['after'] !== $a['before']): ?>
              <div style="font-size:0.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); margin-bottom:0.375rem;">Body</div>
              <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.25rem;">Before</div>
              <div style="font-size:0.8125rem; color:#922b21; background:#fdecea; border-radius:var(--r-sm); padding:0.5rem 0.75rem; margin-bottom:0.5rem; max-height:80px; overflow:auto; font-family:monospace;"><?= htmlspecialchars(mb_substr($a['before'], 0, 300)) ?></div>
              <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.25rem;">After</div>
              <div style="font-size:0.8125rem; color:var(--green-dark); background:var(--green-glow); border-radius:var(--r-sm); padding:0.5rem 0.75rem; max-height:80px; overflow:auto; margin-bottom:0.75rem;"><?= htmlspecialchars(mb_substr($a['after'], 0, 300)) ?></div>
            <?php endif; ?>
            <?php if ($a['excerpt_after'] !== $a['excerpt_before']): ?>
              <div style="font-size:0.6875rem; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); margin-bottom:0.375rem;">Excerpt</div>
              <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.25rem;">Before</div>
              <div style="font-size:0.8125rem; color:#922b21; background:#fdecea; border-radius:var(--r-sm); padding:0.5rem 0.75rem; margin-bottom:0.5rem; max-height:80px; overflow:auto; font-family:monospace;"><?= htmlspecialchars(mb_substr($a['excerpt_before'], 0, 300)) ?></div>
              <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.25rem;">After</div>
              <div style="font-size:0.8125rem; color:var(--green-dark); background:var(--green-glow); border-radius:var(--r-sm); padding:0.5rem 0.75rem; max-height:80px; overflow:auto;"><?= htmlspecialchars(mb_substr($a['excerpt_after'], 0, 300)) ?></div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="action" value="clean_content">
    <button type="submit" class="btn">Apply Fix to <?= count($affected) ?> Post<?= count($affected) === 1 ? '' : 's' ?></button>
  </form>
<?php endif; ?>

<div class="topbar" style="margin-top:2rem;">
  <div class="topbar-text">
    <h1>Import Existing Gallery Photos</h1>
    <p class="page-sub" style="margin:0;">Finds property pages with photos already hardcoded into their gallery section (from before the database-backed gallery existed) and imports them so they show up here.</p>
  </div>
</div>

<?php if ($galleryImported): ?>
  <div class="flash">Imported gallery photos for <?= count($galleryImport) ?> propert<?= count($galleryImport) === 1 ? 'y' : 'ies' ?>.</div>
<?php elseif (!$galleryImport): ?>
  <div class="flash">Nothing to import — every property either already has gallery photos in the database or has no existing photos on its page.</div>
<?php else: ?>
  <div class="flash error">Found <?= count($galleryImport) ?> propert<?= count($galleryImport) === 1 ? 'y' : 'ies' ?> with existing photos that aren't in the database yet.</div>

  <div class="panel" style="margin-bottom:1.5rem;">
    <table>
      <thead><tr><th>Property</th><th>Photos to import</th></tr></thead>
      <tbody>
      <?php foreach ($galleryImport as $entry): ?>
        <tr>
          <td style="font-weight:600; white-space:nowrap;"><?= htmlspecialchars($entry['property']['title']) ?></td>
          <td>
            <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
              <?php foreach ($entry['images'] as $path): ?>
                <img src="<?= htmlspecialchars(asset_url($path)) ?>" alt="" style="width:80px; height:60px; object-fit:cover; border-radius:var(--r-sm);">
              <?php endforeach; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="action" value="import_gallery">
    <button type="submit" class="btn">Import <?= array_sum(array_map(fn($e) => count($e['images']), $galleryImport)) ?> Photo<?= array_sum(array_map(fn($e) => count($e['images']), $galleryImport)) === 1 ? '' : 's' ?> for <?= count($galleryImport) ?> Propert<?= count($galleryImport) === 1 ? 'y' : 'ies' ?></button>
  </form>
<?php endif; ?>

<?php require __DIR__ . '/layout-bottom.php'; ?>
