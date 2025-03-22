<?php
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

echo "Welcome to the secure webpage!";
?>
