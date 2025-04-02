<?php
session_start();
header('Content-Type: application/json');
echo json_encode(['authenticated' => isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true]);
?>