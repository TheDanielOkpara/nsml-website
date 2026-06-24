<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$post = ['slug' => '', 'title' => '', 'excerpt' => '', 'body' => '', 'cover_image' => '', 'published_at' => date('Y-m-d'), 'is_published' => 1];
if ($id) {
    $stmt = db()->prepare('SELECT * FROM blog_posts WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { http_response_code(404); die('Post not found.'); }
    $post = $found;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']) ?: preg_replace('/[^a-z0-9-]+/', '-', strtolower($title));
        $slug = trim($slug, '-');
        $coverImage = handle_upload('cover_image', $post['cover_image']);

        $fields = [
            'slug' => $slug,
            'title' => $title,
            'excerpt' => $_POST['excerpt'],
            'body' => $_POST['body'],
            'cover_image' => $coverImage,
            'published_at' => $_POST['published_at'] ?: null,
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
        ];

        if ($id) {
            $sql = 'UPDATE blog_posts SET slug=?, title=?, excerpt=?, body=?, cover_image=?, published_at=?, is_published=? WHERE id=?';
            db()->prepare($sql)->execute([...array_values($fields), $id]);
        } else {
            $sql = 'INSERT INTO blog_posts (slug, title, excerpt, body, cover_image, published_at, is_published) VALUES (?,?,?,?,?,?,?)';
            db()->prepare($sql)->execute(array_values($fields));
        }
        header('Location: blog.php?saved=1');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
        $post = array_merge($post, $_POST);
    }
}

$token = csrf_token();
$pageTitle = $id ? 'Edit Post' : 'New Post';
$activeNav = 'blog';
require __DIR__ . '/layout-top.php';
?>

<div class="topbar">
  <div class="topbar-text">
    <h1><?= $id ? 'Edit Post' : 'New Post' ?></h1>
    <p class="page-sub" style="margin:0;">Write the article body just like in a document editor — no HTML needed.</p>
  </div>
</div>
<?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card" style="max-width:820px;">
<form method="post" enctype="multipart/form-data" id="postForm">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">

  <div class="field">
    <label>Title</label>
    <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
  </div>

  <div class="field">
    <label>Slug <span class="hint">URL — leave blank to auto-generate from title</span></label>
    <input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" placeholder="e.g. enugu-nilayo-seal-deal">
  </div>

  <div class="field">
    <label>Cover image</label>
    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
    <?php if ($post['cover_image']): ?>
      <div class="preview-thumb-wrap">
        <img class="preview-thumb preview-thumb-wide" src="<?= htmlspecialchars($post['cover_image']) ?>" alt="">
        <div class="current-file">Current: <a href="<?= htmlspecialchars($post['cover_image']) ?>" target="_blank">view full size</a></div>
      </div>
    <?php endif; ?>
  </div>

  <div class="field">
    <label>Excerpt <span class="hint">shown on the news listing card</span></label>
    <textarea name="excerpt" rows="3"><?= htmlspecialchars($post['excerpt']) ?></textarea>
  </div>

  <div class="field">
    <label>Body</label>
    <div id="bodyEditor" style="background:#fff;"><?= $post['body'] ?></div>
    <textarea name="body" id="bodyInput" style="display:none;"></textarea>
  </div>

  <div class="row2">
    <div class="field">
      <label>Published date</label>
      <input type="date" name="published_at" value="<?= htmlspecialchars($post['published_at'] ?? '') ?>">
    </div>
    <div class="field">
      <label>Status</label>
      <select name="is_published">
        <option value="1" <?= $post['is_published'] ? 'selected' : '' ?>>Published</option>
        <option value="0" <?= !$post['is_published'] ? 'selected' : '' ?>>Draft</option>
      </select>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn">Save Post</button>
    <a href="blog.php" class="btn secondary">Cancel</a>
    <?php if ($id && $post['is_published']): ?>
      <a href="https://nilayosports.com/<?= rawurlencode($post['slug']) ?>" class="btn secondary" target="_blank" style="margin-left:auto;">View &amp; Share Live Article ↗</a>
    <?php endif; ?>
  </div>
</form>
</div>

<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<style>
  #bodyEditor .ql-editor { min-height:320px; font-size:0.9375rem; line-height:1.7; font-family:inherit; }
  .ql-toolbar.ql-snow { border-color:var(--border); border-radius:var(--r-sm) var(--r-sm) 0 0; background:var(--bg); }
  .ql-container.ql-snow { border-color:var(--border); border-radius:0 0 var(--r-sm) var(--r-sm); }
</style>
<script>
  const quill = new Quill('#bodyEditor', {
    theme: 'snow',
    modules: { toolbar: [
      [{ header: [2, 3, false] }],
      ['bold', 'italic', 'underline'],
      ['blockquote'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link', 'image'],
      ['clean'],
    ] },
  });
  document.getElementById('postForm').addEventListener('submit', () => {
    document.getElementById('bodyInput').value = quill.root.innerHTML;
  });
</script>

<?php require __DIR__ . '/layout-bottom.php'; ?>
