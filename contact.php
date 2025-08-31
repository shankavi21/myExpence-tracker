<?php
session_start();
include 'confiq.php'; // Database connection

// Handle form submission
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill all required fields!";
    } else {
        // Insert into database
        $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $success = "Your message has been sent successfully!";
        } else {
            $error = "Error sending message. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.theme-color { background-color: #113F67; color: #fff; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg theme-color navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-semibold text-white" href="home.php">MyExpenseTracker</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link text-white" href="home.php#features">Features</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="home.php#about">About</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
    <h2 class="mb-4">Contact Us</h2>

    <!-- Success / Error Messages -->
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Contact Form -->
    <form method="POST" class="mb-5">
        <div class="mb-3">
            <label class="form-label">Name *</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" class="form-control" name="subject">
        </div>
        <div class="mb-3">
            <label class="form-label">Message *</label>
            <textarea class="form-control" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn theme-color text-white">Send Message</button>
        <a href="home.php" class="btn btn-secondary">Back to Home</a>
    </form>

    <!-- Optional Contact Info -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Email</h5>
                <p>myexpensetracker@gmail.com</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Phone</h5>
                <p>+94 123 456 789</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Address</h5>
                <p>Jaffna, Sri Lanka.</p>
            </div>
        </div>
    </div>
</div>
<!-- Footer -->
<footer class="theme-color py-4">
  <div class="container text-center">
    <p>&copy; 2025 MyExpenseTracker</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
