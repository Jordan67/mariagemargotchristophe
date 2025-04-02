<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    die('Accès non autorisé');
}

header('Content-Type: application/json');

$uploadDir = 'uploads/';
$photos = [];

if (file_exists($uploadDir)) {
    $files = scandir($uploadDir);
    
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = $uploadDir . $file;
            $mimeType = mime_content_type($filePath);
            
            if (strpos($mimeType, 'image/') === 0) {
                $photos[] = $file;
            }
        }
    }
}

usort($photos, function($a, $b) use ($uploadDir) {
    return filemtime($uploadDir . $b) - filemtime($uploadDir . $a);
});

echo json_encode($photos);
?>