<?php
$host = "localhost";   // usually localhost
$user = "root";        // default user in XAMPP/WAMP
$pass = "";            // keep empty if no password
$db   = "project";     // your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
