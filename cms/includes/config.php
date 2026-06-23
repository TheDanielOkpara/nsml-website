<?php
// ── NSML CMS configuration ───────────────────────────────────────────────
// Real credentials live in config.local.php (NOT committed to git — see
// config.local.example.php for the template). On the server, copy that file
// to config.local.php and fill in your cPanel database details. This keeps
// your password out of the public GitHub repo and lets a `git pull` redeploy
// without overwriting your settings.
//
// For local development you can instead set DB_* environment variables.

if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// Fallbacks — only define what config.local.php (or env vars) didn't already set.
defined('DB_HOST') || define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
defined('DB_NAME') || define('DB_NAME', getenv('DB_NAME') ?: 'nsml_cms');
defined('DB_USER') || define('DB_USER', getenv('DB_USER') ?: 'nsml_cms_user');
defined('DB_PASS') || define('DB_PASS', getenv('DB_PASS') ?: 'CHANGE_ME');

// Absolute path on disk to the uploads folder (must be writable by the web server).
defined('UPLOADS_DIR') || define('UPLOADS_DIR', __DIR__ . '/../uploads');
// Public URL path to reach that folder from a browser.
defined('UPLOADS_URL') || define('UPLOADS_URL', '/cms/uploads');

// Where contact-form notifications are sent. Use an address on your own domain
// (a cPanel mailbox) as the From so it isn't flagged as spoofed.
defined('CONTACT_NOTIFY_TO')   || define('CONTACT_NOTIFY_TO', getenv('CONTACT_NOTIFY_TO') ?: 'info@nilayosports.com');
defined('CONTACT_NOTIFY_FROM') || define('CONTACT_NOTIFY_FROM', getenv('CONTACT_NOTIFY_FROM') ?: 'no-reply@nilayosports.com');
