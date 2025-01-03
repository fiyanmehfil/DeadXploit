<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'index.php'; // Database connection included

    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Validate user credentials
    $query = "SELECT * FROM users WHERE username=:username AND password=:password";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if ($stmt->fetch()) {
        echo "Login successful!";
    } else {
        echo "Invalid credentials!";
    }
}
?>
