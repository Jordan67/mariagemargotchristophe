<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    die('Accès non autorisé');
}

header('Content-Type: application/json');

$uploadDir = 'uploads/';
$maxSize = 5 * 1024 * 1024;
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$response = ['success' => false];

try {
    if (empty($_FILES['photos'])) {
        throw new Exception('Aucun fichier sélectionné');
    }

    foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
        $fileSize = $_FILES['photos']['size'][$key];
        $fileType = mime_content_type($tmpName);
        $originalName = basename($_FILES['photos']['name'][$key]);
        
        if ($fileSize > $maxSize) {
            throw new Exception("Fichier trop volumineux ($originalName)");
        }
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Type de fichier non autorisé ($originalName)");
        }

        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newName = uniqid('photo_') . '_' . date('Ymd-His') . '.' . $extension;
        $destination = $uploadDir . $newName;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new Exception("Erreur lors de l'envoi du fichier");
        }
    }

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>