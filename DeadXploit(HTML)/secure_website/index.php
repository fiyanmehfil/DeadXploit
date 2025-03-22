<?php
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$host = 'localhost';
$dbname = 'Users';
$user = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure PHP Web App</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-image: url('background.WEBP');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            font-family: Arial, sans-serif;
            color: white;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7); 
            padding: 20px;
            border-radius: 10px; 
        }
    </style>

</head>
<body>
<header>
    <h1>Welcome to Secure Web Application</h1>
</header>

<div class="container">
    <h3>Login Form</h3>
    <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required><br>
        <input type="submit" value="Login">
    </form>
    <h3>Submit a Query</h3>
    <form action="submit.php" method="GET">
        <label for="query">Any Queries:</label>
        <input type="text" id="query" name="input" placeholder="Enter your query" required><br>
        <input type="submit" value="Submit Query">
    </form>

    <h3>File Upload</h3>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <label for="file">Select a file to upload (only .txt files allowed):</label>
        <input type="file" id="file" name="file" required><br>
        <input type="submit" value="Upload File">
    </form>

    <h3>About Us</h3>
    <p><a href="details.php">Details</a></p>
</div>

</body>
</html>