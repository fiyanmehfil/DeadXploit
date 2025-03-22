<?php
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_input = htmlspecialchars($_GET['input']);
    echo "<h2>Your Query</h2><p>" . $user_input . "</p>";
}
?>
