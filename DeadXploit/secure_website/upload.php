<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'index.php';

    if (!isset($_FILES['file']) || $_FILES['file']['error'] != UPLOAD_ERR_OK) {
        die("File upload error.");
    }

    // Ensure the file is safe
    $file_info = pathinfo($_FILES['file']['name']);
    if ($file_info['extension'] !== 'txt') {
        die("Invalid file type");
    }

    // Save the file safely
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
}
?>
