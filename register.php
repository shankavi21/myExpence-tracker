<?php
session_start();
include __DIR__ . '/confiq.php'; // DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname   = trim($_POST['fullname']);
    $email      = trim($_POST['email']);
    $username   = trim($_POST['username']);
    $password   = trim($_POST['password']);
    $confirmpwd = trim($_POST['confirmpassword']);

    // 1. Check empty fields
    if (empty($fullname) || empty($email) || empty($username) || empty($password) || empty($confirmpwd)) {
        echo "<script>alert('All fields are required!'); window.history.back();</script>";
        exit;
    }

    // 2. Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_contains($email, ".com")) {
        echo "<script>alert('Invalid email format! Must include @ and .com'); window.history.back();</script>";
        exit;
    }

    // 3. Validate password
    $uppercase    = preg_match('@[A-Z]@', $password);
    $lowercase    = preg_match('@[a-z]@', $password);
    $number       = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
        echo "<script>alert('Password must be at least 8 characters and include uppercase, lowercase, number, and symbol!'); window.history.back();</script>";
        exit;
    }

    // 4. Confirm password match
    if ($password !== $confirmpwd) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // 5. Check duplicate username/email
    $check = $conn->prepare("SELECT id FROM register WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Username or Email already exists!'); window.history.back();</script>";
        exit;
    }
    $check->close();

    // 6. Hash password & insert
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO register (fullname, email, username, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fullname, $email, $username, $hashedPassword);

    if ($stmt->execute()) {
        // Set session so user is logged in
        $_SESSION['user_id'] = $conn->insert_id; 
        $_SESSION['username'] = $username;

        echo "<script>
            alert('Registration successful!'); 
            window.location='home.php';
        </script>";
    } else {
        echo "<script>alert('Error: Could not register user!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
