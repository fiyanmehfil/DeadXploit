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


    $host = 'localhost';
    $dbname = 'Users';
    $user = 'root';
    $password = '';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

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
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>
