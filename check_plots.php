<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['dir'])) {
    echo json_encode(["hasPlots" => false]);
    exit;
}

$dir = realpath($_SERVER['DOCUMENT_ROOT'] . $_GET['dir']);
$allowed_extensions = ['root', 'png'];

if (!$dir || strpos($dir, realpath($_SERVER['DOCUMENT_ROOT'])) !== 0) {
    echo json_encode(["hasPlots" => false]);
    exit;
}

$files = scandir($dir);
foreach ($files as $file) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (in_array($ext, $allowed_extensions)) {
        echo json_encode(["hasPlots" => true]);
        exit;
    }
}

echo json_encode(["hasPlots" => false]);
?>
