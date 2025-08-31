<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page after logout
header("Location: home.php");
exit();
?>
