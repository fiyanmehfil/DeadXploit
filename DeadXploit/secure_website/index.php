<?php
session_start();
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Database connection
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

// CSRF protection check
function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'])) {
        die("CSRF token mismatch");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure PHP Web App</title>
    <link rel="stylesheet" href="style.css">
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
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>

    <h3>Submit a Query</h3>
    <form action="submit.php" method="GET">
        <label for="query">Any Queries:</label>
        <input type="text" id="query" name="input" required><br>
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
