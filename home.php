<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MyExpenseTracker</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .theme-color { background-color: #113F67; color: #fff; }
    .feature-card:hover {
      transform: translateY(-5px);
      transition: 0.3s;
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg theme-color navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-semibold text-white" href="#">MyExpenseTracker</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Logged-in Users -->
          <li class="nav-item"><a class="nav-link fw-semibold text-white" href="#features">Features</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-white" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-white" href="contact.php">Contact</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-white" href="logout.php">Logout</a></li>
        <?php else: ?>
          <!-- Guests / Not Logged-in Users -->
          <li class="nav-item"><a class="nav-link fw-semibold text-white" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold text-white" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></li>
          
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section class="hero-section text-center py-5 text-white" 
         style="background: url('h2.jpg') no-repeat center center; background-size: cover;">
  <div class="container" style="background: rgba(0,0,0,0.5); padding: 50px; border-radius: 10px;">
    <h1 class="display-4 text-white">Track your expenses, control your future!</h1>
    <p class="lead text-white">A simple and smart way to manage your daily expenses and savings.</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
      <button type="button" class="btn theme-color btn-lg" data-bs-toggle="modal" data-bs-target="#registerModal">
        Register Now
      </button>
    <?php endif; ?>
  </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5 bg-light text-dark">
  <div class="container">
    <h2 class="text-center mb-5" style="font-weight: 700; font-size: 2.5rem;">Features</h2>
    <div class="row g-4">
      <!-- Feature 1 -->
      <div class="col-md-3">
        <a href="add_expense.php" style="text-decoration:none; color:inherit;">
          <div class="card h-100 border-0 shadow-sm text-center p-4 feature-card">
            <div class="mb-3"><i class="fa-solid fa-wallet fa-2x text-primary"></i></div>
            <h5 class="card-title">Add & Categorize Expenses</h5>
            <p class="card-text">Easily add your daily expenses and organize them by category for better tracking.</p>
          </div>
        </a>
      </div>
      <!-- Feature 2 -->
      <div class="col-md-3">
        <a href="track_income_expense.php" style="text-decoration:none; color:inherit;">
          <div class="card h-100 border-0 shadow-sm text-center p-4 feature-card">
            <div class="mb-3"><i class="fa-solid fa-chart-line fa-2x text-primary"></i></div>
            <h5 class="card-title">Track Income vs Expense</h5>
            <p class="card-text">Compare your income and expenses to understand your financial balance clearly.</p>
          </div>
        </a>
      </div>
      <!-- Feature 3 -->
      <div class="col-md-3">
        <a href="monthly_report.php" style="text-decoration:none; color:inherit;">
          <div class="card h-100 border-0 shadow-sm text-center p-4 feature-card">
            <div class="mb-3"><i class="fa-solid fa-calendar-days fa-2x text-primary"></i></div>
            <h5 class="card-title">View Monthly Reports</h5>
            <p class="card-text">Get visual reports each month to analyze spending patterns and trends.</p>
          </div>
        </a>
      </div>
      <!-- Feature 4 -->
      <div class="col-md-3">
        <a href="budget.php" style="text-decoration:none; color:inherit;">
        <div class="card h-100 border-0 shadow-sm text-center p-4 feature-card">
          <div class="mb-3"><i class="fa-solid fa-bell fa-2x text-primary"></i></div>
          <h5 class="card-title">Set Budget & Get Alerts</h5>
          <p class="card-text">Set monthly budgets and receive alerts to stay on track with your finances.</p>
        </div>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- About -->
<section id="about" class="bg-light py-5">
  <div class="container text-center">
    <h2 class="mb-4" style="font-weight: 700; font-size: 2.5rem;">Why Use This App?</h2>
    <p class="lead mb-4" style="font-size: 1.25rem; color: #555;">
      Easy to use, accessible anywhere, safe & secure.
    </p>
  </div>
</section>

<!-- Footer -->
<footer class="theme-color py-4">
  <div class="container text-center">
    <p>&copy; 2025 MyExpenseTracker</p>
  </div>
</footer>

<!-- Registration Modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header theme-color">
        <h5 class="modal-title">Sign Up</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="register.php" method="POST">
          <div class="mb-3"><label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="fullname" required></div>
          <div class="mb-3"><label class="form-label">Email address</label>
            <input type="email" class="form-control" name="email" required></div>
          <div class="mb-3"><label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required></div>
          <div class="mb-3"><label class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required></div>
          <div class="mb-3"><label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" required></div>
          <div class="d-flex justify-content-between">
            <button type="submit" class="btn theme-color">Register</button>
            <button type="reset" class="btn theme-color">Clear</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header theme-color">
        <h5 class="modal-title">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="login.php" method="POST">
          <div class="mb-3"><label class="form-label">Username or Email</label>
            <input type="text" class="form-control" name="username_email" required></div>
          <div class="mb-3"><label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required></div>
          <div class="d-flex justify-content-between">
            <button type="submit" class="btn theme-color">Login</button>
            <button type="reset" class="btn theme-color">Clear</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Password match check
  document.querySelector('#registerModal form').addEventListener('submit', function (e) {
    const p = document.getElementById('password').value;
    const c = document.getElementById('confirmpassword').value;
    if (p !== c) {
      e.preventDefault();
      alert('Passwords do not match!');
    }
  });

  // Auto open login modal if redirected with ?login=1
  <?php if (isset($_GET['login']) && $_GET['login'] == 1): ?>
    var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
    myModal.show();
  <?php endif; ?>
</script>
</body>
</html>
