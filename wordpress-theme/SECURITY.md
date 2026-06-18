# Security notes for the NSML WordPress theme

Most hardening is implemented in the theme itself (`inc/security.php`,
`inc/cpt-property.php`) and applies automatically once the theme is active —
see those files for what's enforced in code (no XML-RPC, no REST user
enumeration, no `?author=N` enumeration, security headers, comments
disabled, strict `sanitize_callback`/`auth_callback` on every custom field,
nonce + capability checks on every meta save).

A few things a theme cannot enforce at runtime and must be configured at the
hosting/`wp-config.php` level. Apply these once the site is on cPanel:

## wp-config.php

```php
// Block the in-dashboard file editor (Appearance > Theme File Editor, Plugin Editor).
define( 'DISALLOW_FILE_EDIT', true );

// Block plugin/theme installs and updates from the dashboard if you want
// all deploys to go through a reviewed process instead.
// define( 'DISALLOW_FILE_MODS', true );

// Force SSL for admin/login.
define( 'FORCE_SSL_ADMIN', true );

// Unique authentication keys/salts — generate fresh ones for production,
// never reuse the placeholder values from the default wp-config-sample.php.
// https://api.wordpress.org/secret-key/1.1/salt/
```

## Database / credentials

- Use a dedicated MySQL user for this site only, with privileges limited to
  the one database (no `GRANT ALL ON *.*`).
- Use a unique, non-default table prefix (not `wp_`) when running the
  installer.
- Store DB credentials only in `wp-config.php`, never commit them to git.

## File permissions (cPanel)

- Directories: `755`. Files: `644`. `wp-config.php`: `600` if the hosting
  user/group setup allows it without breaking PHP-FPM access.
- `wp-content/uploads/` should not be executable — most managed cPanel
  hosts block PHP execution there by default; confirm with hosting support
  if unsure, and add an `.htaccess` with `php_flag engine off` inside
  `uploads/` if not.

## Updates

- Keep WordPress core, and any installed plugins, on auto-update for minor/
  security releases. The theme itself has no required plugin dependencies —
  see README.md's "Plugins" section for the one strongly recommended
  install (an SMTP plugin, for contact-form mail deliverability).

## Backups

- Take a full database + `wp-content` (especially `uploads/`) backup before
  running Appearance > Import Content (or importing
  `nsml/demo-content/nsml-demo-content.xml`), and before any major
  WordPress core update.
