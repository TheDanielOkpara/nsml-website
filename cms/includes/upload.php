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

/**
 * Handles a <input type="file" name="field[]" multiple> upload. Returns the
 * list of public URL paths for every file that was successfully submitted.
 */
function handle_uploads(string $fieldName): array {
    if (empty($_FILES[$fieldName]) || empty($_FILES[$fieldName]['name'][0])) {
        return [];
    }
    $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0755, true);
    }

    $paths = [];
    $count = count($_FILES[$fieldName]['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES[$fieldName]['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($_FILES[$fieldName]['error'][$i] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed with error code ' . $_FILES[$fieldName]['error'][$i]);
        }
        $tmpName = $_FILES[$fieldName]['tmp_name'][$i];
        $ext = strtolower(pathinfo($_FILES[$fieldName]['name'][$i], PATHINFO_EXTENSION));
        if (!isset($allowed[$ext]) || mime_content_type($tmpName) !== $allowed[$ext]) {
            throw new RuntimeException('Only JPG, PNG, or WEBP images are allowed.');
        }
        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = UPLOADS_DIR . '/' . $filename;
        if (!move_uploaded_file($tmpName, $dest)) {
            throw new RuntimeException('Could not save uploaded file.');
        }
        $paths[] = UPLOADS_URL . '/' . $filename;
    }
    return $paths;
}
