<?php
require_once __DIR__ . '/config.php';

/**
 * Handles a single <input type="file"> upload. Returns the public URL path
 * to store in the DB, or null if no file was submitted (keep existing value).
 */
function handle_upload(string $fieldName, ?string $existing = null): ?string {
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $existing;
    }
    $file = $_FILES[$fieldName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed with error code ' . $file['error']);
    }

    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!isset($allowed[$ext]) || mime_content_type($file['tmp_name']) !== $allowed[$ext]) {
        throw new RuntimeException('Only JPG, PNG, or WEBP images are allowed.');
    }

    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0755, true);
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = UPLOADS_DIR . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Could not save uploaded file.');
    }

    return UPLOADS_URL . '/' . $filename;
}
