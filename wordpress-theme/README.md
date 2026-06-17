# NSML WordPress theme

A custom WordPress theme for Nilayo Sports Management Ltd that reproduces
the existing static site design 1:1 (same CSS, same markup, same assets)
while making blog posts and properties editable from `wp-admin`, including
images.

## What's in here

- `nsml/` — the theme itself. Install by copying this folder into
  `wp-content/themes/nsml` on the WordPress install, then activate it under
  Appearance > Themes.
- `demo-content/nsml-demo-content.xml` — a WXR (WordPress eXtended RSS)
  file containing the 16 real blog articles and 10 real properties already
  on the static site, with their real images. Import it via
  Tools > Import > WordPress (the native importer) **after** activating the
  theme, so the `nsml_property` post type already exists.
- `tools/generate_wxr.py` — regenerates `demo-content/nsml-demo-content.xml`
  from the static HTML in the repo root, if that content ever changes before
  go-live. Run with `python3 tools/generate_wxr.py` from the repo root.
- `tests/` — automated tests (see below).

## Install / go-live steps

1. On the cPanel host, install WordPress (Softaculous or manual).
2. Apply the `wp-config.php` and DB hardening in `SECURITY.md`.
3. Upload `nsml/` to `wp-content/themes/nsml`, activate it.
4. Tools > Import > WordPress > upload `demo-content/nsml-demo-content.xml`.
   When prompted, assign the imported posts to an existing admin user and
   tick "Download and import file attachments" — WordPress will fetch every
   image from the live domain (`nilayosports.designthngs.com`) into the
   Media Library.
5. Visit wp-admin once after import — a one-time hook
   (`nsml_resolve_gallery_urls_to_ids()` in `inc/cpt-property.php`) converts
   each property's imported attachment URLs into real Media Library
   attachment IDs for its gallery/sponsor/event-logo fields. This runs
   automatically on the next `admin_init` and only once.
6. Settings > Permalinks > Save (flushes rewrite rules for the
   `nsml_property` custom post type's `/properties/...` URLs).
7. Create the About / Services / Contact pages as regular WordPress Pages
   (the theme's generic `page.php` renders whatever content the client adds
   via the block editor — these three pages aren't part of the demo-content
   import because their content isn't reducible to one CPT).
8. Set Settings > Reading as desired — `front-page.php` is used
   automatically for the site root regardless of that setting.

## Editing content after go-live

- **Blog posts**: Posts > Add New, like any WordPress post. Featured image
  becomes the article hero. Tags become the article's tag pills.
- **Properties**: Properties > Add New. Fill in the "Property Details" meta
  box (location, hero badge, official website, organizer label, next
  edition, stats band, gallery image IDs) and set a featured image + content
  body for the "About" section.

## Tests

```bash
cd wordpress-theme
bash tests/php-lint.sh          # php -l across every theme PHP file
composer install
vendor/bin/phpunit               # sanitizer / auth-callback / template-tag unit tests
python3 tests/validate_wxr.py    # validates demo-content/nsml-demo-content.xml
```

All three currently pass. The WXR validator specifically confirms every
`<wp:attachment_url>` in the demo-content file resolves to a real, existing
image file in this repo — i.e. nothing in the "load our existing content"
import is a broken link.
