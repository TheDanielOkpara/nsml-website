<?php
// Database credentials — fill these in with your cPanel MySQL details.
// cPanel > MySQL Databases: create a DB, a user, and assign the user to the DB.
// Env vars (DB_HOST/DB_NAME/DB_USER/DB_PASS) override the defaults below — handy for local testing.
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'nsml_cms');
define('DB_USER', getenv('DB_USER') ?: 'nsml_cms_user');
define('DB_PASS', getenv('DB_PASS') ?: 'CHANGE_ME');

// Absolute path on disk to the uploads folder (must be writable by the web server).
define('UPLOADS_DIR', __DIR__ . '/../uploads');
// Public URL path to reach that folder from a browser.
define('UPLOADS_URL', '/cms/uploads');
