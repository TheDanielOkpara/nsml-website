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

<h1><?= $id ? 'Edit Post' : 'New Post' ?></h1>
<?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">

  <div class="field">
    <label>Title</label>
    <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
  </div>

  <div class="field">
    <label>Slug (URL — leave blank to auto-generate from title)</label>
    <input type="text" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" placeholder="e.g. enugu-nilayo-seal-deal">
  </div>

  <div class="field">
    <label>Cover image <?php if ($post['cover_image']): ?>(current: <a href="../<?= htmlspecialchars($post['cover_image']) ?>" target="_blank">view</a>)<?php endif; ?></label>
    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
  </div>

  <div class="field">
    <label>Excerpt (shown on the news listing card)</label>
    <textarea name="excerpt" rows="3"><?= htmlspecialchars($post['excerpt']) ?></textarea>
  </div>

  <div class="field">
    <label>Body (full article — HTML allowed, e.g. &lt;p&gt; paragraphs)</label>
    <textarea name="body" rows="14"><?= htmlspecialchars($post['body']) ?></textarea>
  </div>

  <div class="row3">
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

  <button type="submit" class="btn">Save Post</button>
  <a href="blog.php" class="btn secondary">Cancel</a>
</form>
</div>

<?php require __DIR__ . '/layout-bottom.php'; ?>
