<?php
// ── Copy this file to config.local.php on the server and fill in real values ──
//
//   cp config.local.example.php config.local.php
//
// Get these from cPanel → MySQL® Databases (the DB name and user are usually
// prefixed with your cPanel account name, e.g. "myacct_nsml").
// config.local.php is gitignored, so it is never overwritten by a git pull.

define('DB_HOST', 'localhost');
define('DB_NAME', 'youracct_nsml');
define('DB_USER', 'youracct_nsmluser');
define('DB_PASS', 'your-strong-password-here');

// Email address that contact-form submissions are sent to, and the From
// address used (make the From an address on your own domain to avoid spam flags).
define('CONTACT_NOTIFY_TO', 'info@yourdomain.com');
define('CONTACT_NOTIFY_FROM', 'no-reply@yourdomain.com');
