<?php
/**
 * send_file.php — Increments a per-file download counter then redirects to the
 * file in /sendfiles/. Rejects path traversal and missing files.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/base.lib.php';

$filename = $_REQUEST['FICHIER'] ?? '';

// Reject empty names and any attempt at path traversal.
if (!$filename || basename($filename) !== $filename) {
    header('Location: ' . SITE_URL . '/errs/err403.php?BaseError=' . rawurlencode($filename));
    return;
}

$filePath = absolutePath('/sendfiles/' . $filename);
if (!file_exists($filePath)) {
    header('Location: ' . SITE_URL . '/errs/err404.php?BaseError=' . rawurlencode($filename));
    return;
}

// Read the current count; reset to 1 if the counter file predates the download.
$logPath = $filePath . '.count';
if (file_exists($logPath) && filemtime($filePath) <= filemtime($logPath)) {
    $count = (int) file_get_contents($logPath) + 1;
} else {
    $count = 1;
}
file_put_contents($logPath, $count . "\n");

header('Location: ' . SITE_URL . '/sendfiles/' . $filename);
