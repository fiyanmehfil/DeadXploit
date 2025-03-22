<?php
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token mismatch");
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        die("File upload error.");
    }

    $file_info = pathinfo($_FILES['file']['name']);
    if ($file_info['extension'] !== 'txt') {
        die("Invalid file type. Only .txt files are allowed.");
    }

    $uploads_dir = 'uploads';
    if (!file_exists($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }

    $file_path = $uploads_dir . '/' . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        echo "File uploaded successfully!";
    } else {
        echo "File upload failed!";
    }
} else {
    die("Invalid request method.");
}
?>
