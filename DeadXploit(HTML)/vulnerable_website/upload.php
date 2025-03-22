<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload_directory = 'uploads/';

    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0755, true);
    }

    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        move_uploaded_file($file['tmp_name'], $upload_directory . basename($file['name']));
        
        echo "File uploaded successfully!";
    } else {
        echo "No file was uploaded.";
    }
} else {
    echo "Invalid request method.";
}
