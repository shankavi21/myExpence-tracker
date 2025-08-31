<?php
session_start();
include __DIR__ . '/confiq.php'; // DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = trim($_POST['username_email']); // Username or Email
    $password       = trim($_POST['password']);

    // 1️⃣ Select user by username or email
    $sql = "SELECT id, username, email, password FROM register WHERE username=? OR email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // 2️⃣ Verify password
        if (password_verify($password, $row['password'])) {

            // ✅ SESSION SET for current logged-in user
            $_SESSION['user_id'] = $row['id'];       // store user id
            $_SESSION['username'] = $row['username']; // optional: store username

            // 3️⃣ Redirect to home page
            header("Location: home.php");
            exit;

        } else {
            echo "<script>alert('Invalid Password'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Invalid Username or Email'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
