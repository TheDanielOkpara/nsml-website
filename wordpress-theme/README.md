# NSML WordPress theme

A custom WordPress theme for Nilayo Sports Management Ltd that reproduces
the existing static site design 1:1 (same CSS, same markup, same assets)
while making blog posts and properties editable from `wp-admin`, including
images.

## What's in here

- `nsml/` — the theme itself, **including its own bundled demo content**
  at `nsml/demo-content/`. Install by copying this whole folder into
  `wp-content/themes/nsml` on the WordPress install, then activate it under
  Appearance > Themes. Everything needed to populate the site — content
  and images — travels with the theme as a single self-contained package.
- `nsml/demo-content/manifest.php` + `nsml/demo-content/images/` — the
  data and images consumed by the **custom importer**
  (Appearance > Import Content, see below). Every image referenced by a
  post or property is a local file shipped here; importing never makes an
  HTTP request, so it can't fail because a remote host blocks the fetch.
- `nsml/demo-content/nsml-demo-content.xml` — a WXR (WordPress eXtended
  RSS) file with the same 16 articles / 10 properties, kept for reference
  and for the `tests/validate_wxr.py` check. **Prefer the custom importer
  below over Tools > Import > WordPress** — the native WXR importer fetches
  every image over HTTP from the live domain at import time, which several
  hosts block (seen in practice: every image import failed with `Forbidden
  (403)`, and the About/Services/Contact pages were never part of the WXR
  to begin with, since their content isn't reducible to one CPT). The
  custom importer fixes both problems.
- `tools/generate_wxr.py` — regenerates both `nsml-demo-content.xml` and
  `manifest.php` (+ copies the referenced images into
  `nsml/demo-content/images/`) from the static HTML in the repo root, if
  that content ever changes before go-live. Run with
  `python3 tools/generate_wxr.py` from the repo root.
- `tests/` — automated tests (see below).

## Install / go-live steps

1. On the cPanel host, install WordPress (Softaculous or manual).
2. Apply the `wp-config.php` and DB hardening in `SECURITY.md`.
3. Upload `nsml/` to `wp-content/themes/nsml`, activate it.
4. Appearance > **Import Content** — click "Import demo content now". This
   creates the 16 posts, 10 properties, and the About/Services/Contact
   pages, with every image copied directly from the theme package into the
   Media Library (no internet access required). Safe to run more than
   once — anything already present (matched by slug) is left untouched.
5. Appearance > **Theme Settings** — edit the logo, footer tagline,
   contact email/address, social links and footer credit text if they
   differ from the current static-site defaults.
6. Settings > Permalinks > Save (flushes rewrite rules for the
   `nsml_property` custom post type's `/properties/...` URLs, and for the
   `/about/`, `/services/`, `/contact/` page URLs).
7. Settings > Reading — if you want a pretty `/news/` URL for the blog
   index, create a "News" page and set it as the Posts page here.
   `front-page.php` is used for the site root regardless of this setting.

### Alternative: native WordPress importer

If you'd rather use the stock Tools > Import > WordPress flow with
`nsml-demo-content.xml`, you can, but note the caveats above (remote image
fetch can be blocked by the host; About/Services/Contact pages need to be
created by hand afterwards). If you do use it, a one-time hook
(`nsml_resolve_gallery_urls_to_ids()` in `inc/cpt-property.php`) converts
each property's imported attachment URLs into real Media Library
attachment IDs on the next `admin_init`.

## Plugins

The theme is self-contained and works fully with **zero plugins** —
including the contact form (Contact page), which is wired directly to
`wp_mail()` over a custom AJAX handler in `inc/contact-form.php`. No
Contact Form 7 / WPForms / Gravity Forms install is needed; the form
markup, validation, spam honeypot, per-IP rate limiting, and nonce
protection are all built into the theme.

The one thing a theme cannot fix on its own:

- **Install an SMTP plugin (strongly recommended) — e.g. [WP Mail
  SMTP](https://wordpress.org/plugins/wp-mail-smtp/) or [Post
  SMTP](https://wordpress.org/plugins/post-smtp/).** `wp_mail()` falls
  back to PHP's `mail()` function, which on most shared/cPanel hosts is
  unauthenticated and frequently silently dropped or marked as spam by
  the receiving mail server. An SMTP plugin routes mail through a real
  authenticated mailbox or transactional email API (Gmail, Microsoft
  365, SendGrid, Mailgun, etc.) so messages submitted through the
  contact form actually arrive. Without it, the contact form will
  validate and report success/failure correctly, but delivery is not
  guaranteed.
  - Configure it to send as the address set in Appearance > Theme
    Settings > Contact > "Contact email" — that's where contact-form
    submissions are sent.

Everything else (forms, CPTs, theme settings, demo content import,
security hardening) is custom code in this theme and needs no plugin.

## Editing content after go-live

- **Blog posts**: Posts > Add New, like any WordPress post. Featured image
  becomes the article hero. Tags become the article's tag pills.
- **Properties**: Properties > Add New. Fill in the "Property Details" meta
  box (location, hero badge, official website, organizer label, next
  edition, stats band, gallery image IDs) and set a featured image + content
  body for the "About" section.
- **Site details** (logo, tagline, contact info, social links, footer
  text): Appearance > Theme Settings.

## Tests

```bash
cd wordpress-theme
bash tests/php-lint.sh          # php -l across every theme PHP file
composer install
vendor/bin/phpunit               # sanitizer / auth-callback / template-tag unit tests
python3 tests/validate_wxr.py    # validates nsml/demo-content/nsml-demo-content.xml
```

All three currently pass. The WXR validator specifically confirms every
`<wp:attachment_url>` in the demo-content file resolves to a real, existing
image file in this repo — i.e. nothing in the "load our existing content"
import is a broken link. The same source data backs `manifest.php`, used
by the custom importer.
