<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root'; // Default username for XAMPP
$db_pass = ''; // Default password is empty
$db_name = 'Users';

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Web App</title>
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
    <h1>Welcome to Vulnerable Web Application</h1>
</header>

<div class="container">
    <h3>Login Form</h3>
    <form action="login.php" method="POST">
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
        <label for="file">Select a file to upload:</label>
        <input type="file" id="file" name="file" required><br>
        <input type="submit" value="Upload File">
    </form>

    <h3>About Us</h3>
    <p><a href="details.php">Details</a></p>
</div>

</body>
</html>

<?php
$conn->close();
?>
