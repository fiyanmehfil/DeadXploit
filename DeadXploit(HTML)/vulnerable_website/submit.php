<?php
if (isset($_GET['input'])) {
    $user_input = ($_GET['input']);
    echo "<div>$user_input</div>";
} else {
    echo "No input received.";
}
?>
